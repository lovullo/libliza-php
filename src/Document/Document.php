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
    private $_id = "";

    /**
     * Document key/value store
     *
     * @var Bucket
     */
    private $_bucket = null;

    /**
     * Document metadata
     *
     * @var array
     */
    private $_meta = [];


    /**
     * Initialize document with identifier and key/value store
     *
     * $id will be cast to a string.
     *
     * @param string $doc_id document identifier
     * @param Bucket $bucket document key/value store
     * @param array  $meta   document metadata
     */
    public function __construct( $doc_id, Bucket $bucket, array $meta = [] )
    {
        $this->_id     = (string)$doc_id;
        $this->_bucket = $bucket;
        $this->_meta   = $meta;
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
     * Get program identifier
     *
     * @return string program id or empty string if unknown
     */
    public function getProgramId()
    {
        return $this->_getMetaByName( "programId" );
    }


    /**
     * Get agent identifier
     *
     * @return string agent id or empty string if unknown
     */
    public function getAgentId()
    {
        return $this->_getMetaByName( "agentId" );
    }


    /**
     * Get agent entity identifier (i.e. the user id)
     *
     * @return string agent entity id or empty string if unknown
     */
    public function getAgentEntityId()
    {
        return $this->_getMetaByName( "agentEntityId" );
    }


    /**
     * Get the initial rated date.
     *
     * @return int A unix timestamp for the initial rating date
     */
    public function getInitialRatedDate()
    {
        return $this->_getMetaByName( "initialRatedDate" );
    }


    /**
     * Get the start date.
     *
     * @return int A unix timestamp for the start date
     */
    public function getStartDate()
    {
        return $this->_getMetaByName( "startDate" );
    }


    /**
     * Get agent name
     *
     * @return string agent name or empty string if unknown
     */
    public function getAgentName()
    {
        return $this->_getMetaByName( "agentName" );
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


    /**
     * Get metadata by key name
     *
     * @param String The name of the key holding the data.
     *
     * @return string metadata or empty string if unknown
     */
    private function _getMetaByName( $name )
    {
        $data = &$this->_meta[ $name ];
        return (string)$data;
    }
}
