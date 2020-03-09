<?php

/**
 * Bucket factory common test
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

abstract class BucketFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    abstract protected function getSut();


    public function testReturnsBucketObject()
    {
        $this->assertInstanceOf(
            'Lovullo\Liza\Bucket\Bucket',
            $this->getSut()->fromData(array())
        );
    }
}
