<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityUsingAttributesWithCompositeId
{
    #[ORM\Id]
    public ?int $a  = null;

    #[ORM\Id]
    public ?int $b  = null;
}
