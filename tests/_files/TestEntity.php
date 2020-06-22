<?php

namespace Bdf\Prime\Persistence;

use Bdf\Prime\Entity\Model;

class TestEntity extends Model
{
    public $id;
    public $value;

    /** @var AssocEntity */
    public $assoc;

    /**
     * TestEntity constructor.
     *
     * @param $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
}