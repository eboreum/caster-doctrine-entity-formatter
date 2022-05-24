<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EntityUsingAttributesWithUninitializedId
{
    #[ORM\Id]
    public ?int $id;
}
