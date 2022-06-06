eboreum/caster-doctrine-entity-formatter
===============================

![license](https://img.shields.io/github/license/eboreum/caster-doctrine-entity-formatter?label=license)
![build](https://github.com/eboreum/caster-doctrine-entity-formatter/workflows/build/badge.svg?branch=main)
![codecov](https://codecov.io/gh/eboreum/caster-doctrine-entity-formatter/branch/main/graph/badge.svg)
%run "readme/make-readme/make-phpstan-badge.php"%

A caster formatter for Doctrine entities (see [doctrine/orm](https://packagist.org/packages/doctrine/orm)), specifically.

-----

<a name="requirements"></a>
# Requirements

%composer.json.require%

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
%include "readme/make-readme/examples/basics.php"%
```

**Output:**

```
%run "readme/make-readme/examples/basics.php"%
```

## Render `DebugIdentifier` only when ID is not set

The wording "is not set" means the ID can be either uninitialized or `null`.

**Why?**

Often, when you have an ID of something, other information may end up just creating noise. This feature allows you to reduce such noise.

 **Code:**

```php
%include "readme/make-readme/examples/render-debugidentifier-only-when-id-is-not-set.php"%
```

**Output:**

```
%run "readme/make-readme/examples/render-debugidentifier-only-when-id-is-not-set.php"%
```

# License & Disclaimer

See [`LICENSE`](LICENSE) file. Basically: Use this library at your own risk.

# Contributing

We prefer that you create a ticket and/or a pull request at https://github.com/eboreum/caster-doctrine-entity-formatter, and have a discussion about a feature or bug here.

# Credits

## Authors

%composer.json.authors%
