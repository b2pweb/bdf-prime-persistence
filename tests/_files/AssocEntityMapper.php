<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Mapper\Mapper;
use Bdf\Prime\Persistence\TestEntity;

class AssocEntityMapper extends Mapper
{
    public function schema(): array
    {
        return [
            'connection' => 'test',
            'table' => 'assoc'
        ];
    }

    public function buildFields($builder): void
    {
        $builder->string('value')->primary();
    }

    public function buildRelations($builder): void
    {
        $builder->on('entities')->hasMany(TestEntity::class.'::value', 'value')->detached();
    }
}
