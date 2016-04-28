<?php
/**
 * Tests RESTful client strategy
 *
 *  Copyright (C) 2016 Lovullo Associates, Inc.
 *
 *  This file is part of libliza-php.
 *
 *  libliza-php is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace
{
    $file_get_contents_ret = '';
    $file_get_contents_url = '';
}

// for mocking our REST calls until we have a proper abstraction
namespace Lovullo\Liza\Client
{
    function file_get_contents( $url )
    {
        global $file_get_contents_url,
            $file_get_contents_ret;

        $file_get_contents_url = $url;
        return $file_get_contents_ret;
    }
}


namespace Lovullo\Liza\Tests\Client
{

use Lovullo\Liza\Client\RestClientStrategy as Sut;


class RestClientStrategyTest
    extends ClientStrategyTestCase
{
protected function createSut()
    {
        $sut = $this->createPlainSut( 'https://base' );

        // valid data for test case supertype
        $sut->method( 'queryDocument' )
            ->willReturn( $this->getDummyData() );

        return $sut;
    }


    /**
     * Create SUT without setting up default mocks
     */
    protected function createPlainSut( $base_url )
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Client\RestClientStrategy'
        )
            ->setConstructorArgs( array( $base_url ) )
            ->setMethods( array( 'queryDocument' ) )
            ->getMock();
    }


    /**
     * Just enough data to be acceptable
     */
    protected function getDummyData()
    {
        return array(
            'id'   => 0,
            'data' => array(),
        );
    }


    public function testQueryMethodIsGivenProperBaseUrl()
    {
        $url = 'https://foo.bar/baz/';
        $sut = $this->createPlainSut( $url );

        $sut->expects( $this->once() )
            ->method( 'queryDocument' )
            ->with( $url, $this->anything() )
            ->willReturn( $this->getDummyData() );

        $sut->getDocumentData( 0 );
    }


    public function testQueryMethodIsGivenProperDocId()
    {
        $sut = $this->createPlainSut( 'base' );
        $id  = 'FOOBAR';

        $sut->expects( $this->once() )
            ->method( 'queryDocument' )
            ->with( $this->anything(), $id )
            ->willReturn( $this->getDummyData() );

        $sut->getDocumentData( $id );
    }


    /**
     * @depends testQueryMethodIsGivenProperDocId
     */
    public function testMapsQuoteIdFieldToGenericId()
    {
        $sut = $this->createPlainSut( 'base' );
        $id  = 'ABCFOO';

        $dummy_data = $this->getDummyData();

        $dummy_data[ 'quoteId' ] = $id;
        unset( $dummy_data[ 'id' ] );

        $sut->method( 'queryDocument' )
            ->willReturn( $dummy_data );

        // make sure it doesn't just use the id we provided, rather than the
        // id in the response
        $given = $sut->getDocumentData( 'nonsenseidfortesting' );

        $this->assertEquals( $id, $given[ 'id' ] );
        $this->assertArrayNotHasKey( 'quoteId', $given );
    }


    public function testReturnsAllDocumentData()
    {
        $sut        = $this->createPlainSut( 'base' );
        $dummy_data = $this->getDummyData();

        // key that is not likely to exist
        $expected            = 'mooooo';
        $dummy_data[ 'cow' ] = $expected;

        $sut->method( 'queryDocument' )
            ->willReturn( $dummy_data );

        $this->assertEquals(
            $dummy_data,
            $sut->getDocumentData( 0 )
        );
    }


    public function testDocumentRequestsBaseUrlWithIdAndInit()
    {
        global $file_get_contents_url,
            $file_get_contents_ret;

        $url = 'https://foo/document/';
        $id  = 'FOO123';

        // no mocking at all now
        $sut = new Sut( $url );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'worked' ] = 'ok';

        // the test will fail unless this JSON-decodes into an array
        $file_get_contents_ret = json_encode( $dummy_data );

        // see mock file_get_contents at top of this file
        $result = $sut->getDocumentData( $id );

        $this->assertEquals(
            $url . $id . '/init',
            $file_get_contents_url
        );

        $this->assertArrayHasKey( 'worked', $result );
    }
}
}
