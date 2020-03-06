<?php

/**
 * Liza server dao
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

namespace Lovullo\Liza\Tests\Dao;

use Lovullo\Liza\Dao\MongoDaoFactory as Sut;
use Lovullo\Liza\Dao\MongoDao;
use MongoClient;

class MongoDaoFactoryTest extends \PHPUnit_Framework_TestCase
{
    private function createSut()
    {
        return new Sut();
    }


    public function testCreateSut()
    {
        $sut = $this->createSut();
        $this->assertInstanceOf(Sut::class, $sut);
    }
}
