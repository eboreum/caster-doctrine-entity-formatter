<?php

declare(strict_types=1);

namespace Test\Unit\Eboreum\CasterDoctrineEntityFormatter;

use PHPUnit\Framework\TestCase;

class PHPStanAtDesiredLevelTest extends TestCase
{
    public function testPHPStanIsAtDesiredLevel(): void
    {
        $directory = dir(dirname(TEST_ROOT_PATH));

        $this->assertIsObject($directory);
        assert(is_object($directory));

        $command = sprintf(
            'cd %s && php vendor/bin/phpstan 2> /dev/null',
            escapeshellarg($directory->path),
        );
        $resultCode = 0;
        $output = [];

        exec($command, $output, $resultCode);

        if (0 !== $resultCode) {
            throw new \RuntimeException('phpstan is not at the level specified in phpstan.neon');
        }

        $this->assertTrue(true);
    }
}
