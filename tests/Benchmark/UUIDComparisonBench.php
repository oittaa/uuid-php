<?php

declare(strict_types=1);

namespace UUID\Benchmark;

use UUID\UUID;

class UUIDComparisonBench
{
    public function benchComparisonWithEquals(): void
    {
        UUID::cmp(
            'urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            '{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}'
        );
    }

    public function benchComparisonWithDifference(): void
    {
        UUID::cmp(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            '2140a926-4a47-465c-b622-4571ad9bb378'
        );
    }

    public function benchEquals(): void
    {
        UUID::equals(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            'C4A760A8-DBCF-5254-A0D9-6A4474BD1B62'
        );
    }

    public function benchNotEquals(): void
    {
        UUID::equals(
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
            'c4a760a8-dbcf-5254-a0d9-6a4474bd1b63'
        );
    }

    public function benchIsValid(): void
    {
        UUID::isValid('11a38b9a-b3da-360f-9353-a5a725514269');
    }

    public function benchIsNotValid(): void
    {
        UUID::isValid('11a38b9a-b3da-xxxx-9353-a5a725514269');
    }
}
