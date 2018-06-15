<?php
/**
 * RESTful client data retrieval
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
 * Retrieves data over a RESTful interface
 *
 * TODO: Document RESTful API somewhere.
 *
 * TODO: The constructor should accept a network abstraction, which we do
 * not have right now; for the time being, we'll restrict network activity
 * to a single method that can be overridden.
 */
class RestClientStrategy
    implements ClientStrategy
{
    /**
     * Base URL of REST endpoint
     *
     * @var string
     */
    private $_base_url = '';

    /**
     * Session key required for REST endpoint
     *
     * @var string
     */
    private $_skey = '';


    /**
     * Initialize REST client
     *
     * @param string $base_url base URL for REST endpoint
     * @param string $skey     Session key
     */
    public function __construct( $base_url, $skey )
    {
        $this->_base_url = (string)$base_url;
        $this->_skey     = (string)$skey;
    }


    /**
     * Retrieve data for document identified by given id
     *
     * Until a better API is available, this simply uses the `/init`
     * request, which returns all the data we need.
     *
     * @param string $doc_id document id
     *
     * @return array document data
     */
    public function getDocumentData( $doc_id )
    {
        $doc_id = (string)$doc_id;

        $doc_data = $this->translateDocId(
            $this->queryDocument( $this->_base_url, $doc_id, 'init' )
        );

        $this->verifyData( $doc_data );

        return $doc_data;
    }


    /**
     * Retrieve program data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array program data
     */
    public function getProgramData( $doc_id )
    {
        $doc_id = (string)$doc_id;

        $program_data = $this->translateDocId(
            $this->queryDocument( $this->_base_url, $doc_id, 'progdata' )
        );

        return $program_data;
    }


    /**
     * Query server for document data
     *
     * TODO: This will eventually use a network abstraction; until that
     * time, a simple `file_get_contents` will be sort of acceptable, but
     * not really, because we do not have proper error handling; this would
     * fail simply because the caller's data validations would fail.
     *
     * No trailing slash will be added to $base_url.
     *
     * @param string $base_url base URL for REST service
     * @param string $doc_id   id of document to retrieve
     * @param string $endpoint endpoint for REST service
     *
     * @return array document data
     */
    protected function queryDocument( $base_url, $doc_id, $endpoint )
    {
        $url = $base_url . $doc_id . '/' . $endpoint . '?skey=' . $this->_skey;

        return json_decode(
            file_get_contents( $url ),
            true
        );
    }


    /**
     * {Forward,Backward}s compatibility for document ids
     *
     * The original liza server, having been written for an insurance
     * quoting system, identified the document as `quoteId`.  This allows
     * for forward-compatiblity with the new `id`, and
     * backwards-compatiblity once it actually changes.
     *
     * All new systems should use `id`.
     *
     * TODO: Remove me after some time.
     *
     * @return array document data with quote id translation
     */
    protected function translateDocId( array $doc_data )
    {
        if ( array_key_exists( 'quoteId', $doc_data ) )
        {
            $doc_data[ 'id' ] = $doc_data[ 'quoteId' ];
            unset( $doc_data[ 'quoteId' ] );
        }

        return $doc_data;
    }


    /**
     * Verify that the given document data are correct; otherwise, throw an
     * exception
     *
     * @param array $doc_data document data to verify
     *
     * @return void
     *
     * @throws BadClientDataException
     */
    protected function verifyData( array $doc_data )
    {
        if ( !isset( $doc_data[ 'id' ] )
            && !isset( $doc_data[ 'quoteId' ] ) )
        {
            throw new BadClientDataException(
                "Data are missing document id"
            );
        }
    }
}
