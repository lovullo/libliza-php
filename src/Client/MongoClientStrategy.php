<?php
/**
 * Liza server client strategy
 *
 *  Copyright (C) 2020 Ryan Specialty Group, LLC.
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

class MongoClientStrategy
    implements ClientStrategy
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
    public function __construct( Dao $dao )
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
    public function getDocumentData( $doc_id )
    {
        throw new \Lovullo\Liza\Client\NotImplementedException( 'This method has not been implemented' );
    }


    /**
     * Retrieve program, data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array program data
     * @throws \Lovullo\Liza\Client\NotImplementedException
     */
    public function getProgramData( $doc_id )
    {
        throw new \Lovullo\Liza\Client\NotImplementedException( 'This method has not been implemented' );
    }


    /**
     * Send updated bucket data to the server
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     */
    public function sendBucketData( $doc_id, array $data )
    {
        return json_encode( $this->_dao->update( $doc_id, $data ) );
    }
}
