<?php
/**
 * Generalized key/value store
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

namespace Lovullo\Liza\Tests\Bucket;

use Lovullo\Liza\Bucket\Bucket;


abstract class BucketTestCase
    extends \PHPUnit_Framework_TestCase
{
    private $_initialData = array(
        '_foo'    => array( 'foo', 'bar', 'baz' ),
        '_err'    => '5',
        '_badval' => array( 5 ),
    );


    /**
     * Return the bucket instance acting as the SUT
     *
     * Be sure to populate the bucket with the initial data passed to this
     * method, otherwise certain assertions will fail.
     *
     * @param array $initial_data initial data to populate bucket with
     *
     * @return Bucket
     */
    abstract protected function getSut( $initial_data );


    /**
     * Ensure that the developer did not forget to implement the interface
     */
    public function testIsABucket()
    {
        $this->assertTrue( $this->getSut( array() ) instanceof Bucket,
            'Does not implement interface Bucket'
        );
    }


    /**
     * The bucket data should be retrievable by the name of its key, so long as
     * it exists.
     */
    public function testCanRetrieveBucketDataByName()
    {
        $key = '_foo';

        $this->assertEquals(
            $this->_initialData[ $key ],
            $this->getSut( $this->_initialData )->getDataByName( $key ),
            'Bucket should return the value identified by a given name'
        );
    }


    /**
     * PHP < 5.4's lack of array dereferencing support can be rather
     * frustrating.  As such, getDataByName() should also accept an index to
     * return the value of.
     */
    public function testCanRetrieveDataByNameAndIndex()
    {
        $sut   = $this->getSut( $this->_initialData );
        $key   = '_foo';
        $index = 2;

        $this->assertEquals(
            $this->_initialData[ $key ][ $index ],
            $sut->getDataByName( $key, $index ),
            'Bucket should return the value identified by a name and index'
        );
    }


    /**
     * The absense of a key should not be indicitive of an error, since the
     * data within the bucket is highly dynamic and highly dependent on user
     * input.  In fact, it may be completely normal that a value does not
     * exist.
     *
     * @depends testCanRetrieveBucketDataByName
     */
    public function testRetrievingNonexistantDataByNameYieldsEmptyArray()
    {
        $sut = $this->getSut( $this->_initialData );

        $this->assertEquals(
            array(),
            $sut->getDataByName( '_surelythiskeydoesnotexist' ),
            'getDataByName() should yield an empty array if data is unset'
        );
    }


    public function hasDataProvider()
    {
        // key, index, key exists, index exists
        return array(
            array( '_foo', 0, true, true, ),
            array( '_foo', 1, true, true ),
            array( '_foo', 2, true, true ),
            array( '_foo', 3, true, false ),
            array( '_000', 0, false, false ),
        );
    }


    /**
     * @dataProvider hasDataProvider
     */
    public function testCanCheckIfKeyExists( $name, $_, $expected )
    {
        $this->assertEquals( $expected,
            $this->getSut( $this->_initialData )->hasData( $name )
        );
    }


    /**
     * @dataProvider hasDataProvider
     * @depends testCanCheckIfKeyExists
     */
    public function testCanCheckIfIndexExists( $name, $index, $_, $expected )
    {
        $this->assertEquals( $expected,
            $this->getSut( $this->_initialData )->hasData( $name, $index )
        );
    }


    public function valueLengthProviderProvider()
    {
        // data, requested index, success
        return array(
            array( array(),           0, false ),
            array( array( 'one' ),    0, true ),
            array( array( 'one' ),    1, false ),
            array( array( '1', '2' ), 1, true ),
            array( array( '1', '2' ), 0, true ),
        );
    }


    /**
     * Considering the previous test, one should also understand that, under
     * certain circumstances, a key may very well be required. Consider
     * rating, for example. If a certain key does not exist
     * (e.g. applicant_name), surely that is indicative of an
     * error. Therefore, if a specific index is requested and does not
     * exist, throw an exception.
     *
     * @depends testCanRetrieveBucketDataByName
     * @dataProvider valueLengthProviderProvider
     */
    public function testWillThrowErrorIfRequestedIndexDoesNotExist(
        $data, $index, $succeed
    )
    {
        $key = '_bar';

        if ( $succeed === false )
        {
            $this->setExpectedException( 'DomainException' );
        }

        // perform the operation, which may or may not throw an exception
        $this->getSut( array( $key => $data ) )
            ->getDataByName( $key, $index );
    }
}
