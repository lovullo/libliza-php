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

use MongoDb;

class MongoDao implements Dao
{
    /**
     * Mongo database connection
     */
    private $_db;


    /**
     * QuoteDao class
     *
     * @param MongoDb $mongo_host
     */
    public function __construct( MongoDb $mongo )
    {
        $this->_db = $mongo;
    }


    /**
     * Update Mongo document with transformed quote data
     *
     * @param integer $quote_id Quote ID used for document
     * @param array   $data     Array built for data updates
     *
     * @return mixed Results of the mongo query call
     */
    public function update(
        $quote_id,
        $data
    )
    {
        return $this->_db->program->quotes->updateOne(
            [ 'id'   => $quote_id ],
            [ '$set' => $data ]
        );
    }

}

