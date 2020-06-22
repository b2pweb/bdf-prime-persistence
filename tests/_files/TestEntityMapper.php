<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Mapper\Mapper;

class TestEntityMapper extends Mapper
{
    public function schema()
    {
        return [
            'connection' => 'test',
            'table' => 'test'
        ];
    }

    public function buildFields($builder)
    {
        $builder
            ->integer('id')->autoincrement()
            ->string('value');
    }

    public function buildRelations($builder)
    {
        $builder->on('assoc')->belongsTo(AssocEntity::class . '::value', 'value');
        $builder->on('detached')->belongsTo(AssocEntity::class . '::value', 'value')->detached();
    }
}