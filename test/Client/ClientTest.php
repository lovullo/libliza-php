<?php

/**
 * Tests Liza client
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

namespace Lovullo\Liza\Tests\Client;

use Lovullo\Liza\Client\Client as Sut;
use Lovullo\Liza\Client\BadClientDataException;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected function createSut($strategy, $doc_factory, $bucket_factory)
    {
        return new Sut($strategy, $doc_factory, $bucket_factory);
    }


    protected function createMockStrategy()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Client\ClientStrategy'
        )
            ->getMock();
    }


    protected function createMockDocFactory()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Document\DocumentFactory'
        )
            ->setMethods(array( 'createDocument' ))
            ->getMock();
    }


    protected function createMockBucketFactory()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\BucketFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods(array( 'fromData' ))
            ->getMock();
    }


    protected function createDummyDocument()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Document\Document'
        )
            ->disableOriginalConstructor()
            ->getMock();
    }


    protected function createDummyBucket()
    {
        return $this->getMockBuilder(
            'Lovullo\Liza\Bucket\Bucket'
        )
            ->disableOriginalConstructor()
            ->getMock();
    }


    protected function getDummyData()
    {
        // valid data
        return [
            'id'   => 0,
            'content' => [
                'data' => [],
                'meta' => [],
            ]
        ];
    }


    public function testRetrievesNewBucketFromDocumentData()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();
        $doc_request_id      = '0';

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $mock_return = [
            'id'       => 200007,
            'content'  => [ "valid" => false ],
            'hasError' => false
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('getDocumentData')
            ->with($doc_request_id)
            ->willReturn($mock_return);

        $this->assertSame(
            $mock_return,
            $sut->getDocumentData($doc_request_id)
        );
    }


    public function testSendBucketData()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();
        $doc_request_id      = 200007;
        $bucket_data         = [ 'bucket' => 'data' ];

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $parameters = [
            'data'            => $bucket_data,
            'concluding_save' => false
        ];

        $mock_return = [
            'quoteId'  => $doc_request_id,
            'hasError' => false
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('setDocumentData')
            ->with($doc_request_id, $parameters)
            ->willReturn($mock_return);

        $this->assertSame(
            $mock_return,
            $sut->setDocumentData($doc_request_id, $parameters)
        );
    }


    public function testSetOwnerName()
    {
        $mock_strategy = $this->getMockBuilder('Lovullo\Liza\Client\MongoClientStrategy')
            ->disableOriginalConstructor()
            ->getMock();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();
        $doc_request_id      = 200007;
        $agentName           = 'john';

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $mock_return = [
            'quoteId'  => $doc_request_id,
            'hasError' => false
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('setDocumentOwnerName')
            ->with($doc_request_id, $agentName)
            ->willReturn($mock_return);

        $this->assertSame(
            $mock_return,
            $sut->setDocumentOwnerName($doc_request_id, $agentName)
        );
    }


    public function testSetOwnerId()
    {
        $mock_strategy = $this->getMockBuilder('Lovullo\Liza\Client\MongoClientStrategy')
            ->disableOriginalConstructor()
            ->setMethods(['setDocumentOwnerId'])
            ->getMock();

        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();
        $doc_request_id      = 200007;
        $agent_entity_id     = 'AGT12345';

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $mock_return = [
            'quoteId'  => $doc_request_id,
            'hasError' => false
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('setDocumentOwnerId')
            ->with($doc_request_id, $agent_entity_id)
            ->willReturn($mock_return);

        $this->assertSame(
            $mock_return,
            $sut->setDocumentOwnerId($doc_request_id, $agent_entity_id)
        );
    }


    public function testRetrievesDocumentDataForGivenId()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_request_id   = 'REQUESTID';
        $doc_id           = 'FOO123';
        $bucket_data      = array( 'bucket' => 'data' );
        $meta_bucket_data = array( 'foo' => 'bar' );
        $document         = $this->createDummyDocument();
        $bucket           = $this->createDummyBucket();
        $meta_bucket      = $this->createDummyBucket();

        $doc_data = [
            'id'      => $doc_id,
            'content' => [
                'data' => $bucket_data,
                'meta' => $meta_bucket_data,
            ],
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('getDocumentData')
            ->with($doc_request_id)
            ->willReturn($doc_data);

        $mock_bucket_factory
            ->expects($this->at(0))
            ->method('fromData')
            ->with($bucket_data)
            ->willReturn($bucket);

        $mock_bucket_factory
            ->expects($this->at(1))
            ->method('fromData')
            ->with($meta_bucket_data)
            ->willReturn($meta_bucket);

        $mock_doc_factory
            ->expects($this->once())
            ->method('createDocument')
            ->with($doc_id, $bucket, $meta_bucket)
            ->willReturn($document);

        // this ensures that the document id that is actually used in the
        // end is the one returned by the server, not the one we provide
        $this->assertSame(
            $document,
            $sut->getDocument($doc_request_id)
        );
    }


    public function testFailsIfBucketDataNotProvided()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'content' ] = [];

        $mock_strategy
            ->method('getDocumentData')
            ->willReturn($dummy_data);

        $this->setExpectedExceptionRegexp(
            BadClientDataException::class,
            "/Invalid or missing bucket data/"
        );

        $sut->getDocument(0);
    }


    public function testFailsIfDocumentContentsNotProvided()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        // missing 'content'
        $document = [ 'id' => '' ];

        $mock_strategy
            ->method('getDocumentData')
            ->willReturn($document);

        $this->setExpectedExceptionRegexp(
            BadClientDataException::class,
            "/Invalid or missing content data/"
        );

        $sut->getDocument(0);
    }


    public function testFailsIfBucketDataNotAnArray()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'content' ][ 'data' ] = 'notarray';

        $mock_strategy
            ->method('getDocumentData')
            ->willReturn($dummy_data);

        $this->setExpectedExceptionRegexp(
            BadClientDataException::class,
            "/Invalid or missing bucket data/"
        );

        $sut->getDocument(0);
    }


    public function testFailsIfMetaBucketDataNotAnArray()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $dummy_data = $this->getDummyData();
        $dummy_data[ 'content' ][ 'data' ] = [ 'foo' => 'bar' ];
        $dummy_data[ 'content' ][ 'meta' ] = 'notarray';

        $mock_strategy
            ->method('getDocumentData')
            ->willReturn($dummy_data);

        $this->setExpectedExceptionRegexp(
            BadClientDataException::class,
            "/Invalid or missing meta data/"
        );

        $sut->getDocument(0);
    }


    public function testRetrievesProgramDataForGivenId()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_id = 'FOO123';

        $expected = [ 'foo' => 'bar' ];

        $doc_data = [
            'error' => null,
            'data' => $expected,
        ];

        $mock_strategy
            ->expects($this->once())
            ->method('getProgramData')
            ->with($doc_id)
            ->willReturn($doc_data);

        $given = $sut->getProgramData($doc_id);

        $this->assertEquals($expected, $given);
    }


    public function testFailsIfProgramDataNotAnArray()
    {
        $mock_strategy       = $this->createMockStrategy();
        $mock_doc_factory    = $this->createMockDocFactory();
        $mock_bucket_factory = $this->createMockBucketFactory();

        $sut = $this->createSut(
            $mock_strategy,
            $mock_doc_factory,
            $mock_bucket_factory
        );

        $doc_id = '345678';

        $mock_strategy
            ->expects($this->once())
            ->method('getProgramData')
            ->with($doc_id)
            ->willReturn('');

        $this->setExpectedExceptionRegexp(
            BadClientDataException::class,
            "/Invalid or missing program data/"
        );

        $sut->getProgramData($doc_id);
    }
}
