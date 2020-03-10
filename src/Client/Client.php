<?php

/**
 * Liza server client
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

namespace Lovullo\Liza\Client;

use Lovullo\Liza\Document\DocumentFactory;
use Lovullo\Liza\Bucket\BucketFactory;

/**
 * Liza server client
 *
 * This is a high-level API for the entire system.
 */
class Client
{
    /**
     * Data retrieval strategy
     * @var ClientStrategy
     */
    private $_strategy = null;

    /**
     * Document creation
     * @var DocumentFactory
     */
    private $_doc_factory = null;

    /**
     * Bucket creation
     * @var BucketFactory
     */
    private $_bucket_factory = null;


    /**
     * Initialize client
     *
     * @param ClientStrategy  $strategy       data retrieval strategy
     * @param DocumentFactory $doc_factory
     * @param BucketFactory   $bucket_factory
     */
    public function __construct(
        ClientStrategy $strategy,
        DocumentFactory $doc_factory,
        BucketFactory $bucket_factory
    ) {
        $this->_strategy       = $strategy;
        $this->_doc_factory    = $doc_factory;
        $this->_bucket_factory = $bucket_factory;
    }


    /**
     * Initialize document data
     *
     * @param string $doc_id document identifier
     *
     * @return array document data
     *
     * @throws BadClientDataException if data are invalid or missing
     */
    public function getDocumentData($doc_id)
    {
        return $this->_strategy->getDocumentData($doc_id);
    }


    /**
     * Retrieve document by given id
     *
     * @param string  $doc_id document identifier
     *
     * @return Document
     *
     * @throws BadClientDataException if data are invalid or missing
     */
    public function getDocument($doc_id)
    {
        $doc_data = $this->getDocumentData($doc_id);

        return $this->_doc_factory->fromData(
            $doc_data,
            $this->_bucket_factory->fromData(
                $this->getBucketData($doc_data)
            ),
            $this->_bucket_factory->fromData(
                $this->getMetaBucketData($doc_data)
            )
        );
    }


    /**
     * Set bucket data for a document
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     */
    public function setDocumentData($doc_id, $data)
    {
        return $this->_strategy->setDocumentData($doc_id, $data);
    }


    /**
     * Set bucket data for a document
     *
     * @param string $doc_id Document id
     * @param string $name   The entity's name
     *
     * @return string JSON object
     */
    public function setDocumentOwnerName($doc_id, $name)
    {
        return $this->_strategy->setDocumentOwnerName($doc_id, $name);
    }


    /**
     * Set bucket data for a document
     *
     * @param string $doc_id Document id
     * @param string $id     The entity's id
     *
     * @return string JSON object
     */
    public function setDocumentOwnerId($doc_id, $id)
    {
        return $this->_strategy->setDocumentOwnerId($doc_id, $id);
    }


    /**
     * Retrieve program data by given id
     *
     * @param string $doc_id document identifier
     *
     * @return array program data
     *
     * @throws BadClientDataException if program data is invalid or missing
     */
    public function getProgramData($doc_id)
    {
        $program_data = $this->_strategy->getProgramData($doc_id);

        if (
            empty($program_data[ 'data' ])
            || !is_array($program_data[ 'data' ])
        ) {
            throw new BadClientDataException(
                "Invalid or missing program data"
            );
        }

        return $program_data[ 'data' ];
    }


    /**
     * Retrieve bucket data
     *
     * The data are expected to reside in `data` of `$doc_data`, and must be
     * an array.
     *
     * @param array $doc_data document data
     *
     * @return array bucket data
     *
     * @throws BadClientDataException if document content is invalid or missing
     * @throws BadClientDataException if data are invalid or missing
     */
    protected function getBucketData(array $doc_data)
    {
        if (
            !isset($doc_data[ 'content' ])
            || !is_array($doc_data[ 'content' ])
        ) {
            throw new BadClientDataException(
                "Invalid or missing content data"
            );
        }

        $content = $doc_data[ 'content' ];

        if (
            empty($content[ 'data' ])
            || !is_array($content[ 'data' ])
        ) {
            throw new BadClientDataException(
                "Invalid or missing bucket data"
            );
        }

        return $content[ 'data' ];
    }


    /**
     * Retrieve meta bucket data
     *
     * The data are expected to reside in `meta` of `$doc_data`, and must be
     * an array.
     *
     * @param array $doc_data document data
     *
     * @return array bucket data
     *
     * @throws BadClientDataException if document content is invalid or missing
     * @throws BadClientDataException if data are invalid or missing
     */
    protected function getMetaBucketData(array $doc_data)
    {
        if (
            !isset($doc_data[ 'content' ])
            || !is_array($doc_data[ 'content' ])
        ) {
            // Unreachable by phpunit.
            // getBucketData will catch this before
            // execution gets here
            // @codeCoverageIgnoreStart
            throw new BadClientDataException(
                "Invalid or missing content data"
            );
            // @codeCoverageIgnoreEnd
        }

        $content = $doc_data[ 'content' ];

        if (
            empty($content[ 'meta' ])
            || !is_array($content[ 'meta' ])
        ) {
            throw new BadClientDataException(
                "Invalid or missing meta data"
            );
        }

        return $content[ 'meta' ];
    }
}
