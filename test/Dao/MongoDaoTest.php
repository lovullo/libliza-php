<?php

/**
 * Tests Liza client
 *
 *  Copyright (C) 2016-2020 Ryan Specialty Group, LLC.
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
use MongoClient;

class MongoDaoTest extends \PHPUnit_Framework_TestCase
{


    protected function createSut($mongo_client)
    {
        return new Sut($mongo_client);
    }


    private function mockQuotes()
    {
        return $this->getMockBuilder(\StdClass::class)
            ->setMethods([ 'update' ])
            ->getMock();
    }


    private function mockProgram($quotes = null)
    {
        $program = $this->getMockBuilder(\StdClass::class)->getMock();
        $program->quotes = ( !empty($quotes) ) ? $quotes : $this->mockQuotes();

        return $program;
    }


    private function mockMongo($program = null)
    {
        $mongo = $this->getMockBuilder(MongoClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mongo->program = ( !empty($program) ) ? $program : $this->mockProgram();

        return $mongo;
    }


    public function testCreateSut()
    {
        $sut = $this->createSut($this->mockMongo());

        $this->assertInstanceOf(Sut::class, $sut);
    }


    public function testItUpdatesARecord()
    {
        $quotes  = $this->mockQuotes();
        $program = $this->mockProgram($quotes);
        $mongo   = $this->mockMongo($program);
        $id      = '12345';
        $query   = [ 'id' => $id ];
        $data    = [ 'name' => 'john' ];
        $bucket  = [ '$set' => $data ];

        $mongo
            ->program
            ->quotes
            ->expects($this->once())
            ->method('update')
            ->with($query, $bucket)
            ->willReturn(1);

        $sut = $this->createSut($mongo);
        $result = $sut->update($id, $data);

        $this->assertEquals(1, $result);
    }
}
