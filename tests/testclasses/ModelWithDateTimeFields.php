<?php declare(strict_types=1);

namespace atkdatagermanextensions\tests\testclasses;

use Atk4\Data\Model;

class ModelWithDateTimeFields extends Model
{
    public $table = 'some_table';

    protected function init(): void
    {
        parent::init();
        $this->addField(
            'datetime',
            ['type' => 'datetime']
        );
        $this->addField(
            'date',
            ['type' => 'date']
        );
        $this->addField(
            'time',
            ['type' => 'time']
        );
        $this->addField(
            'some_other_field',
            ['type' => 'string']
        );

        $this->set('datetime', new \DateTime());
        $this->set('date', new \DateTime());
        $this->set('time', new \DateTime());
    }
}
