[![CI](https://github.com/oittaa/uuid-php/actions/workflows/main.yml/badge.svg)](https://github.com/oittaa/uuid-php/actions/workflows/main.yml)
[![codecov](https://codecov.io/gh/oittaa/uuid-php/branch/master/graph/badge.svg?token=TZILVOSUKM)](https://codecov.io/gh/oittaa/uuid-php)

# uuid-php

A small PHP class for generating [RFC 4122][RFC 4122] version 3, 4, and 5 universally unique identifiers (UUID). Additionally supports [draft][draft] versions 6, 7, and 8.

If all you want is a unique ID, you should call `uuid4()`.

> Implementations SHOULD utilize UUID version 7 instead of UUID version 1 and 6 if possible.

If you're regularly generating more than thousand UUIDs per second, you might want to use `uuid8()` instead of `uuid7()`. This implementation of `uuid8()` sacrifices some entrpopy bytes of `uuid7()` to offer 100 nanosecond granularity while being otherwise compatible.

## Minimal UUID v4 implementation

Credits go to [this answer][stackoverflow uuid4] on Stackoverflow for this minimal RFC 4122 compliant solution.
```php
<?php
function uuid4()
{
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

echo uuid4();
```

## Installation

If you need comparison tools or sortable identifiers like in versions 6, 7, and 8, you might find this small and fast package useful. It doesn't require any other dependencies.

```bash
composer require oittaa/uuid
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use UUID\UUID;

// Generate a version 3 (name-based and hashed with MD5) UUID
$uuid3 = UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net');
echo $uuid3 . "\n"; // 11a38b9a-b3da-360f-9353-a5a725514269

// Generate a version 4 (random) UUID
$uuid4 = UUID::uuid4();
echo $uuid4 . "\n"; // e.g. 2140a926-4a47-465c-b622-4571ad9bb378

// Generate a version 5 (name-based and hashed with SHA1) UUID
$uuid5 = UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net');
echo $uuid5 . "\n"; // c4a760a8-dbcf-5254-a0d9-6a4474bd1b62

// Generate a version 6 (lexicographically sortable) UUID
$uuid6_first = UUID::uuid6();
echo $uuid6_first . "\n"; // e.g. 1ec9414c-232a-6b00-b3c8-9e6bdeced846
$uuid6_second = UUID::uuid6();
var_dump($uuid6_first < $uuid6_second); // bool(true)

// Generate a version 7 (lexicographically sortable) UUID
$uuid7_first = UUID::uuid7();
echo $uuid7_first . "\n"; // e.g. 017f22e2-79b0-7cc3-98c4-dc0c0c07398f
$uuid7_second = UUID::uuid7();
var_dump($uuid7_first < $uuid7_second); // bool(true)

// Generate a version 8 (lexicographically sortable) UUID
$uuid8_first = UUID::uuid8();
echo $uuid8_first . "\n"; // e.g. 017f22e2-79b0-8cc3-98c4-dc0c0c07398f
$uuid8_second = UUID::uuid8();
var_dump($uuid8_first < $uuid8_second); // bool(true)

// Test if a given string is a valid UUID
$isvalid = UUID::isValid('11a38b9a-b3da-360f-9353-a5a725514269');
var_dump($isvalid); // bool(true)

// The string standard representation of the UUID.
$tostring = UUID::toString('{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}');
var_dump($tostring); // string(36) "c4a760a8-dbcf-5254-a0d9-6a4474bd1b62"

// Test if two UUIDs are equal
$equals1 = UUID::equals(
    'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
    '2140a926-4a47-465c-b622-4571ad9bb378'
);
var_dump($equals1); // bool(false)

$equals2 = UUID::equals(
    'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
    'C4A760A8-DBCF-5254-A0D9-6A4474BD1B62'
);
var_dump($equals2); // bool(true)

$equals3 = UUID::equals(
    'urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
    '{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}'
);
var_dump($equals3); // bool(true)

// UUID comparison. Returns < 0 if uuid1 is less than uuid2;
// > 0 if uuid1 is greater than uuid2, and 0 if they are equal.
$cmp1 = UUID::cmp(
    '11a38b9a-b3da-360f-9353-a5a725514269',
    '2140a926-4a47-465c-b622-4571ad9bb378'
);
var_dump($cmp1 < 0); // bool(true)

$cmp2 = UUID::cmp(
    'c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
    '2140a926-4a47-465c-b622-4571ad9bb378'
);
var_dump($cmp2 > 0); // bool(true)

$cmp3 = UUID::cmp(
    'urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62',
    '{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}'
);
var_dump($cmp3 === 0); // bool(true)

// Extract Unix time from versions 6, 7, and 8 as a string.
$uuid6_time = UUID::getTime('1ec9414c-232a-6b00-b3c8-9e6bdeced846');
var_dump($uuid6_time); // string(18) "1645557742.0000000"
$uuid7_time = UUID::getTime('017f22e2-79b0-7cc3-98c4-dc0c0c07398f');
var_dump($uuid7_time); // string(18) "1645557742.0000000"
$uuid8_time = UUID::getTime('017f22e2-79b0-8cc3-98c4-dc0c0c07398f');
var_dump($uuid8_time); // string(18) "1645557742.0007977"

// Extract the UUID version.
$uuid_version = UUID::getVersion('2140a926-4a47-465c-b622-4571ad9bb378');
var_dump($uuid_version); // int(4)
```

## UUIDv6 Field and Bit Layout

```
        0                   1                   2                   3
        0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                           time_high                           |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |           time_mid            |      time_low_and_version     |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |clk_seq_hi_res |  clk_seq_low  |         node (0-1)            |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                         node (2-5)                            |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
```

## UUIDv7 Field and Bit Layout

```

        0                   1                   2                   3
        0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                           unix_ts_ms                          |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |          unix_ts_ms           |  ver  |       rand_a          |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |var|                        rand_b                             |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                            rand_b                             |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
```

## UUIDv8 Field and Bit Layout

```
        0                   1                   2                   3
        0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1 2 3 4 5 6 7 8 9 0 1
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                           unix_ts_ms                          |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |          unix_ts_ms           |  ver  |       subsec          |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |var|sub|                     rand                              |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
        |                             rand                              |
        +-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+
```

- `unix_ts_ms`: 48 bit big-endian unsigned number of Unix epoch timestamp with millisecond level of precision
- `ver`: The 4 bit UUIDv8 version (1000)
- `subsec`: 12 bits allocated to sub-second precision values
- `var`: 2 bit UUID variant (10)
- `sub`: 2 bits allocated to sub-second precision values
- `rand`: The remaining 60 bits are filled with pseudo-random data

14 bits dedicated to sub-second precision provide 100 nanosecond resolution. The `unix_ts_ms` and `subsec` fields guarantee the order of UUIDs generated within the same timestamp by monotonically incrementing the timer.

[RFC 4122]: http://tools.ietf.org/html/rfc4122
[draft]: https://github.com/ietf-wg-uuidrev/rfc4122bis
[stackoverflow uuid4]: https://stackoverflow.com/a/15875555
