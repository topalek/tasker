<?php
/**
 * Created by topalek
 * Date: 23.05.2020
 * Time: 9:18
 */

namespace app\models;


use app\core\Model;

class Task extends Model
{
    public $id;
    public $user_name;
    public $user_email;
    public $text;
    public $status;
    public $edit;
    public $updated_at;
    public $created_at;

    public static function tableName()
    {
        return 'task';
    }


}