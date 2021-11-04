<?php

declare(strict_types=1);

namespace UUID\Benchmark;

use UUID\UUID;

class UUIDGenerationBench
{
    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchUUID3Generation(): void
    {
        UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchUUID4Generation(): void
    {
        UUID::uuid4();
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchUUID5Generation(): void
    {
        UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net');
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchUUID6Generation(): void
    {
        UUID::uuid6();
    }
}
