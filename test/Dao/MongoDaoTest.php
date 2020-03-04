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

use Lovullo\Liza\Dao\MongoDao as Sut;
use MongoDb;
use MongoDB\UpdateResult;

class MongoDaoTest
    extends \PHPUnit_Framework_TestCase
{


    protected function createSut( $mongo_client )
    {
        return new Sut( $mongo_client );
    }


    private function _mockMongoResult()
    {
        return $this->getMockBuilder( UpdateResult::class )
            ->setMethods( [ 'getMatchedCount', 'getModifiedCount' ] )
            ->getMock();
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
        $mongo = $this->getMockBuilder( MongoDb::class )
            ->disableOriginalConstructor()
            ->getMock();

        $mongo->program = ( !empty( $program ) ) ? $program : $this->_mockProgram();

        return $mongo;
    }


    public function testCreateSut()
    {
        $sut = $this->createSut( $this->_mockMongo() );

        $this->assertInstanceOf( Sut::class, $sut );
    }


    public function testItUpdatesARecord()
    {
        $quotes  = $this->_mockQuotes();
        $program = $this->_mockProgram( $quotes );
        $mongo   = $this->_mockMongo( $program );
        $id      = '12345';
        $query   = [ 'id' => $id ];
        $data    = [ 'name' => 'john' ];
        $bucket  = [ '$set' => $data ];
        $return  = [
            'id' => $id,
            'content' => $data
        ];

        $update_result = $this->_mockMongoResult();
        $update_result->expects( $this->once() )
            ->method( 'getModifiedCount' )
            ->willReturn( 1 );

        $mongo
            ->program
            ->quotes
            ->expects( $this->once() )
            ->method( 'updateOne' )
            ->with(  $query, $bucket  )
            ->willReturn( $update_result );

        $sut = $this->createSut( $mongo );
        $result = $sut->update( $id, $data );

        $this->assertInstanceOf( UpdateResult::class, $result );
        $this->assertEquals( 1, $result->getModifiedCount() );
    }
}
