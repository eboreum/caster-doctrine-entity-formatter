<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesIdBeingMultidimensional;

use Doctrine\ORM\Mapping as ORM;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityUsingAttributesIdBeingMultidimensional\A\B;

#[ORM\Entity]
class A
{
    #[ORM\Id]
    public ?B $id = null;
}
