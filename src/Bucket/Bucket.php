<?php
/**
 * Generalized key/value store
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

namespace Lovullo\Liza\Bucket;


/**
 * Generalized key/value store
 *
 * All bucket data can be thought of as a key-value store in which every value
 * is represented as an array.  All values *must* be an array, and the bucket
 * implementation should always perform that assertion.
 *
 * This should closely resemble the bucket implementation in Liza.
 */
interface Bucket
{
    /**
     * Return the values associated with the provided key
     *
     * This method accepts an index due to PHP's lack of array dereferencing
     * support prior to 5.4.
     *
     * @param string $name  name of key
     * @param int    $index index to return (default all)
     *
     * @return array|mixed values associated with the key
     *
     * @throws \DomainException if requested index does not exist
     */
    public function getDataByName( $name, $index = null );


    /**
     * Whether the given key and (optionally) index exist
     *
     * @param string $name  name of key
     * @param int    $index index to check existence of
     *
     * @return bool whether the requested key/index exists
     */
    public function hasData( $name, $index = null );
}
