[![CI](https://github.com/oittaa/uuid-php/actions/workflows/main.yml/badge.svg)](https://github.com/oittaa/uuid-php/actions/workflows/main.yml)
[![codecov](https://codecov.io/gh/oittaa/uuid-php/branch/master/graph/badge.svg?token=TZILVOSUKM)](https://codecov.io/gh/oittaa/uuid-php)

# uuid-php

A small PHP class for generating [RFC 4122](http://tools.ietf.org/html/rfc4122) version 3, 4, and 5 universally unique identifiers (UUID). Additionally supports [draft](https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-02) versions 6 and 7.

If all you want is a unique ID, you should call `uuid4()`.

## Minimal UUID v4 implementation

Credits go to [this answer](https://stackoverflow.com/a/15875555) on Stackoverflow for this minimal RFC 4122 compliant solution.
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

If you need comparison tools or sortable identifiers like in versions 6 and 7, you might find this small and fast package useful. It doesn't require any other dependencies.

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
echo $uuid6_first . "\n"; // e.g. 1ebacf4f-a4a8-68ee-b4ec-618c14d005d5
$uuid6_second = UUID::uuid6();
var_dump($uuid6_first < $uuid6_second); // bool(true)

// Generate a version 7 (lexicographically sortable) UUID
$uuid7_first = UUID::uuid7();
echo $uuid7_first . "\n"; // e.g. 061a3d43-61d0-7cf4-bfce-753dadab55e1
$uuid7_second = UUID::uuid7();
var_dump($uuid7_first < $uuid7_second); // bool(true)

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

// Extract Unix time from versions 6 and 7 as a string.
$uuid6_time = UUID::getTime(UUID::uuid6());
var_dump($uuid6_time); // e.g. string(18) "1639860190.2801270"
$uuid7_time = UUID::getTime(UUID::uuid7());
var_dump($uuid7_time); // e.g. string(18) "1639860190.2801320"
```
