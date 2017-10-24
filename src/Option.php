<?php


namespace Pfs;


class Option
{
    private $label;
    private $value;

    public function __construct($label, $value)
    {
        $this->label = $label;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}