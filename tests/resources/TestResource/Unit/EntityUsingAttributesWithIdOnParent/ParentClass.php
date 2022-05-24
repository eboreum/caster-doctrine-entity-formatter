<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesWithIdOnParent;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;

class ParentClass implements DebugIdentifierAttributeInterface
{
    #[ORM\Id]
    protected ?int $id  = null;

    #[DebugIdentifier]
    public string $foo = 'lorem';
}
