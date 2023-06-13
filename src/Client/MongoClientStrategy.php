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
    public function getDocumentData(string $doc_id, string $program)
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
    public function getProgramData(string $doc_id, string $program)
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
     * @{inheritdoc}
     */
    public function setDocumentOrigin($doc_id, array $origin)
    {
        return json_encode($this->_dao->update($doc_id, [ 'meta.origin' => $origin ]));
    }


    /**
     * @{inheritdoc}
     */
    public function setDocumentOwner(
        int $doc_id,
        array $access_groups,
        string $agent_email
    ) {
        return json_encode($this->_dao->update($doc_id, [
            'meta.liza_access_groups' => $access_groups,
            'meta.liza_access_email' => [$agent_email],
        ]));
    }
}
