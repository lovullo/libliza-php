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
    public function getDocumentData($doc_id);


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array program data
     */
    public function getProgramData($doc_id);


    /**
     * Send updated bucket data to the server
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     */
    public function setDocumentData($doc_id, array $data);


    /**
     * Update the document owner fields on a document
     * These three fields work in conjunction to show ownership of the document
     * None of these fields should be updated without the others
     *
     * @param int    $doc_id          Document id
     * @param string $agent_entity_id The entity id
     * @param string $agent_id        The owner id
     * @param string $agent_name      The owner name
     * @param string $retail_agency   The retail agency
     *
     * @return string JSON object
     */
    public function setDocumentOwner(
        int $doc_id,
        string $agent_entity_id,
        string $agent_id,
        string $agent_name,
        string $retail_agency
    );


   /**
    * Set document origin
    *
    * @param string $doc_id Document id
    * @param array  $origin The document origin
    *
    * @return string JSON object
    */
    public function setDocumentOrigin($doc_id, array $origin);
}
