<?php


namespace app\core;


use PDO;
use PDOException;

class Db
{
    private static $_instance = null;
    protected $connection = null;
    protected $dsn;
    protected $user;
    protected $password;
    private $_pdo,
        $_query,
        $_error = false,
        $_results = [],
        $_params = [],
        $limit = [],
        $offset = [],
        $order = [],
        $_count = 0;
    private $sql;

    private function __construct()
    {
        $db = array_merge(
            require_once '_db.php',
            require_once '_db-local.php'
        );
        try {
            $this->_pdo = new PDO($db['dsn'], $db['user'], $db['password']);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public static function getInstance()
    {
        if ( ! isset(self::$_instance)) {
            self::$_instance = new Db();
        }

        return self::$_instance;
    }

    public static function escape($string)
    {
        return htmlentities($string, ENT_QUOTES, 'UTF-8');
    }

    public function get($table, $where = [])
    {
        return $this->action('select *', $table, $where);
    }

    private function action($action, $table, $where = [])
    {
        if (count($where) === 3) {
            $operators = ['=', '<', '>', '<=', '>='];
            [$field, $operator, $value] = $where;
            if (in_array($operator, $operators)) {
                $this->sql     = "{$action} from {$table} where {$field} {$operator} ?";
                $this->_params = [$field => $value];
            }
        }
        if (count($where) == 0) {
            $this->sql = "{$action} from {$table}";
        }

        return $this;
    }

    public function delete($table, $where = [])
    {
        return $this->action('delete', $table, $where);
    }

    public function insert($table, $fields)
    {
        if (count($fields)) {
            $keys   = array_keys($fields);
            $values = null;
            $x      = 1;
            foreach ($fields as $field) {
                $values .= '?';
                if ($x < count($fields)) {
                    $values .= ', ';
                }
                $x++;
            }
            $this->sql     = "INSERT INTO {$table} (`" . implode("`,`", $keys) . "`) VALUES ({$values})";
            $this->_params = $fields;

            if ( ! $this->createCommand()->error()) {
                return true;
            }
        }

        return false;
    }

    public function error()
    {
        return $this->_error;
    }

    private function createCommand()
    {
        $this->_error = false;
        if ( ! empty($this->order)) {
            $this->sql .= " order by {$this->order['field']} {$this->order['order']}";
        }
        if ( ! empty($this->limit)) {
            $this->sql .= " limit {$this->limit['limit']}";
        }
        if ( ! empty($this->offset)) {
            $this->sql .= " offset {$this->offset['offset']}";
        }

        if ($this->_query = $this->_pdo->prepare($this->sql)) {
            $x = 1;
            if (count($this->_params)) {
                foreach ($this->_params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }

            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count   = $this->_query->rowCount();
            }else {
                $this->_error = $this->_query->errorInfo();
            }
        }

        return $this;
    }

    public function update($table, $id, $fields)
    {
        $set = '';
        $x   = 1;
        foreach ($fields as $name => $value) {
            $set .= "{$name} = ?";
            if ($x < count($fields)) {
                $set .= ", ";
            }
            $x++;
        }

        $this->sql     = "UPDATE {$table} SET {$set} WHERE id = {$id}";
        $this->_params = $fields;
        if ( ! $this->createCommand()->error()) {
            return true;
        }

        return $this->createCommand()->error();

        return false;
    }

    public function count()
    {
        return $this->_count;
    }

    public function limit($limit)
    {
        $this->limit = ['limit' => $limit];

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = ['offset' => $offset];

        return $this;
    }

    public function order($field, $order = 'ASC')
    {
        $this->order = ['field' => $field, 'order' => $order];

        return $this;
    }

    public function one()
    {
        return $this->all()[0];
    }

    public function all()
    {
        return $this->createCommand()->_results;
    }

    public function getColumns($table)
    {
        return $this->query("DESCRIBE {$table}")->createCommand()->_results;
    }

    public function query($sql, $params = [])
    {
        //$this->_error = false;
        //if ($this->_query = $this->_pdo->prepare($sql)) {
        //    $x = 1;
        //    if (count($params)) {
        //        foreach ($params as $param) {
        //            $this->_query->bindValue($x, $param);
        //            $x++;
        //        }
        //    }
        //    if ($this->_query->execute()) {
        //        $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
        //        $this->_count   = $this->_query->rowCount();
        //    }else {
        //        $this->_error = true;
        //    }
        //}
        $this->sql     = $sql;
        $this->_params = $params;

        return $this;
    }
}
