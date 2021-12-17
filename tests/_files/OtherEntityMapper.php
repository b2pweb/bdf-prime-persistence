<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Mapper\Mapper;

class OtherEntityMapper extends Mapper
{
    public function schema(): array
    {
        return [
            'connection' => 'test',
            'table' => 'other'
        ];
    }

    public function buildFields($builder): void
    {
        $builder->integer('id')->autoincrement();
    }
}