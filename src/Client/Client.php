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
        ClientStrategy  $strategy,
        DocumentFactory $doc_factory,
        BucketFactory   $bucket_factory
    )
    {
        $this->_strategy       = $strategy;
        $this->_doc_factory    = $doc_factory;
        $this->_bucket_factory = $bucket_factory;
    }


    /**
     * Retrieve document by given id
     *
     * @param string $doc_id document identifier
     *
     * @return Document
     *
     * @throws BadClientDataException if data are invalid or missing
     */
    public function getDocument( $doc_id )
    {
        $doc_data = $this->_strategy->getDocumentData( $doc_id );

        return $this->_doc_factory->fromData(
            $doc_data,
            $this->_bucket_factory->fromData(
                $this->getBucketData( $doc_data )
            )
        );
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
     * @throws BadClientDataException if data are invalid or missing
     */
    protected function getBucketData( array $doc_data )
    {
        if ( empty( $doc_data[ 'data' ] )
            || !is_array( $doc_data[ 'data' ] ) )
        {
            throw new BadClientDataException(
                "Invalid or missing bucket data"
            );
        }

        return $doc_data[ 'data' ];
    }
}
