<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Mapper\Mapper;

class OtherEntityMapper extends Mapper
{
    public function schema()
    {
        return [
            'connection' => 'test',
            'table' => 'other'
        ];
    }

    public function buildFields($builder)
    {
        $builder->integer('id')->autoincrement();
    }
}