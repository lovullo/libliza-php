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

use Lovullo\Liza\Client\NotImplementedException;

/**
 * Retrieves data over a RESTful interface
 *
 * TODO: Document RESTful API somewhere.
 *
 * TODO: The constructor should accept a network abstraction, which we do
 * not have right now; for the time being, we'll restrict network activity
 * to a single method that can be overridden.
 */
class RestClientStrategy implements ClientStrategy
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
     * @param string $base_url Base URL for REST endpoint
     * @param string $skey     Internal session key
     */
    public function __construct($base_url, $skey)
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
     * @param string      $doc_id document id
     * @param string|null $cookie Session cookie
     *
     * @return array document data
     */
    public function getDocumentData($doc_id, $cookie = null)
    {
        $doc_id = (string)$doc_id;
        $doc_data = $this->translateDocId(
            $this->queryDocument($this->_base_url, $doc_id, 'init', $cookie)
        );

        $this->verifyData($doc_data);

        return $doc_data;
    }


    /**
     * Retrieve program data for document identified by given id
     *
     * @param string $doc_id document id
     * @param string|null $cookie Session cookie
     *
     * @return array program data
     */
    public function getProgramData($doc_id, $cookie = null)
    {
        $doc_id = (string)$doc_id;

        $program_data = $this->translateDocId(
            $this->queryDocument($this->_base_url, $doc_id, 'progdata', $cookie)
        );

        return $program_data;
    }


    /**
     * Set updated bucket data to the server
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     * @param string|null $cookie Session cookie
     *
     * @return string JSON object
     */
    public function setDocumentData($doc_id, array $data, $cookie = null)
    {
        $doc_id = (string)$doc_id;

        $this->verifyData($data[ 'data' ]);
        $data[ 'data' ] = json_encode($data[ 'data' ]);

        return $this->postData($this->_base_url, $doc_id, 'step/1/post', $data, $cookie);
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
     * @SuppressWarnings(PHPMD)  not implemented yet, satisfying interfaces
     */
    public function setDocumentOwner($doc_id, $agent_entity_id, $agent_id, $agent_name)
    {
        throw new NotImplementedException('This feature has not been implemented');
    }


    /**
     * @{inheritdoc}
     *
     * @SuppressWarnings(PHPMD)  not implemented yet, satisfying interfaces
     */
    public function setDocumentOrigin($doc_id, array $origin)
    {
        throw new NotImplementedException('This feature has not been implemented');
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
     * @param string|null $cookie Session cookie
     *
     * @return array document data
     */
    protected function queryDocument($base_url, $doc_id, $endpoint, $cookie = null)
    {
        $url  = $base_url . $doc_id . '/' . $endpoint;
        $url .= (null === $cookie) ? '?skey=' . $this->_skey : '';

        $http = [
            'method' => 'GET'
        ];

        if (null !== $cookie) {
            $http['header'] = "Cookie: " . $cookie;
        }

        $options = ['http' => $http];

        $context = stream_context_create($options);

        return json_decode(
            file_get_contents($url, false, $context),
            true
        );
    }


    /**
     * Post data to the server
     *
     * TODO: This will eventually use a network abstraction; until that
     * time, a simple `file_get_contents` will be sort of acceptable, but
     * not really, because we do not have proper error handling; this would
     * fail simply because the caller's data validations would fail.
     *
     * No trailing slash will be added to $base_url.
     *
     * @param string $base_url Base URL for REST service
     * @param string $doc_id   Id of document
     * @param string $endpoint Endpoint for REST service
     * @param array  $data     The data as an array
     * @param string|null $cookie Session cookie
     *
     * @return string JSON object
     */
    protected function postData($base_url, $doc_id, $endpoint, $data, $cookie = null)
    {
        $url  = $base_url . $doc_id . '/' . $endpoint;
        $url .= (null === $cookie) ? '?skey=' . $this->_skey : '';

        $content = http_build_query($data);

        $http = [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => $content,
            'timeout' => 60,
        ];

        if (null !== $cookie) {
            $http['header'] .= "Cookie: " . $cookie . "\r\n";
        }

        $options = ['http' => $http];

        $context = stream_context_create($options);

        return file_get_contents($url, false, $context);
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
    protected function translateDocId(array $doc_data)
    {
        if (array_key_exists('quoteId', $doc_data)) {
            $doc_data[ 'id' ] = $doc_data[ 'quoteId' ];
            unset($doc_data[ 'quoteId' ]);
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
    protected function verifyData(array $doc_data)
    {
        if (
            !isset($doc_data[ 'id' ])
            && !isset($doc_data[ 'quoteId' ])
        ) {
            throw new BadClientDataException(
                "Data are missing document id"
            );
        }
    }
}
