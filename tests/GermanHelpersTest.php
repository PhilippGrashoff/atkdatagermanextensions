<?php declare(strict_types=1);

namespace traitsforatkdata\tests;

use Atk4\Data\Model;
use Atk4\Data\Persistence;
use traitsforatkdata\TestCase;
use atk4\schema\Migration;
use traitsforatkdata\DateTimeHelpersTrait;


class GermanHelpersTest extends TestCase {


    public function testArrayToGermanCommaList(): void
    {
        $t = new Template();
        $t->app = $this->app;
        $t->loadTemplateFromString('Hallo {$DADA} Test');
        $t->setGermanList('DADA', ['Hansi', '', 'Peter', 'Klaus']);
        self::assertTrue(strpos($t->render(), 'Hansi, Peter und Klaus') !== false);
    }

    public function testDateCasting() {
        $dth = $this->getTestModel();
        self::assertEquals(
            (new \DateTime())->format('d.m.Y H:i:s'),
            $dth->castDateTimeToGermanString($dth->getField('datetime'))
        );
        self::assertEquals(
            (new \DateTime())->format('d.m.Y'),
            $dth->castDateTimeToGermanString($dth->getField('date'))
        );
        self::assertEquals(
            (new \DateTime())->format('H:i:s'),
            $dth->castDateTimeToGermanString($dth->getField('time'))
        );
        self::assertEquals(
            '',
            $dth->castDateTimeToGermanString($dth->getField('some_other_field'))
        );
    }

    public function testShortenTime() {
        $dth = $this->getTestModel();
        self::assertEquals(
            (new \DateTime())->format('d.m.Y H:i'),
            $dth->castDateTimeToGermanString($dth->getField('datetime'), true)
        );
        self::assertEquals(
            (new \DateTime())->format('H:i'),
            $dth->castDateTimeToGermanString($dth->getField('time'), true)
        );
    }

    public function testNoDateTimeInterFaceValue() {
        $dth = $this->getTestModel();
        $dth->set('some_other_field', 'lala');
        self::assertEquals(
            'lala',
            $dth->castDateTimeToGermanString($dth->getField('some_other_field'))
        );
    }

    protected function getTestModel(): Model {
        $class = new class() extends Model {

            use DateTimeHelpersTrait;

            public $table = 'some_table';

            protected function init(): void
            {
                parent::init();
                $this->addField('datetime', ['type' => 'datetime']);
                $this->addField('date', ['type' => 'date']);
                $this->addField('time', ['type' => 'time']);
                $this->addField('some_other_field');

                $this->set('datetime', new \DateTime());
                $this->set('date', new \DateTime());
                $this->set('time', new \DateTime());
            }
        };

        $persistence = Persistence::connect('sqlite::memory:');
        $model = new $class($persistence);

        return $model;
    }
}
