<?php
/**
 * Simple bucket factory
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

use Lovullo\Liza\Bucket\SimpleBucketFactory as Sut;


class SimpleBucketFactoryTest
    extends BucketFactoryTestCase
{
    protected function getSut()
    {
        return new Sut();
    }


    public function testReturnsImmutableBucket()
    {
        // we can't check whether the array is properly passed to the
        // subtype; we'll have to trust it
        $this->assertInstanceOf(
            'Lovullo\Liza\Bucket\ImmutableBucket',
            $this->getSut()->fromData( array() )
        );
    }
}
