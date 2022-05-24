eboreum/caster-doctrine-entity-formatter
===============================

A caster formatter for Doctrine entities (see [doctrine/orm](https://packagist.org/packages/doctrine/orm)), specifically.

This library is a bridge between [eboreum/caster](https://packagist.org/packages/eboreum/caster) and [doctrine/orm](https://packagist.org/packages/doctrine/orm).

This library handles formatting of classes (entities), which either:

 - Has the attribute `Doctrine\ORM\Mapping\Entity` (commonly written as `#[ORM\Entity]`).
 - Has the annotation `Doctrine\ORM\Mapping\Entity` (commonly written as `@ORM\Entity` in the docblock of a class).

ID properties (i.e. `Doctrine\ORM\Mapping\Id` or `@ORM\Id` as either attribute or annotation) are always identified and included. May be used with `Eboreum\Caster\Contract\DebugIdentifierAttributeInterface` and subsequently the attribute `Eboreum\Caster\Attribute\DebugIdentifier`, to provide additional information about the entity. The latter is especially useful in the following scenarios (often during debugging):

 - The entity has not yet been persisted and thus it has not yet received an ID (e.g. through auto generation). By having `#[DebugIdentifier]` on other properties, this may help providing crucial debugging information.
 - Some other, non-ID property is essential for e.g. debugging purposes.
 - Some desire to increase verbosity on an entity when it is being formatted.