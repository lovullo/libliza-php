<?php

/**
 * Generic document factory
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

namespace Lovullo\Liza\Document;

use Lovullo\Liza\Bucket\Bucket;

/**
 * Creates documents from raw data
 */
class DocumentFactory
{
    /**
     * Create document from given raw document data
     *
     * The document data $doc_data should be an array containing at least a
     * key named 'id' containing the document identifier and 'content'
     * containing document metadata.  This corresponds to the '/init' request.
     *
     * @param array  $doc_data    document init response data
     * @param Bucket $bucket      document data key/value store
     * @param Bucket $meta_bucket document meta key/value store
     *
     * @return Document
     *
     * @throws BadDocumentDataException if `id` key is missing or empty
     */
    final public function fromData(
        array $doc_data,
        Bucket $bucket,
        Bucket $meta_bucket
    ) {
        if (empty($doc_data[ 'id' ])) {
            throw new BadDocumentDataException(
                "Invalid or incomplete document data: missing 'id'"
            );
        }

        if (empty($doc_data[ 'content' ])) {
            throw new BadDocumentDataException(
                "Invalid or incomplete document data: missing 'content'"
            );
        }

        $doc_id = $doc_data[ 'id' ];
        $meta   = $doc_data[ 'content' ];

        return $this->createDocument($doc_id, $bucket, $meta_bucket, $meta);
    }


    /**
     * Create document from given data and bucket
     *
     * This exists to permit subtypes to override behavior.
     *
     * @param integer $doc_id      document identifier
     * @param Bucket  $bucket      document data key/value store
     * @param Bucket  $meta_bucket document meta key/value store
     * @param array   $meta        document metadata
     *
     * @return Document
     *
     * @codeCoverageIgnore constructor
     */
    protected function createDocument(
        $doc_id,
        Bucket $bucket,
        Bucket $meta_bucket,
        array $meta = []
    ) {
        return new Document($doc_id, $bucket, $meta_bucket, $meta);
    }
}
