<?php

namespace app\core;


class User
{
    private $_db, $_data;


    public function __construct()
    {
        $this->_db = Db::getInstance();
    }

    public function login($name = null, $password = null)
    {
        $user = $this->find($name);
        if ($user) {
            if ($this->data()->password === Security::generatePassword($password, $this->data()->salt)) {
                Session::put('user', $this->data()->name);

                return true;
            }
        }

        return false;
    }

    public function find($name)
    {
        $query       = $this->_db->get('user', ['name', '=', $name]);
        $this->_data = $query->one();
        if ($this->_data) {
            return $this->_data;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->_data;
    }

    public function create($fields = [])
    {
        if ( ! $this->_db->insert('user', $fields)) {
            Session::flash('danger', 'Ошибка создания');
            Redirect::to('/');
        }
    }


}