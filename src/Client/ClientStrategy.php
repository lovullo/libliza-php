<?php
/**
 * Client data retrieval
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


/**
 * Strategy for retrieving data from the server
 */
interface ClientStrategy
{
    /**
     * Retrieve data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array document data
     */
    public function getDocumentData( $doc_id );


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array program data
     */
    public function getProgramData( $doc_id );


    /**
     * Send updated bucket data to the server
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     */
    public function sendBucketData( $doc_id, array $data );
}
