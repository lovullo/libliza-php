<?php
/**
 * Bucket formatting as XML
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

namespace Lovullo\Liza\Bucket\Formatter;

use Lovullo\Liza\Bucket\Bucket;


/**
 * Builds XML from a Bucket using a convenient syntax
 *
 * This class builds XML using data provided from a bucket. The XML
 * structure is defined using a convenient array syntax:
 *   - Keys are interpreted as node names
 *     - Unless prefixed by '@', where it is then interpreted as an
 *       attribute of the parent node
 *   - A string value is assigned directly to the node as character data
 *     - Unless prefixed with ':', which will result in a bucket lookup using
 *       the remainder of the string as the bucket id
 *       - An index may optionally be provided in braces (e.g. [1]) at the end
 *         of the string (default: 0)
 *   - An array value is interpreted to be a set of sub-nodes
 *     - Unless the respective key has a '*' suffix, in wich case the array
 *       is expected to be a 0-indexed list of values which will duplicate
 *       the node name before the '*' within the key.
 *   - A closure will be evaluated and its result used as character data
 *     (like a normal non-bucket-value string).
 *     - The closure will be passed a continuation to generate additional
 *       data based on a definition.
 *     - TODO: support for merging trees
 *
 * For example:
 *   array( 'foo' => array(
 *       '@id' => ':id',
 *       'bar' => 'baz',
 *       'animals' => array(
 *           'animal*' => array(
 *               array( '@type' => ':animal[0]', 'speak' => ':speak[0]' ),
 *               array( '@type' => ':animal[1]', 'speak' => ':speak[1]' ),
 *           ),
 *       ),
 *       'apply' => function( $generate, $bucket )
 *       {
 *           // use $generate( $dfn ) for nodes
 *           return 'some result ' . $bucket->getDataByName( 'apply', 0 );
 *       }
 *   ) )
 *
 * Given the following bucket values:
 *   array(
 *     'id'    => array( 'bar' ),
 *     'type'  => array( 'cow', 'cat' ),
 *     'speak' => array( 'Moo', 'Meow' ),
 *     'apply' => array( 'applied' ),
 *   );
 *
 * Will result in the following XML:
 *   <foo id="bar">
 *     <bar>baz</bar>
 *     <animals>
 *       <animal type="cow">
 *         <speak>Moo</speak>
 *       </animal>
 *       <animal type="cat">
 *         <speak>Meow</speak>
 *       </animal>
 *       <apply>some result applied</apply>
 *     </animals>
 *   </foo>
 */
class XmlBucketFormatter
    implements BucketFormatter
{
    /**
     * Reference to bucket currently being operated upon
     * @var Bucket
     */
    private $_bucket = null;

    /**
     * Name of root node
     * @var string
     */
    private $_root_name = '';

    /**
     * XML definition
     * @var array
     */
    private $_dfn = array();


    /**
     * Initialize builder with root node
     *
     * @param array  $dfn            XML definition
     * @param string $root_node_name name of root node
     */
    public function __construct( $dfn, $root_node_name )
    {
        $this->_dfn       = $dfn;
        $this->_root_name = (string)$root_node_name;
    }


    /**
     * Build XML from the given bucket and XML structure definition
     *
     * @param Bucket $bucket bucket from which data should be retrieved
     */
    final public function format( Bucket $bucket )
    {
        // temporary (TODO: replace array_walk with array_reduce in PHP 5.4
        // when $this can be referenced)
        $this->_bucket = $bucket;

        // recursively generate the XML, beginning with the root node
        $xml = $this->_buildXml(
            $this->_dfn,
            $this->_root_name,
            null
        );

        // clear bucket reference to allow it to be GC'd and return the data
        $this->_bucket = null;

        return $xml->asXml();
    }


    /**
     * Recursively build SimpleXmlElement using arrays and string values
     *
     * @param array|string     $value node value (array indicates subnode)
     * @param string           $name  node name
     * @param SimpleXmlElement $xml xml object to manipulate
     *
     * @return SimpleXmlElement root node
     */
    private function _buildXml( $value, $name, $xml )
    {
        // arrays represent sub-nodes
        if ( is_array( $value ) )
        {
            return $this->_buildNode( $value, $name, $xml );
        }
        elseif ( $value instanceof \Closure )
        {
            // until PHP 5.4 (which supports $this in closures)
            $build = array( $this, '_buildXml' );

            $result_value = $value(
                function( array $sub_dfn ) use ( $build, $name )
                {
                    return call_user_func( $build, $sub_dfn, $name, null );
                },
                $this->_bucket
            );
        }
        else
        {
            // any other value should be assigned to the current node
            $result_value = $this->_parseValue( $value );
        }

        // @ indicates an attribute
        if ( $name[ 0 ] === '@' )
        {
            $xml[ substr( $name, 1 ) ] = $result_value;
        }
        else
        {
            // simply add the string value
            $xml->addChild( $name, htmlentities( $result_value ) );
        }

        return $xml;
    }


    /**
     * Builds duplicate nodes for the given name
     *
     * @param array|string     $value node value (array indicates subnode)
     * @param string           $name  node name
     * @param SimpleXmlElement $xml xml object to manipulate
     *
     * @return void
     */
    private function _buildDuplicateNodes( $value, $name, $xml )
    {
        // strip off * suffix
        $name  = substr( $name, 0, -1 );

        for ( $i = 0, $len = count( $value ); $i < $len; $i++ )
        {
            $node = $xml->addChild( $name );
            array_walk(
                $value[ $i ], array( $this, '_buildXml' ), $node
            );
        }

        return $xml;
    }


    /**
     * Builds a node, creating an initial root node if necessary
     *
     * @param array|string     $value node value (array indicates subnode)
     * @param string           $name  node name
     * @param SimpleXmlElement $xml xml object to manipulate
     *
     * @return void
     */
    private function _buildNode( $value, $name, $xml )
    {
        // * suffix implies multiple nodes of the same name
        if ( substr( $name, -1 ) === '*' )
        {
            return $this->_buildDuplicateNodes( $value, $name, $xml );
        }

        // if no node exists, create the root node
        if ( $xml === null )
        {
            $node = $xml = new \SimpleXmlElement( "<$name />" );
        }
        else
        {
            $node = $xml->addChild( $name );
        }

        // recurse
        array_walk( $value, array( $this, '_buildXml' ), $node );

        return $xml;
    }


    /**
     * Determines how to interpret a value
     *
     * If prefixed with a colon (:), will perform a bucket lookup
     *
     * @param mixed $value value to parse
     *
     * @return mixed bucket lookup or original value
     */
    private function _parseValue( $value )
    {
        // colon indicates bucket lookup
        if ( $value && $value[ 0 ] === ':' )
        {
            $value = $this->_bucketLookup( $value );
        }

        return $value;
    }


    /**
     * Looks up a bucket value and returns the result
     *
     * @param string $value node value
     *
     * @return string bucket value
     */
    private function _bucketLookup( $value )
    {
        preg_match( '/^:(.*?)(?:\[([0-9]+)\])?$/', $value, $data );

        $bname = $data[ 1 ];
        $index = ( isset( $data[ 2 ] ) ) ? (int)( $data[ 2 ] ) : 0;

        return $this->_bucket->getDataByName( $bname, $index );
    }
}
