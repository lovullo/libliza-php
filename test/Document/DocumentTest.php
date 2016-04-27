<?php
/**
 * Tests generic document
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

namespace Lovullo\Liza\Tests\Document;

use \Lovullo\Liza\Document\Document as Sut;


class DocumentTest
    extends \PHPUnit_Framework_TestCase
{
    protected function getDummyBucket()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\Bucket'
        )
            ->setMethods( array( 'getDataByName', 'hasData' ) )
            ->getMock();
    }


    protected function createSut( $id, $bucket )
    {
        return new Sut( $id, $bucket );
    }


    public function testReturnsDocumentId()
    {
        $id = '123456';

        $this->assertEquals(
            $id,
            $this->createSut( $id, $this->getDummyBucket() )->getId()
        );
    }


    public function testIdReturnedAsAString()
    {
        $this->assertSame(
            '1234',
            $this->createSut( 1234, $this->getDummyBucket() )->getId()
        );
    }


    public function testReturnsBucket()
    {
        $bucket = $this->getDummyBucket();

        $this->assertSame(
            $bucket,
            $this->createSut( 'foo', $bucket )->getBucket()
        );
    }
}
