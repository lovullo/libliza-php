<?php

/**
 * Liza server client strategy
 *
 *  Copyright (C) 2016-2020 Ryan Specialty Group, LLC.
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

use Lovullo\Liza\Dao\Dao;
use Lovullo\Liza\Client\BadClientDataException;
use Lovullo\Liza\Client\NotImplementedException;

class MongoClientStrategy implements ClientStrategy
{
    /**
     * Data Access Object
     *
     * @var Dao
     */
    private $_dao = null;


    /**
     * Initialize REST client
     *
     * @param Dao $dao
     */
    public function __construct(Dao $dao)
    {
        $this->_dao = $dao;
    }


    /**
     * Retrieve data for document identified by given id
     *
     * @param string $doc_id document id
     * @param string|null $cookie Session cookie
     *
     * @return array document data
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function getDocumentData($doc_id, $cookie = null)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id document id
     * @param string|null $cookie Session cookie
     *
     * @return array program data
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function getProgramData($doc_id, $cookie = null)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * Set updated bucket data
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     * @param string|null $cookie Session cookie
     *
     * @return string JSON object
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function setDocumentData($doc_id, array $data, $cookie = null)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * @{inheritdoc}
     */
    public function setDocumentOrigin($doc_id, array $origin)
    {
        return json_encode($this->_dao->update($doc_id, [ 'meta' => [ 'origin' => $origin ]]));
    }


    /**
     * Update the document owner fields on a document
     * These three fields work in conjunction to show ownership of the document
     * None of these fields should be updated without the others
     *
     * @param string  $doc_id          Document id
     * @param integer $agent_entity_id The entity id
     * @param integer $agent_id        The owner id
     * @param string  $agent_name      The owner name
     *
     * @return string JSON object
     *
     * @throws BadClientDataException
     */
    public function setDocumentOwner($doc_id, $agent_entity_id, $agent_id, $agent_name)
    {
        if (!is_numeric($agent_entity_id)) {
            throw new BadClientDataException('Entity ID must be numeric');
        }

        return json_encode($this->_dao->update($doc_id, [
            'agentEntityId' => (int)$agent_entity_id,
            'agentId'       => (string)$agent_id,
            'agentName'     => (string)$agent_name,
        ]));
    }
}
