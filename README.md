About
=====

This is MySQL timestamp type implementation for Doctrine ORM.
 
Problem
-------

Using the datetime data type in MySQL may cause a problem when you make a DB
connection with different [timezones][5]. Instead, the timestamp data type is
recommended to make DB server recalculate date values due to timezone.

As far as Doctrine has no type for MySQL timestamp, this lib implements it.
It generates timestamp columns when datetimetz column type is specified in
entity configuration. Also, to exclude timezone mismatch in DateTime object and
MySQL connection, unix timestamp is used to transfer DateTime data from PHP to MySQL
and vice versa. 

Installation
------------
Use [Composer][2]:
```bash
composer require ursusarctosua/doctrine-timestamp
```

Configuration
-------------

### [Doctrine][3]

Register this type with the Doctrine Type system and hook it into the database platform (see [Dotrine Custom Mapping Types][1]):

```php
<?php
use Doctrine\DBAL\Types\Type;

Type::addType('datetimetz', 'UrsusArctosUA\DoctrineTimestamp\DBAL\Types\DateTimeTzType');
$conn->getDatabasePlatform()
     ->registerDoctrineTypeMapping('mysql_datetimetz', 'datetimetz');
```

### [Symfony][4]
Register type in configuration:
```yaml
# config/packages/doctrine.yaml

doctrine:
    dbal:
        types:
            datetimetz: UrsusArctosUA\DoctrineTimestamp\DBAL\Types\DateTimeTzType
```

Usage
-----

Specify this type as a field type in mapping configuration:
```php
<?php
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity()
 */
class MyPersistentClass
{
    /**
    * @var \DateTimeInterface
    * @ORM\Column(type="datetimetz")
    */
    private $field;
}
```

Known bugs
----------

 - Doctrine schema generator generates requests this do nothing when field marked
as not null. However, it works correctly for nullable fields.

[1]: https://www.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html#custom-mapping-types
[2]: https://getcomposer.org/
[3]: https://www.doctrine-project.org/
[4]: https://symfony.com/
[5]: https://dev.mysql.com/doc/refman/8.0/en/time-zone-support.html