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
     * Use session cookie
     *
     * @var string
     */
    private $_cookie = null;


    /**
     * Initialize REST client
     *
     * @param string $base_url Base URL for REST endpoint
     * @param string $skey     Internal session key
     * @param bool   $cookie   Session cookie
     */
    public function __construct($base_url, $skey, $cookie = null)
    {
        $this->_base_url = (string)$base_url;
        $this->_skey     = (string)$skey;
        $this->_cookie   = $cookie;
    }


    /**
     * Retrieve data for document identified by given id
     *
     * Until a better API is available, this simply uses the `/init`
     * request, which returns all the data we need.
     *
     * @param string $doc_id  document id
     * @param string $program program id
     *
     * @return array document data
     */
    public function getDocumentData(string $doc_id, string $program)
    {
        $doc_id = (string)$doc_id;

        $doc_data = $this->translateDocId(
            $this->queryDocument($this->_base_url, $program, $doc_id, 'init')
        );

        $this->verifyData($doc_data);

        return $doc_data;
    }


    /**
     * Retrieve program data for document identified by given id
     *
     * @param string $doc_id  document id
     * @param string $program program id
     *
     * @return array program data
     */
    public function getProgramData(string $doc_id, string $program)
    {
        $doc_id = (string)$doc_id;

        $program_data = $this->translateDocId(
            $this->queryDocument($this->_base_url, $program, $doc_id, 'progdata')
        );

        return $program_data;
    }


    /**
     * Set updated bucket data to the server
     *
     * @param string $doc_id Document id
     * @param array  $data   The data as an array
     *
     * @return string JSON object
     */
    public function setDocumentData($doc_id, array $data)
    {
        $doc_id = (string)$doc_id;

        $this->verifyData($data[ 'data' ]);
        $data[ 'data' ] = json_encode($data[ 'data' ]);

        return $this->postData($this->_base_url, $doc_id, 'step/1/post', $data);
    }


    /**
     * @{inheritdoc}
     *
     * @SuppressWarnings(PHPMD)  not implemented yet, satisfying interfaces
     */
    public function setDocumentOwner(
        int $doc_id,
        array $access_groups,
        string $agent_email
    ) {
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
     * @param string $program  program id
     * @param string $doc_id   id of document to retrieve
     * @param string $endpoint endpoint for REST service
     *
     * @return array document data
     */
    protected function queryDocument($base_url, $program, $doc_id, $endpoint)
    {
        $url  = $base_url . $program . '/' . $doc_id . '/' . $endpoint;
        $url .= (null === $this->_cookie) ? '?skey=' . $this->_skey : '';

        $http = [
            'method' => 'GET'
        ];

        if (null !== $this->_cookie) {
            $http['header'] = "Cookie: " . $this->_cookie;
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
     *
     * @return string JSON object
     */
    protected function postData($base_url, $doc_id, $endpoint, $data)
    {
        $url  = $base_url . $doc_id . '/' . $endpoint;
        $url .= (null === $this->_cookie) ? '?skey=' . $this->_skey : '';

        $content = http_build_query($data);

        $http = [
            'method'  => 'POST',
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'content' => $content,
            'timeout' => 60,
        ];

        if (null !== $this->_cookie) {
            $http['header'] .= "Cookie: " . $this->_cookie . "\r\n";
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
