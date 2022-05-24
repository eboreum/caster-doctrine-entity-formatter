<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass\ParentClass;

#[ORM\Entity]
class EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass extends ParentClass
{
    #[DebugIdentifier]
    public string $foo = 'lorem';
}
