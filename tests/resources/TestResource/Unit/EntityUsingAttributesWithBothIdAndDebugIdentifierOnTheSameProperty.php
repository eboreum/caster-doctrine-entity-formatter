<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;

#[ORM\Entity]
class EntityUsingAttributesWithBothIdAndDebugIdentifierOnTheSameProperty implements DebugIdentifierAttributeInterface
{
    #[ORM\Id]
    #[DebugIdentifier]
    public ?int $id  = null;
}
