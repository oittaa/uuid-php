<?php

declare(strict_types=1);

namespace UUID\Benchmark;

use UUID\UUID;

class UUIDGenerationBench
{
    public function benchUUID3Generation(): void
    {
        UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net');
    }

    public function benchUUID4Generation(): void
    {
        UUID::uuid4();
    }

    public function benchUUID5Generation(): void
    {
        UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net');
    }

    public function benchUUID6Generation(): void
    {
        UUID::uuid6();
    }
    public function benchUUID7Generation(): void
    {
        UUID::uuid7();
    }
    public function benchUUID8Generation(): void
    {
        UUID::uuid8();
    }
    public function benchUUIDToString(): void
    {
        UUID::toString('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}');
    }
}
