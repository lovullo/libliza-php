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
     * Document data key/value store
     *
     * @var Bucket
     */
    private $_bucket = null;

    /**
     * Document meta key/value store
     *
     * @var Bucket
     */
    private $_meta_bucket = null;

    /**
     * Document fields
     *
     * @var array
     */
    private $_fields = [];


    /**
     * Initialize document with identifier and key/value store
     *
     * $id will be cast to a string.
     *
     * @param string $doc_id      document identifier
     * @param Bucket $bucket      document data key/value store
     * @param Bucket $meta_bucket document meta key/value store
     * @param array  $fields      document fields
     */
    public function __construct(
        $doc_id,
        Bucket $bucket,
        Bucket $meta_bucket,
        array $fields = []
    ) {
        $this->_id          = (string)$doc_id;
        $this->_bucket      = $bucket;
        $this->_meta_bucket = $meta_bucket;
        $this->_fields      = $fields;
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
        // This existed before MissingDocumentFieldException and
        // was expected to not throw an exception when it does not exist.
        try {
            return (string)$this->getFieldByName("programId");
        } catch (MissingDocumentFieldException $e) {
            return "";
        }
    }


    /**
     * Get agent identifier
     *
     * @return string agent id
     */
    public function getAgentId()
    {
        return $this->getFieldByName("agentId");
    }


    /**
     * Get agent entity identifier (i.e. the user id)
     *
     * @return string agent entity id
     */
    public function getAgentEntityId()
    {
        return $this->getFieldByName("agentEntityId");
    }


    /**
     * Get the initial rated date.
     *
     * @return int A unix timestamp for the initial rating date
     */
    public function getInitialRatedDate()
    {
        return $this->getFieldByName("initialRatedDate");
    }


    /**
     * Get the last rated date.
     *
     * @return int A unix timestamp for the last rating date
     */
    public function getLastPremDate()
    {
        return $this->getFieldByName("lastPremDate");
    }


    /**
     * Get the start date.
     *
     * @return int A unix timestamp for the start date
     */
    public function getStartDate()
    {
        return $this->getFieldByName("startDate");
    }


    /**
     * Get agent name
     *
     * @return string agent name
     */
    public function getAgentName()
    {
        return $this->getFieldByName("agentName");
    }


    /**
     * Get copy quote id
     *
     * @return string copied from quote_id or empty string if unknown
     */
    public function getCopyFromQuote()
    {
        try {
            return (string)$this->getFieldByName("copyFromQuote");
        } catch (MissingDocumentFieldException $e) {
            return "";
        }
    }


    /**
     * Retrieve document meta key/value store
     *
     * @return Bucket
     */
    public function getMetaBucket()
    {
        return $this->_meta_bucket;
    }


    /**
     * Retrieve document data key/value store
     *
     * @return Bucket
     */
    public function getBucket()
    {
        return $this->_bucket;
    }


    /**
     * Get fields by key name
     *
     * @param String The name of the key holding the data
     * @return string field or empty string if unknown
     * @throws MissingDocumentFieldException when the field does not exist
     */
    private function getFieldByName($name)
    {
        if (array_key_exists($name, $this->_fields)) {
            return $this->_fields[ $name ];
        }

        throw new MissingDocumentFieldException("Missing field data");
    }
}
