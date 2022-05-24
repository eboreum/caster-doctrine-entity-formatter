<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesIdBeingMultidimensional\A;

#[ORM\Entity]
class EntityUsingAttributesIdBeingMultidimensional
{
    #[ORM\Id]
    public ?A $id = null;
}
