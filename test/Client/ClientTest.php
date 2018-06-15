<?php
/**
 * Tests Liza client
 *
 *  Copyright (C) 2016 LoVullo Associates, Inc.
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

namespace Lovullo\Liza\Tests\Client;

use Lovullo\Liza\Client\Client as Sut;


class ClientTest
    extends \PHPUnit_Framework_TestCase
{
    protected function createSut( $strategy, $doc_factory, $bucket_factory )
    {
        return new Sut( $strategy, $doc_factory, $bucket_factory );
    }


    protected function createMockStrategy()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Client\ClientStrategy'
        )
            ->getMock();
    }


    protected function createMockDocFactory()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Document\DocumentFactory'
        )
            ->setMethods( array( 'createDocument' ) )
            ->getMock();
    }


    protected function createMockBucketFactory()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\BucketFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods( array( 'fromData' ) )
            ->getMock();
    }


    protected function createDummyDocument()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Document\Document'
        )
            ->disableOriginalConstructor()
            ->getMock();
    }


    protected function createDummyBucket()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\Bucket'
        )
            ->disableOriginalConstructor()
            ->getMock();
    }


    protected function getDummyData()
    {
        // valid data
        return [
            'id'   => 0,
            'content' => [
                'data' => [],
            ]
        ];
    }


    public function testRetrievesDocumentDataForGivenId()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_request_id = 'REQUESTID';
        $doc_id         = 'FOO123';
        $bucket_data    = array( 'bucket' => 'data' );
        $document       = $this->createDummyDocument();
        $bucket         = $this->createDummyBucket();

        $doc_data = [
            'id'      => $doc_id,
            'content' => [
                'data' => $bucket_data,
            ],
        ];

        $mock_strategy
            ->expects( $this->once() )
            ->method( 'getDocumentData' )
            ->with( $doc_request_id )
            ->willReturn( $doc_data );

        $mock_bucket_factory
            ->expects( $this->once() )
            ->method( 'fromData' )
            ->with( $bucket_data )
            ->willReturn( $bucket );

        $mock_doc_factory
            ->expects( $this->once() )
            ->method( 'createDocument' )
            ->with( $doc_id, $bucket )
            ->willReturn( $document );

        // this ensures that the document id that is actually used in the
        // end is the one returned by the server, not the one we provide
        $this->assertSame(
            $document,
            $sut->getDocument( $doc_request_id )
        );
    }


    /**
     * @expectedException Lovullo\Liza\Client\BadClientDataException
     * @expectedExceptionMessageRegExp /missing bucket data/
     */
    public function testFailsIfBucketDataNotProvided()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'content' ] = [];

        $mock_strategy
            ->method( 'getDocumentData' )
            ->willReturn( $dummy_data );

        $sut->getDocument( 0 );
    }


    /**
     * @expectedException Lovullo\Liza\Client\BadClientDataException
     * @expectedExceptionMessageRegExp /missing content/
     */
    public function testFailsIfDocumentContentsNotProvided()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        // missing 'content'
        $document = [ 'id' => '' ];

        $mock_strategy
            ->method( 'getDocumentData' )
            ->willReturn( $document );

        $sut->getDocument( 0 );
    }


    /**
     * @expectedException Lovullo\Liza\Client\BadClientDataException
     */
    public function testFailsIfBucketDataNotAnArray()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'data' ] = 'nonarray';

        $mock_strategy
            ->method( 'getDocumentData' )
            ->willReturn( $dummy_data );

        $sut->getDocument( 0 );
    }


    public function testRetrievesProgramDataForGivenId()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_id = 'FOO123';

        $expected = [ 'foo' => 'bar' ];

        $doc_data = [
            'error' => null,
            'data' => $expected,
        ];

        $mock_strategy
            ->expects( $this->once() )
            ->method( 'getProgramData' )
            ->with( $doc_id )
            ->willReturn( $doc_data );

        $given = $sut->getProgramData( $doc_id );

        $this->assertEquals( $expected, $given );
    }


    /**
     * @expectedException Lovullo\Liza\Client\BadClientDataException
     */
    public function testFailsIfProgramDataNotAnArray()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_id = '345678';

        $mock_strategy
            ->expects( $this->once() )
            ->method( 'getProgramData' )
            ->with( $doc_id )
            ->willReturn( '' );

        $sut->getProgramData( $doc_id );
    }
}
