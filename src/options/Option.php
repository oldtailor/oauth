<?php
namespace oldtailor\oauth\options;

abstract class Option
{

    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function init($data=[])
    {
        return new static($data);
    }

    public function __get($name)
    {
        return empty($this->data[$name]) ? null : $this->data[$name];
    }
}