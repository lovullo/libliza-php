<?php
/**
 * Bucket string formatter
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

namespace Lovullo\Liza\Bucket\Formatter;

use Lovullo\Liza\Bucket\Bucket;


/**
 * Format bucket contents into string
 */
interface BucketFormatter
{
    /**
     * Build string from bucket
     *
     * @param Bucket $bucket bucket from which data should be retrieved
     */
    public function format( Bucket $bucket );
}
