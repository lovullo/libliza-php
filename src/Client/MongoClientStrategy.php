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
     *
     * @return array document data
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function getDocumentData($doc_id)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array program data
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function getProgramData($doc_id)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * Set updated bucket data
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function setDocumentData($doc_id, array $data)
    {
        throw new NotImplementedException('This method has not been implemented');
    }


    /**
     * Update the agentName field on a document
     *
     * @param string $doc_id Document id
     * @param string $name   The entity name
     *
     * @return string JSON object
     */
    public function setDocumentOwnerName($doc_id, $agent_name)
    {
        return json_encode($this->_dao->update($doc_id, ['agentName' => $agent_name]));
    }


    /**
     * Update the agentEntityId field on a document
     *
     * @param string  $doc_id Document id
     * @param integer $id     The entity id
     *
     * @return string JSON object
     *
     * @throws BadClientDataException
     */
    public function setDocumentOwnerId($doc_id, $agent_entity_id)
    {
        if (!is_numeric($agent_entity_id)) {
            throw new BadClientDataException('Owner ID must be numeric');
        }

        return json_encode($this->_dao->update($doc_id, ['agentEntityId' => (int)$agent_entity_id]));
    }
}
