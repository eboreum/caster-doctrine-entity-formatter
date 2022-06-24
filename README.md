eboreum/caster-doctrine-entity-formatter
===============================

![license](https://img.shields.io/github/license/eboreum/caster-doctrine-entity-formatter?label=license)
[![build](https://github.com/eboreum/caster-doctrine-entity-formatter/workflows/build/badge.svg?branch=main)](https://github.com/eboreum/caster-doctrine-entity-formatter/actions)
[![Code Coverage](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/e9ca874dbc40d29add801d94ac79a663/raw/test-coverage__main.json)](https://github.com/eboreum/caster-doctrine-entity-formatter/actions)
[![PHPStan Level](https://img.shields.io/endpoint?url=https://gist.githubusercontent.com/kafoso/e9ca874dbc40d29add801d94ac79a663/raw/phpstan-level__main.json)](https://github.com/eboreum/caster-doctrine-entity-formatter/actions)

A caster formatter for Doctrine entities (see [doctrine/orm](https://packagist.org/packages/doctrine/orm)), specifically.

-----

<a name="requirements"></a>
# Requirements

```json
"php": "^8.1",
"ext-mbstring": "*",
"doctrine/annotations": "^1.0",
"doctrine/orm": "^2.0",
"eboreum/caster": "^1.0",
"eboreum/exceptional": "^1.0"
```

For more information, see the [`composer.json`](composer.json) file.

# Installation

Via [Composer](https://getcomposer.org/) (https://packagist.org/packages/eboreum/caster-doctrine-entity-formatter):

    composer require eboreum/caster-doctrine-entity-formatter

Via GitHub:

    git clone git@github.com:eboreum/caster-doctrine-entity-formatter.git

# Fundamentals

This library is a bridge between [eboreum/caster](https://packagist.org/packages/eboreum/caster) and [doctrine/orm](https://packagist.org/packages/doctrine/orm).

This library handles formatting of entity classes, which either:

 - Has the attribute `Doctrine\ORM\Mapping\Entity` (commonly written as `#[ORM\Entity]`).
 - Has the annotation `Doctrine\ORM\Mapping\Entity` (commonly written as `@ORM\Entity` in the docblock of a class).

ID properties (i.e. `Doctrine\ORM\Mapping\Id` or `@ORM\Id` as either attribute or annotation) are always identified and included. May be used with `Eboreum\Caster\Contract\DebugIdentifierAttributeInterface` and subsequently the attribute `Eboreum\Caster\Attribute\DebugIdentifier`, to provide additional information about the entity. The latter is especially useful in the following scenarios (often during debugging):

 - The entity has not yet been persisted and thus it has not yet received an ID (e.g. through auto generation). By having `#[DebugIdentifier]` on other properties, this may help providing crucial debugging information.
 - Some other, non-ID property is essential for e.g. debugging purposes.
 - Some desire to increase verbosity on an entity when it is being formatted.

For help with Doctrine annotations and/or attributes and their uses, please see:

- Attributes reference: https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/attributes-reference.html
- Annotations reference: https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/annotations-reference.html#annotations-reference

# Examples

## Basics

 **Code:**

```php
<?php

declare(strict_types=1);

namespace SomeCustomNamespace_9c95fb43;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Caster;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\CasterDoctrineEntityFormatter\EntityFormatter;

#[ORM\Entity]
class User implements DebugIdentifierAttributeInterface
{
    #[ORM\Id]
    public ?int $id = null;

    #[DebugIdentifier]
    public string $name = 'foo';
}

$user = new User();

$caster = Caster::create()->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
    new EntityFormatter(),
]));

echo $caster->cast($user) . "\n";

$user->id = 42;
$user->name = 'bar';

echo "\n";
echo $caster->cast($user) . "\n";

```

**Output:**

```
\SomeCustomNamespace_9c95fb43\User {$id = (null) null, $name = (string(3)) "foo"}

\SomeCustomNamespace_9c95fb43\User {$id = (int) 42, $name = (string(3)) "bar"}

```

## Render `DebugIdentifier` only when ID is not set

The wording "is not set" means the ID can be either uninitialized or `null`.

**Why?**

Often, when you have an ID of something, other information may end up just creating noise. This feature allows you to reduce such noise.

 **Code:**

```php
<?php

declare(strict_types=1);

namespace SomeCustomNamespace_fd813f94;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Caster;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\CasterDoctrineEntityFormatter\EntityFormatter;

#[ORM\Entity]
class User implements DebugIdentifierAttributeInterface
{
    #[ORM\Id]
    public ?int $id;

    #[DebugIdentifier]
    public string $name = 'foo';
}

$user = new User();

$entityFormatter = new EntityFormatter();
$entityFormatter = $entityFormatter->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true);

$caster = Caster::create()->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
    $entityFormatter,
]));

echo $caster->cast($user) . "\n";

$user->id = null;

echo "\n";
echo $caster->cast($user) . "\n";

$user->id = 42;

echo "\n";
echo $caster->cast($user) . "\n";

```

**Output:**

```
\SomeCustomNamespace_fd813f94\User {$id = (uninitialized), $name = (string(3)) "foo"}

\SomeCustomNamespace_fd813f94\User {$id = (null) null, $name = (string(3)) "foo"}

\SomeCustomNamespace_fd813f94\User {$id = (int) 42}

```

# License & Disclaimer

See [`LICENSE`](LICENSE) file. Basically: Use this library at your own risk.

# Contributing

We prefer that you create a ticket and/or a pull request at https://github.com/eboreum/caster-doctrine-entity-formatter, and have a discussion about a feature or bug here.

# Credits

## Authors

- **Kasper Søfren** (kafoso)<br>E-mail: <a href="mailto:soefritz@gmail.com">soefritz@gmail.com</a><br>Homepage: <a href="https://github.com/kafoso">https://github.com/kafoso</a>
- **Carsten Jørgensen** (corex)<br>E-mail: <a href="mailto:dev@corex.dk">dev@corex.dk</a><br>Homepage: <a href="https://github.com/corex">https://github.com/corex</a>
