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
                session_regenerate_id();
                $sid = session_id();
                $this->_db->update('user', $this->data()->id, ['sid' => $sid]);
                Session::put('user', ['id' => $this->data()->id, 'sid' => $sid, 'name' => $this->data()->name]);

                return true;
            }
        }

        return false;
    }

    public function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? 'id' : 'name';
            $query = $this->_db->get('user', [$field, '=', $user]);
            $this->_data = $query->one();
            if ($this->_data) {
                return $this;
            }
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
