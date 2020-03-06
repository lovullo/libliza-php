<?php

/**
 * Liza server dao
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

namespace Lovullo\Liza\Dao;

use MongoClient;
use RuntimeException;

class MongoDaoFactory
{
    /**
     * Override alias names (if needed)
     *
     * @param string $domain Domain where script is run from
     *
     * @return MongoDao
     * @codeCoverageIgnore constructor
     */
    public function fromHost($host)
    {
        $mongo_connection = $this->getMongoConnection($host);

        return new MongoDao($mongo_connection);
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
     * @param string $host The server host URI
     * @param integer $retry Number of connection retries on failure
     *
     * @return MongoClient connection
     *
     * @throws \RuntimeException
     * @codeCoverageIgnore constructor
     */
    private function getMongoConnection($host, $retry = 50)
    {
        try {
            return new MongoClient($host);
        } catch (\Exception $e) {
            if ($retry > 0) {
                return $this->getMongoConnection($host, --$retry);
            }
        }

        throw new RuntimeException("Tried to connect to the database multiple times and failed. Database may be offline.");
    }
}
