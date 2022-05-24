<?php

declare(strict_types=1);

namespace TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter;

use Doctrine\ORM\Mapping as ORM;
use TestResource\Unit\Eboreum\CasterDoctrineEntityFormatter\EntityWithDebugIdentifierOnStaticPropertyOfParent\ParentClass;

/**
 * @ORM\Entity
 */
class EntityWithDebugIdentifierOnStaticPropertyOfParent extends ParentClass
{
    /**
     * @ORM\Id
     */
    public ?int $id  = null;
}