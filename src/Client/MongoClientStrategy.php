<?php

namespace Lovullo\Liza\Client;

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
     * @param string $base_url base URL for REST endpoint
     * @param string $skey     Session key
     */
    public function __construct( $dao )
    {
        $this->_dao = $dao;
    }
    /**
     * Retrieve data for document identified by given id
     *
     * @param string $doc_id document id
     *
     * @return array document data
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
    public function sendBucketData( $doc_id, $data )
    {
        return json_encode( $this->_dao->update( $doc_id, $data ) );
    }
}
