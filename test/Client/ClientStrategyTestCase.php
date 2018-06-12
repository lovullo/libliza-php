<?php
/**
 * Test case for client data retrieval strategies
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

use Lovullo\Liza\Client\ClientStrategy as Sut;


abstract class ClientStrategyTestCase
    extends \PHPUnit_Framework_TestCase
{
    protected abstract function createSut();


    public function testSutIsAClientStrategy()
    {
        $this->assertInstanceOf(
            'Lovullo\Liza\Client\ClientStrategy',
            $this->createSut()
        );
    }


    public function testGetDocumentDataReturnsAnArray()
    {
        $this->assertTrue(
            is_array( $this->createSut()->getDocumentData( 0 ) )
        );
    }


    /**
     * What was requested might not be what the server wants to return
     * (maybe there's an alias, redirect, etc).
     *
     * @depends testGetDocumentDataReturnsAnArray
     */
    public function testGetDocumentDataContainsDocumentId()
    {
        $given = $this->createSut()->getDocumentData( 0 );

        $this->assertArrayHasKey( 'id', $given );
    }


    /**
     * @depends testGetDocumentDataReturnsAnArray
     */
    public function testGetDocumentDataContainsKeyValueStoreData()
    {
        $given = $this->createSut()->getDocumentData( 0 );

        $this->assertArrayHasKey( 'data', $given );
        $this->assertTrue( is_array( $given ) );
    }


    public function testGetProgramDataReturnsAnArray()
    {
        $this->assertTrue(
            is_array( $this->createSut()->getProgramData( 0 ) )
        );
    }


    /**
     * What was requested might not be what the server wants to return
     * (maybe there's an alias, redirect, etc).
     *
     * @depends testGetProgramDataReturnsAnArray
     */
    public function testGetProgramDataContainsDocumentId()
    {
        $given = $this->createSut()->getProgramData( 0 );

        $this->assertArrayHasKey( 'id', $given );
    }


    /**
     * @depends testGetProgramDataReturnsAnArray
     */
    public function testGetProgramDataContainsKeyValueStoreData()
    {
        $given = $this->createSut()->getProgramData( 0 );

        $this->assertArrayHasKey( 'data', $given );
        $this->assertTrue( is_array( $given ) );
    }
}
