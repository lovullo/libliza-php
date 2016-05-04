<?php
/**
 * Tests bucket formatting as XML
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

namespace Lovullo\Liza\Tests\Bucket\Formatter;

use Lovullo\Liza\Bucket\Formatter\XmlBucketFormatter as Sut;


class XmlBucketFormatterTest
    extends BucketFormatterTestCase
{
    /**
     * Test name for root node
     * @var string
     */
    private $_root_node_name = 'test';

    /**
     * Data to populate bucket with
     * @var array
     */
    private $_bucket_data = array(
        'foo' => array( 'bar', 'baz' ),
        'bar' => array( 'foo', 'foobaz' ),

        'foos' => array( 'a', 'b' ),
    );

    /**
     * Default XML definition for tests
     * @var array
     */
    private $_default_dfn = array(
        '@root'  => 'foo',
        '@bval'  => ':foo',
        '@bval1' => ':foo[1]',

        'bval'  => ':bar',
        'bval1' => ':bar[1]',

        // test entity escape
        'esc' => 'M&T',

        'val' => 'moo',
        'sub' => array(
            '@subattr' => 'bar',
            'val'      => 'poo',
        ),

        'foo*' => array(
            array( 'val' => ':foos' ),
            array( 'val' => ':foos[1]' ),
        ),
    );


    protected function createCaseSut()
    {
        return new Sut( array(), 'foo' );
    }


    protected function getXml( array $dfn = null, $bucket = null )
    {
        $dfn    = ( $dfn ) ?: $this->_default_dfn;
        $bucket = $bucket ?: $this->getMockBucket( $this->_bucket_data );

        $sut = new Sut( $dfn, $this->_root_node_name );

        // returns string, so translate back into a SimpleXMLElement that we
        // can query
        return simplexml_load_string(
            $sut->format( $bucket )
        );
    }


    protected function assertNodes( array $asserts, $xml = null )
    {
        $use_xml = $xml ?: $this->getXml();

        foreach ( $asserts as $xpath => $expected )
        {
            $value = '';

            // honor custom nodes
            $data  = ( $xml !== null )
                ? $use_xml->xpath( $xpath )
                : $use_xml->xpath( "/$this->_root_node_name/$xpath" );

            // PHP is obnoxious in that it will not return the value of an
            // attribute directly, even if it's explicitly requested within an
            // XPath query
            if ( preg_match( '/\/@(.*)$/', $xpath, $match ) )
            {
                // prior to array dereferencing support in PHP
                $value = (string)( $data[ 0 ][ $match[ 1 ] ] );
            }
            else
            {
                $value = (string)( $data[ 0 ] );
            }

            $this->assertEquals( $expected, $value );
        }
    }


    public function testReturnsGivenRootNode()
    {
        $result = $this->getXml();

        $this->assertEquals( 1,
            count( $result->xpath( '/' . $this->_root_node_name ) ),
            'Should generate correct root node'
        );
    }


    public function testCanAddAttributes()
    {
        $this->assertNodes( array(
            '/@root'        => $this->_default_dfn['@root'],
            '/sub/@subattr' => $this->_default_dfn['sub']['@subattr'],
        ) );
    }


    public function testCanUseBucketValuesForAttributes()
    {
        $this->assertNodes( array(
            '/@bval'  => $this->_bucket_data[ 'foo' ][ 0 ],
            '/@bval1' => $this->_bucket_data[ 'foo' ][ 1 ],
        ) );
    }


    public function testCanAddNodes()
    {
        $this->assertNodes( array(
            '/val' => $this->_default_dfn['val'],
            '/sub/val' => $this->_default_dfn['sub']['val'],
        ) );
    }


    public function testCanUseBucketValuesForNodes()
    {
        $this->assertNodes( array(
            '/@bval'  => $this->_bucket_data[ 'foo' ][ 0 ],
            '/@bval1' => $this->_bucket_data[ 'foo' ][ 1 ],
        ) );
    }


    public function testCanDuplicateNodes()
    {
        $this->assertNodes( array(
            '/foo[1]/val' => $this->_bucket_data[ 'foos' ][ 0 ],
            '/foo[2]/val' => $this->_bucket_data[ 'foos' ][ 1 ],
        ) );
    }


    public function testAmpersandsAreNotAnIssue()
    {
        $this->assertNodes( array(
            '/esc' => $this->_default_dfn['esc'],
        ) );
    }


    public function testAppliesFunctionsAndUsesResult()
    {
        $gen_data = array(
            'cow' => array(
                'moo' => 'graze',
            ),
        );

        $bucket = $this->getMockBucket( array() );

        $given_gen_result = null;
        $given_bucket     = null;
        $expected         = 'quux';
        $expected_attr    = 'quuux_attr';

        $result = $this->getXml( array(
            'foo' => array(
                'apply' => function( \Closure $generate, $bucket )
                    use ( $gen_data, $expected, &$given_gen_result )
                {
                    // will be a SimpleXmlElement
                    $given_gen_result = $generate( $gen_data );

                    return $expected;
                },
                '@apply' => function( \Closure $generate, $bucket )
                    use ( $expected_attr, &$given_bucket )
                {
                    $given_bucket = $bucket;

                    return $expected_attr;
                },
            ),
        ), $bucket );

        // overall result
        $this->assertNodes(
            array(
                './foo/apply'  => $expected,
                './foo/@apply' => $expected_attr,
            ),
            $result
        );

        // generated nodes within callback
        $this->assertNodes(
            array(
                'cow/moo' => 'graze',
            ),
            $given_gen_result
        );

        // we should have been given an actual bucket reference
        $this->assertSame( $bucket, $given_bucket );
    }
}
