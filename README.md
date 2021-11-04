[![CI](https://github.com/oittaa/uuid-php/actions/workflows/main.yml/badge.svg)](https://github.com/oittaa/uuid-php/actions/workflows/main.yml)
[![codecov](https://codecov.io/gh/oittaa/uuid-php/branch/master/graph/badge.svg?token=TZILVOSUKM)](https://codecov.io/gh/oittaa/uuid-php)

# uuid-php

A small PHP class for generating [RFC 4122](http://tools.ietf.org/html/rfc4122) version 3, 4, 5, and 6 ([draft](https://datatracker.ietf.org/doc/html/draft-peabody-dispatch-new-uuid-format-02)) universally unique identifiers (UUID).

If all you want is a unique ID, you should call `uuid4()`.

## Installation

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
```
