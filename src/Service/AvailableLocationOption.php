<?php
namespace Item\Service;

class AvailableLocationOption
{

    /**
     *
     * @var string
     */
    public $value;

    /**
     *
     * @var string
     */
    public $label;

    /**
     *
     * @var int
     */
    public $level;

    /**
     *
     * @var AvailableLocationOption[]
     */
    public $subOptions = [];

}
