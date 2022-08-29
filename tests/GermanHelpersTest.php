<?php declare(strict_types=1);

namespace atkdatagermanextensions\tests;

use atkdatagermanextensions\GermanHelpers;
use atkdatagermanextensions\tests\testclasses\ModelWithDateTimeFields;
use traitsforatkdata\TestCase;


class GermanHelpersTest extends TestCase
{

    protected $sqlitePersistenceModels = [
        ModelWithDateTimeFields::class
    ];

    public function testArrayToGermanCommaList(): void
    {
        self::assertSame(
            'Hansi, Peter und Klaus',
            GermanHelpers::arrayToGermanCommaList(['Hansi', '', 'Peter', 'Klaus'])
        );
    }

    public function testDateCasting(): void
    {
        $testModel = new ModelWithDateTimeFields($this->getSqliteTestPersistence());
        self::assertEquals(
            (new \DateTime())->format('d.m.Y H:i:s'),
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('datetime'))
        );
        self::assertEquals(
            (new \DateTime())->format('d.m.Y'),
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('date'))
        );
        self::assertEquals(
            (new \DateTime())->format('H:i:s'),
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('time'))
        );
        self::assertEquals(
            '',
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('some_other_field'))
        );
    }

    public function testShortenTime(): void
    {
        $testModel = new ModelWithDateTimeFields($this->getSqliteTestPersistence());
        self::assertEquals(
            (new \DateTime())->format('d.m.Y H:i'),
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('datetime'), true)
        );
        self::assertEquals(
            (new \DateTime())->format('H:i'),
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('time'), true)
        );
    }

    public function testNoDateTimeInterFaceValue(): void
    {
        $testModel = new ModelWithDateTimeFields($this->getSqliteTestPersistence());
        $testModel->set('some_other_field', 'lala');
        self::assertEquals(
            'lala',
            GermanHelpers::dateTimeFieldToGermanString($testModel->getField('some_other_field'))
        );
    }
}
