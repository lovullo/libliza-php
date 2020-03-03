<?php
/**
 * Liza server dao
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

namespace Lovullo\Liza\Dao;

use MongoClient;

class MongoDaoFactory
{
    /**
     * Override alias names (if needed)
     *
     * @param string $domain Domain where script is run from
     *
     * @return array $pdo_alias Array of db aliases to be used
     */
    public function fromHost( $host )
    {
        $mongo_client = $this->_getMongoClient( $host );

        return new MongoDao( $mongo_client );
    }


    /**
     * MongoDB uses connection pooling/persistent connections. However, it does not
     * guarantee the connection will be valid. From PHP, we must catch any
     * Exceptions when creating a MongoClient connection and retry until we get a
     * working connection.
     *
     * Connection pooling in the driver cannot be turned off, so this is the only
     * option until the phongo driver is stable.
     *
     * @see https://jira.mongodb.org/browse/PHP-854
     *
     * @throws \Exception
     */
    private function _getMongoClient( $mongo_db, $retry = 50 )
    {
        try
        {
            return new \MongoClient( $mongo_db );
        }
        catch ( \Exception $e )
        {
            // Do nothing, we are going to retry the connection
        }

        if ( $retry > 0 )
        {
            return $this->_getMongoClient( $mongo_db, --$retry );
        }

        throw new \Exception( "Tried to connect to the database 50 times and failed. Database may be offline." );
    }
}





