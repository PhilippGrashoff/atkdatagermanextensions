<?php declare(strict_types=1);

namespace atkdatagermanextensions\tests;

use Atk4\Core\AtkPhpunit\TestCase;
use atkdatagermanextensions\GermanHelpers;


class GermanHelpersTest extends TestCase
{

    public function testArrayToGermanCommaList(): void
    {
        self::assertSame(
            'Hansi, Peter und Klaus',
            GermanHelpers::arrayToGermanCommaList(['Hansi', '', 'Peter', 'Klaus'])
        );
    }
}
