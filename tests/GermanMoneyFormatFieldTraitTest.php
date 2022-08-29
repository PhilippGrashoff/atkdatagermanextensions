<?php declare(strict_types=1);

namespace atkdatagermanextensions\tests;

use Atk4\Core\AtkPhpunit\TestCase;
use Atk4\Data\Model;
use Atk4\Data\Persistence;
use Atk4\Ui\Persistence\Ui;
use atkdatagermanextensions\GermanMoneyFormatFieldTrait;

class GermanMoneyFormatFieldTraitTest extends TestCase
{

    public function testLoadValueToUI()
    {
        $gmf = $this->getTestModel();
        $gmf->set('money_test', '25.25');
        $gmf->save();

        $pui = new UI();
        $pui->currency = null;
        $res = $pui->typecastSaveField($gmf->getField('money_test'), 25.25);
        self::assertEquals('25,25', $res);
        $res = $pui->typecastSaveField($gmf->getField('money_test'), 1234.56);
        self::assertEquals('1.234,56', $res);
    }

    public function testSaveValueFromUI()
    {
        $gmf = $this->getTestModel();
        $gmf->save();

        $pui = new UI();
        $res = $pui->typecastLoadField($gmf->getField('money_test'), '25,25');
        self::assertEquals(25.25, $res);
        self::assertEquals(25.25, $res);
        $res = $pui->typecastLoadField($gmf->getField('money_test'), '025,250');
        self::assertEquals(25.25, $res);
        $res = $pui->typecastLoadField($gmf->getField('money_test'), '025,2');
        self::assertEquals(25.20, $res);
        $res = $pui->typecastLoadField($gmf->getField('money_test'), '25');
        self::assertEquals(25.00, $res);

        $res = $pui->typecastLoadField($gmf->getField('money_test'), '1.234,56');
        self::assertEquals(1234.56, $res);
    }

    protected function getTestModel(): Model
    {
        $modelClass = new class() extends Model {

            use GermanMoneyFormatFieldTrait;

            public $table = 'gmf';

            protected function init(): void
            {
                parent::init();
                $this->addFields(
                    [
                        ['money_test', 'type' => 'money'],
                    ]
                );
                $this->germanPriceForMoneyField($this->getField('money_test'));
            }
        };

        return new $modelClass(new Persistence\Array_());
    }
}