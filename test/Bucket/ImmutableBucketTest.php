<?php

namespace Lovullo\Liza\Tests\Bucket;

use Lovullo\Liza\Bucket\ImmutableBucket as Sut;

class ImmutableBucketTest extends BucketTestCase
{
    /**
     * Return the bucket instance acting as the SUT
     *
     * Be sure to populate the bucket with the initial data passed to this
     * method, otherwise certain assertions will fail.
     *
     * @param array $initial_data initial data to populate bucket with
     *
     * @return ImmutableBucket
     */
    protected function getSut($initial_data)
    {
        return new Sut($initial_data);
    }
}
