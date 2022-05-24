<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithIdOnParent\ParentClass;

#[ORM\Entity]
class EntityUsingAttributesWithIdOnParent extends ParentClass
{
    #[DebugIdentifier]
    public string $foo = 'ipsum';

    public float $bar = 3.14;
}
