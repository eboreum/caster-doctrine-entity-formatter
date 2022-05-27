<?php

declare(strict_types=1);

namespace SomeCustomNamespace_fd813f94;

use Doctrine\ORM\Mapping as ORM;
use Eboreum\Caster\Attribute\DebugIdentifier;
use Eboreum\Caster\Caster;
use Eboreum\Caster\Collection\Formatter\ObjectFormatterCollection;
use Eboreum\Caster\Contract\DebugIdentifierAttributeInterface;
use Eboreum\CasterDoctrineEntityFormatter\EntityFormatter;

require_once dirname(__DIR__, 3) . '/vendor/autoload.php'; // README.md.remove

#[ORM\Entity]
class User implements DebugIdentifierAttributeInterface
{
    #[ORM\Id]
    public ?int $id;

    #[DebugIdentifier]
    public string $name = 'foo';
}

$user = new User();

$entityFormatter = new EntityFormatter();
$entityFormatter = $entityFormatter->withIsRenderingDebugIdentifierOnlyWhenIdHasNotBeenSet(true);

$caster = Caster::create()->withCustomObjectFormatterCollection(new ObjectFormatterCollection([
    $entityFormatter,
]));

echo $caster->cast($user) . "\n";

$user->id = null;

echo "\n";
echo $caster->cast($user) . "\n";

$user->id = 42;

echo "\n";
echo $caster->cast($user) . "\n";
