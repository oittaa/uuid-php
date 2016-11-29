# uuid-php

A small PHP class for generating [RFC 4122](http://tools.ietf.org/html/rfc4122) version 3, 4, and 5 universally unique identifiers (UUID).

If all you want is a unique ID, you should call `uuid4()`.

## Examples

```php
<?php
require_once 'uuid.php';

// Generate a version 3 (name-based and hashed with MD5) UUID
$uuid3 = UUID::uuid3(UUID::NAMESPACE_DNS, 'php.net');
echo $uuid3 . "\n"; // 11a38b9a-b3da-360f-9353-a5a725514269

// Generate a version 4 (random) UUID
$uuid4 = UUID::uuid4();
echo $uuid4 . "\n"; // e.g. 2140a926-4a47-465c-b622-4571ad9bb378

// Generate a version 5 (name-based and hashed with SHA1) UUID
$uuid5 = UUID::uuid5(UUID::NAMESPACE_DNS, 'php.net');
echo $uuid5 . "\n"; // c4a760a8-dbcf-5254-a0d9-6a4474bd1b62

// Test if a given string is a valid UUID
$isvalid = UUID::isValid('11a38b9a-b3da-360f-9353-a5a725514269');
var_dump($isvalid); // bool(true)

// Test if two UUIDs are equal
$equals1 = UUID::equals('c4a760a8-dbcf-5254-a0d9-6a4474bd1b62', '2140a926-4a47-465c-b622-4571ad9bb378');
var_dump($equals1); // bool(false)
$equals2 = UUID::equals('c4a760a8-dbcf-5254-a0d9-6a4474bd1b62', 'C4A760A8-DBCF-5254-A0D9-6A4474BD1B62');
var_dump($equals2); // bool(true)
$equals3 = UUID::equals('urn:uuid:c4a760a8-dbcf-5254-a0d9-6a4474bd1b62', '{C4A760A8-DBCF-5254-A0D9-6A4474BD1B62}');
var_dump($equals3); // bool(true)

```
