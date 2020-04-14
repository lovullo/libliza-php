<?php

/**
 * Tests MongoClient strategy
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

use Lovullo\Liza\Client\MongoClientStrategy as Sut;
use Lovullo\Liza\Dao\Dao;

class MongoClientStrategyTest extends ClientStrategyTestCase
{


    protected function createSut()
    {
        $mongo = $this->mockMongo();
        $dao = $this->mockDao($mongo);

        return new Sut($dao);
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
        $mongo = $this->getMockBuilder(\MongoDb::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mongo->program = ( !empty($program) ) ? $program : $this->mockProgram();

        return $mongo;
    }


    private function mockDao($mongo)
    {
        return $this->getMockBuilder(Dao::class)
            ->setConstructorArgs([ $mongo ])
            ->setMethods([ 'update' ])
            ->getMock();
    }


    public function testCreateSut()
    {
        $sut = $this->createSut();
        $this->assertInstanceOf(Sut::class, $sut);
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
        $this->createSut()->getDocumentData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetDocumentDataContainsDocumentId()
    {
        $this->createSut()->getDocumentData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetDocumentDataContainsKeyValueStoreData()
    {
        $this->createSut()->getDocumentData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataReturnsAnArray()
    {
        $this->createSut()->getProgramData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataContainsDocumentId()
    {
        $this->createSut()->getProgramData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testGetProgramDataContainsKeyValueStoreData()
    {
        $this->createSut()->getProgramData(0);
    }


    /**
     * Overrides unneeded parent test
     *
     * @expectedException Lovullo\Liza\Client\NotImplementedException
     */
    public function testSetDocumentDataNotImplemented()
    {
        $this->createSut()->setDocumentData(0, []);
    }


    public function testItUpdatesTheOwner()
    {
        $quote   = $this->mockQuotes();
        $program = $this->mockProgram($quote);
        $mongo   = $this->mockMongo($program);
        $dao     = $this->mockDao($mongo);
        $sut     = new Sut($dao);

        $id              = '12345';
        $agent_entity_id = 12434300;
        $agent_id        = 921322;
        $agent_name      = 'john';

        $data = [
            'agentEntityId' => $agent_entity_id,
            'agentId'       => $agent_id,
            'agentName'     => $agent_name,
        ];

        $dao_return = [
            'ok' => 1,
            'nModified' => 0,
            'n' => 0,
            'err' => null,
            'errmsg' => null,
            'updatedExisting' => 1,
        ];

        $dao->expects($this->once())
            ->method('update')
            ->with($id, $data)
            ->willReturn($dao_return);

        $actual = $sut->setDocumentOwner($id, $agent_entity_id, $agent_id, $agent_name);
        $actual = json_decode($actual, true);

        $this->assertEquals(1, $actual[ 'ok' ]);
    }


    /**
    * @expectedException Lovullo\Liza\Client\BadClientDataException
    */
    public function testUpdateOwnerIdThrowsBadDataException()
    {
        $quote   = $this->mockQuotes();
        $program = $this->mockProgram($quote);
        $mongo   = $this->mockMongo($program);
        $dao     = $this->mockDao($mongo);
        $sut     = new Sut($dao);

        $id              = '12345';
        $agent_entity_id = 'x12434300';
        $agent_id        = 921322;
        $agent_name      = 'john';

        $actual = $sut->setDocumentOwner($id, $agent_entity_id, $agent_id, $agent_name);
    }
}
