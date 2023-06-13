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
     * @param string $doc_id  document id
     * @param string $program program id
     *
     * @return array document data
     */
    public function getDocumentData(string $doc_id, string $program);


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id  document id
     * @param string $program program id
     *
     * @return array program data
     */
    public function getProgramData(string $doc_id, string $program);


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
     * @param array  $access_groups   The visibility groups
     * @param string $agent_email     The email address of the agent
     *
     * @return string JSON object
     */
    public function setDocumentOwner(
        int $doc_id,
        array $access_groups,
        string $agent_email
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
