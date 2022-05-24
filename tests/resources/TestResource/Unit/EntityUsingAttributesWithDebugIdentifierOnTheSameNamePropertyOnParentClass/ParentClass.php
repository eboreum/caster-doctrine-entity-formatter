<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithDebugIdentifierOnTheSameNamePropertyOnParentClass;

use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;

class ParentClass implements DebugIdentifierAttributeInterface
{
    #[DebugIdentifier]
    public string $foo = 'ipsum';
}
