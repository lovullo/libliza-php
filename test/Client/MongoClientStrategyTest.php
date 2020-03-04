<?php
/**
 * Tests MongoClient strategy
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

namespace Lovullo\Liza\Tests\Client;

use Lovullo\Liza\Client\MongoClientStrategy as Sut;
use Lovullo\Liza\Dao\Dao;

class MongoClientStrategyTest
    extends ClientStrategyTestCase
{


    protected function createSut()
    {
        $mongo = $this->_mockMongo();
        $dao = $this->_mockDao( $mongo );

        return new Sut( $dao );
    }


    private function _mockQuotes()
    {
        return $this->getMockBuilder( \StdClass::class )
            ->setMethods( [ 'updateOne' ] )
            ->getMock();
    }


    private function _mockProgram( $quotes = null )
    {
        $program = $this->getMockBuilder( \StdClass::class )->getMock();
        $program->quotes = ( !empty( $quotes ) ) ? $quotes : $this->_mockQuotes();

        return $program;
    }


    private function _mockMongo( $program = null )
    {
        $mongo = $this->getMockBuilder( \MongoDb::class )
            ->disableOriginalConstructor()
            ->getMock();

        $mongo->program = ( !empty( $program ) ) ? $program : $this->_mockProgram();

        return $mongo;
    }


    private function _mockDao( $mongo )
    {
        return $this->getMockBuilder( Dao::class )
            ->setConstructorArgs( [ $mongo ] )
            ->setMethods( [ 'update' ] )
            ->getMock();
    }


    public function testCreateSut()
    {
        $sut = $this->createSut();
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


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetDocumentDataReturnsAnArray()
    {
        $this->createSut()->getDocumentData( 0 );
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetDocumentDataContainsDocumentId()
    {
        $this->createSut()->getDocumentData( 0 );
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetDocumentDataContainsKeyValueStoreData()
    {
        $this->createSut()->getDocumentData( 0 );
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataReturnsAnArray()
    {
        $this->createSut()->getProgramData( 0 );
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataContainsDocumentId()
    {
        $this->createSut()->getProgramData( 0 );
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataContainsKeyValueStoreData()
    {
        $this->createSut()->getProgramData( 0 );
    }


    public function testItSendsDataToBucket()
    {
        $quote   = $this->_mockQuotes();
        $program = $this->_mockProgram( $quote );
        $mongo   = $this->_mockMongo( $program );
        $dao     = $this->_mockDao( $mongo );
        $sut     = new Sut( $dao );

        $id = '12345';
        $data = [
            'name' => 'john'
        ];

        $expected = [
            'id' => $id,
            'data' => $data
        ];

        $dao->expects( $this->once() )
            ->method( 'update' )
            ->with( $id, $data )
            ->willReturn( $expected );

        $actual = $sut->sendBucketData( $id, $data );

        $this->assertEquals( $expected, json_decode( $actual, true ) );
    }
}
