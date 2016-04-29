<?php
/**
 * Generalized immutable key/value store
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

namespace Lovullo\Liza\Bucket;


/**
 * Generalized key/value store
 *
 * All bucket data can be thought of as a key-value store in which every value
 * is represented as an array.  All values *must* be an array, and the bucket
 * implementation should always perform that assertion.
 *
 * This should closely resemble the bucket implementation in Liza.
 */
class ImmutableBucket
    implements Bucket
{
    /**
     * Bucket data
     *
     * An array of an array of strings
     *
     * @type array
     */
    private $_data = array();


    /**
     * Initialize bucket with data
     *
     * Once initialized, the bucket data cannot be modified
     *
     * No integrity checks are performed during initialization; they are
     * deferred until an attempt is made to access an invalid element.
     *
     * @param array $data bucket data
     */
    public function __construct( array $data )
    {
        $this->_data = $data;
    }


    /**
     * Return the values associated with the provided key
     *
     * This method accepts an index due to PHP's lack of array dereferencing
     * support prior to 5.4.
     *
     * @param string $name  name of key
     * @param int    $index index to return
     *
     * @return array values associated with the key
     *
     * @throws \DomainException if requested index does not exist
     */
    public function getDataByName( $name, $index = null )
    {
        $data = ( isset( $this->_data[ $name ] ) )
            ? $this->_data[ $name ]
            : array();

        // if the data is not an array, then there is a problem somewhere
        if ( is_array( $data ) === false )
        {
            throw new \DomainException(
                sprintf( 'Data integrity failure: %s', $name )
            );
        }

        // if we have less than the required number of keys, make the probelm
        // very clear
        if ( ( $index !== null )
            && ( array_key_exists( $index, $data ) === false ) )
        {
            throw new \DomainException(
                sprintf(
                    'Requested index %d for %s does not exist; have %d values',
                    $index,
                    $name,
                    count( $data )
                )
            );
        }

        // if an index was requested, return that instead of the full set
        // (ensuring that it is properly cast to a string)
        return ( $index === null )
            ? $data
            : (string)( $data[ $index ] );
    }


    /**
     * Determine if the requested index exists for the given name
     *
     * @param string $name  name of key
     * @param int    $index index to check existence of
     *
     * @return bool whether the requested index for the given name exists
     */
    public function hasData( $name, $index = null )
    {
        if ( !array_key_exists( $name, $this->_data ) )
        {
            return false;
        }

        $data = $this->_data[ $name ];

        return ( $index !== null )
            ? isset( $data[ $index ] )
            : true;
    }
}
