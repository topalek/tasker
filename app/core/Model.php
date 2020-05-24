<?php

namespace app\core;

class Model
{
    public $attributes;

    public function __construct()
    {
        //$this->attributes = $this->getColumns();
    }

    public function load($data)
    {
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function save()
    {
        $fields = array_filter((array)$this);

        return Db::getInstance()->insert(static::tableName(), $fields);
    }

    public static function tableName()
    {
        $table = explode('\\', get_called_class());
        $table = strtolower(end($table));

        return $table;
    }

    public function getColumns()
    {
        return Db::getInstance()->getColumns(static::tableName());
    }

}
