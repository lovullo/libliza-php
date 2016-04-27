<?php
/**
 * Generic document factory test
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

use Lovullo\Liza\Document\DocumentFactory as Sut;


class DocumentFactoryTest
    extends \PHPUnit_Framework_TestCase
{
    protected function createSut()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Document\DocumentFactory'
        )
            ->setMethods( array( 'createDocument' ) )
            ->getMock();
    }


    protected function createDummyBucket()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\Bucket'
        )
            ->getMock();
    }


    public function testCreatesDocumentWithGivenDocumentData()
    {
        $sut    = $this->createSut();
        $obj    = new \StdClass();
        $bucket = $this->createDummyBucket();
        $data   = array( 'foo' );

        $sut
            ->expects( $this->once() )
            ->method( 'createDocument' )
            ->with( $data, $bucket )
            ->willReturn( $obj );

        $this->assertSame(
            $obj,
            $sut->fromData( $data, $bucket )
        );
    }
}
