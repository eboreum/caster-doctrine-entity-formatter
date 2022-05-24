<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesIdBeingMultidimensional\A;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class B
{
    #[ORM\Id]
    public ?int $id = null;
}
