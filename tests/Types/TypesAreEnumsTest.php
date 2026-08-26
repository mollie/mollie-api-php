<?php

declare(strict_types=1);

namespace Tests\Types;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Every API value set under src/Types is a string-backed enum. Only query
 * helpers and the HTTP verb list stay constant classes. Keeps UPGRADING.md
 * honest without printing a count that rots.
 */
class TypesAreEnumsTest extends TestCase
{
    private const INTENTIONAL_CLASSES = [
        'ClientQuery',
        'MandateQuery',
        'Method',
        'MethodQuery',
        'PaymentIncludesQuery',
        'PaymentQuery',
        'TerminalPairingCodeQuery',
    ];

    #[Test]
    public function every_type_file_is_a_backed_enum_unless_listed_as_intentional(): void
    {
        $files = glob(__DIR__.'/../../src/Types/*.php');
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $short = basename($file, '.php');
            $class = 'Mollie\\Api\\Types\\'.$short;

            if (in_array($short, self::INTENTIONAL_CLASSES, true)) {
                $this->assertFalse(enum_exists($class), "{$short} is listed as a constant class but is an enum; remove it from the list");

                continue;
            }

            $this->assertTrue(enum_exists($class), "{$short} must be a backed enum");
            $this->assertSame('string', (string) (new \ReflectionEnum($class))->getBackingType(), "{$short} must be string-backed");
        }
    }
}
