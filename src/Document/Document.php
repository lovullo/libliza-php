<?php
/**
 * Generic document
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

namespace Lovullo\Liza\Document;

use Lovullo\Liza\Bucket\Bucket;


/**
 * Generic document with an associated key/value store
 *
 * This is a document in the same sense as a document database.
 */
class Document
{
    /**
     * Document identifier
     *
     * @var string
     */
    private $_id = '';

    /**
     * Document key/value store
     *
     * @var Bucket
     */
    private $_bucket = null;


    /**
     * Initialize document with identifier and key/value store
     *
     * $id will be cast to a string.
     *
     * @param string $id     document identifier
     * @param Bucket $bucket document key/value store
     */
    public function __construct( $id, Bucket $bucket )
    {
        $this->_id     = (string)$id;
        $this->_bucket = $bucket;
    }


    /**
     * Retrieve document identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }


    /**
     * Retrieve document key/value store
     *
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->_bucket;
    }
}
