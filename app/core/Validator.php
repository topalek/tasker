<?php

namespace app\core;

/**
 *
 * Validator
 *
 * A simple, flexible and easy to use PHP form validation class
 * (uses a fluent interface )
 *
 *
 * typical use:
 *
 * $valid = new Validator($_POST);
 *
 * $valid->name('user_name')->required('You must choose a user name!')->alfa()->minSize(5);
 *
 * $valid->name('user_email')->required()->email();
 *
 * $valid->name('birthdate')->date('please enter date in YYYY-MM-DD format');
 *
 * if ($valid->isGroupValid())
 * echo 'Validation Passed!';
 * //////////////////////////////////////
 * OR
 *
 * $valid = new Validator($_POST);
 *
 * if (  $valid->name('user_name')->required('You must choose a user name!')->alfa()->minSize(5)
 * ->name('user_email')->required()->email()
 * ->name('birthdate')->date('please enter date in YYYY-MM-DD format')
 * ->isGroupValid() )
 * echo 'Validation passed!';
 * //////////////////////////////////////////////////////////////////
 *    On HTML
 * <form method="POST">
 *
 * <input type="text"   name="email"
 * value="<?php echo $valid->getValue('email'); ?>" />
 * <span class="error">
 * <?php echo $valid->getError('email'); ?>
 * </span>
 * ...
 * ...
 * ///////////////////////////////////////////////////////////////////
 *  To create new validation rules!
 *
 * #1 define default error message
 * private static $error_myValidaton = 'my default error message';
 *
 * #2 create new validation function
 * function myValidation($param , $errorMsg=NULL)
 * {
 * if ($this->isValid && (! empty($this->currentObj->value)))
 * {
 * //
 * //code to check if validation pass
 * //
 * $this->isValid = // TRUE or FALSE ;
 * if (! $this->isValid)
 * $this->setErrorMsg($errorMsg, self::$error_myValidation, $param);
 * }
 * return $this;
 * }
 *
 * #3 use it
 * $Valid->name('testing')->myValidation(10, 'some error msg!');
 *
 *
 */


/**
 * helper class for Validator
 */
class validationObj
{

    public $value;
    public $error;


    function __construct($value)
    {
        $this->value = $value;
        $this->error = '';
    }

}


/**
 *
 */
class Validator
{

    private static $error_required = 'This field is required';
    private static $error_date = 'Please enter a date in the YYYY-MM-DD format';
    private static $error_email = 'Please enter a valid email'; //array of validationObj
    private static $error_url = 'Please enter a valid url'; //pointer to current validationObj , set by ->name()
    //default error messages
    private static $error_alfa = 'Only letters and numbers are permited';
    private static $error_text = 'Only letters are permited';
    private static $error_minSize = 'Please enter more than %s characters';
    private static $error_maxSize = 'Please enter less than %s characters';
    private static $error_numberFloat = 'Only numbers are permitted';
    private static $error_numberInteger = 'Only numbers are permitted';
    private static $error_numberMax = 'Please enter a value lower than %s ';
    private static $error_numberMin = 'Please enter a value greater than %s ';
    private static $error_oneOf = 'Please choose one of " %s "';
    private static $error_equal = 'Fields did not match';
    private static $error_regex = 'Please choose a valid value';
    public $isValid = true;
    public $isGroupValid = true;
    public $validObjs;
    private $currentObj;
    // some regEx's
    private $pattern_email = '/^([a-zA-Z0-9_\+\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/';
    private $pattern_url = '/^((http|ftp|https):\/\/)?www\..*.\.\w\w\w?(\/.*)?(\?.*)?$/'; //check...
    ////private $pattern_alfa = '/^[a-zA-Z0-9_\-\. ]+$/';
    private $pattern_alfa = '/^(\d|\-|_|\.| |(\p{L}\p{M}*))+$/u';
    private $pattern_text = '/^( |(\p{L}\p{M}*)+$/u';
    private $pattern_numberInteger = '/^[\+\-]?[0-9]+$/';
    private $pattern_numberFloat = '/^[\+\-]?[0-9\.]+$/';
    private $pattern_date = '/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';


    /**
     *
     * @param Array $post ($Key => $value) array
     */
    function __construct($post)
    {
        foreach ($post as $key => $value) {
            $this->validObjs[$key] = new validationObj(trim($value));
        }
    }


    /**
     * Helper: returns TRUE if last valiadtion passed , else FALSE
     * @return Boolean
     */
    function isValid()
    {
        return $this->isValid;
    }


    /**
     * Helper: returns TRUE if all validations passed , else FALSE
     * @return Boolean
     */
    function isGroupValid()
    {
        return $this->isGroupValid;
    }


    /**
     * Return's $name validation error
     *
     * @param string $name
     *
     * @return string the error
     */
    function getError($name)
    {
        if (isset($this->validObjs[$name])) {
            return $this->validObjs[$name]->error;
        }

        return '';
    }


    /**
     * Returs $name value
     *
     * @param string $name
     *
     * @return string the value
     */
    function getValue($name)
    {
        if (isset($this->validObjs[$name])) {
            return $this->validObjs[$name]->value;
        }

        return '';
    }


    /**
     * Used to set starting values on Form data
     * ex: $valid->name('user_name)->setValue($database->getUserName() );
     *
     * @param string $value
     */
    function setValue($value)
    {
        $this->currentObj->value = $value;

        return $this;
    }


    /**
     *  used to set error messages out of the scope of Validator
     *  ex: $valid->name('user_name')->setError('The Name "Andre" is already taken , please try another')
     *
     * @param string $error
     */
    function setError($error)
    {
        $this->currentObj->error = $error;
        $this->isGroupValid      = false;
        $this->isValid           = false;

        return $this;
    }

    /**
     * Used to set a pointer for current validation object
     * if $name doesnt exits, it will be created with a empty value
     *        note:validation always pass on empty not required fields
     *
     * @param string $name as in array($name => 'name value')
     *
     * @return Validator
     */
    function name($name)
    {
        if ( ! isset($this->validObjs[$name])) {
            $this->validObjs[$name] = new validationObj('');
        }

        $this->isValid = true;

        $this->currentObj = &$this->validObjs[$name];

        return $this;
    }

    ////////////////////////////////////////////////
    ///////////////////////////////////////////////
    ////
    ///	    Validation Functions
    //

    /**
     * Note if field is required , then it must me called right after name!!
     * ex: $valid->name('user_name')->required()->text()->minSize(5);
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function required($errorMsg = null)
    {
        if ($this->isValid) {
            $this->isValid = ($this->currentObj->value != '') ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_required);
            }
        }

        return $this;
    }

    /**
     * PRIVATE Helper to set error messages
     *
     * @param string $errorMsg custom error message
     * @param string $default default error message
     * @param string $params extra parameter to default error message
     */
    private function setErrorMsg($errorMsg, $default, $params = null)
    {
        $this->isGroupValid = false;
        if ($errorMsg == '') {
            $this->currentObj->error = sprintf($default, $params);
        }else {
            $this->currentObj->error = $errorMsg;
        }
    }

    /**
     *  validates a Date in yyyy-mm-dd format
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function date($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_date, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_date);
            }
        }

        return $this;
    }


    /**
     * validates an email address
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function email($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_email, $this->currentObj->value) > 0) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_email);
            }
        }

        return $this;
    }


    /**
     * validates a URL address
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function url($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_url, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_url);
            }
        }

        return $this;
    }


    /**
     * ex: ->regex('/^[^<>]+$/', 'ERROR:  < and > arent valid characters')
     *
     * @param string $regex a regular expresion '/regex/'
     * @param string $errorMsg
     *
     * @return Validator
     */
    function regex($regex, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($regex, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_regex);
            }
        }

        return $this;
    }


    /**
     *  ex: ->name('password')->equal('passwordConfirm' , 'passwords didnt match')
     *
     * @param string $value2
     * @param string $errorMsg
     *
     * @return Validator
     */
    function equal($value2, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = ($value2 == $this->currentObj->value);

            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_equal);
            }
        }

        return $this;
    }


    /**
     * Ex: ->oneOf('blue:red:green' , 'only blue , red and green permited')
     * *case insensitive*
     *
     * @param string $items ex: 'blue:red:green'
     * @param string $errorMsg
     *
     * @return Validator
     */
    function oneOf($items, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $item          = explode(':', strtolower($items));
            $result        = array_intersect($item, [strtolower($this->currentObj->value)]);
            $this->isValid = ( ! empty($result));

            if ( ! $this->isValid) {
                $itemsList = str_replace(':', ' / ', $items);
                $this->setErrorMsg($errorMsg, self::$error_oneOf, $itemsList);
            }
        }

        return $this;
    }

    /////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    ////
    ///	    text validation
    //


    /**
     * Only allows A-Z a-Z and space
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function text($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_text, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_text);
            }
        }

        return $this;
    }

    function alpha($errorMsg = null)
    {
        $this->alfa($errorMsg);

        return $this;
    }

    // same function, better spelled

    /**
     * Only allows A-Z a-z 0-9 space and ( - . _ )
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function alfa($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_alfa, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_alfa);
            }
        }

        return $this;
    }

    /**
     * @param int $size the maximum string size
     * @param string $errorMsg
     *
     * @return Validator
     */
    function maxSize($size, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (strlen($this->currentObj->value) <= $size);
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_maxSize, $size);
            }
        }

        return $this;
    }


    /**
     * @param int $size the minimum string size
     * @param string $errorMsg
     *
     * @return Validator
     */
    function minSize($size, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (strlen($this->currentObj->value) >= $size);
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_minSize, $size);
            }
        }

        return $this;
    }

    /////////////////////////////////////////////////////
    ////////////////////////////////////////////////////
    ////
    ///	     Numbers validation
    //


    /**
     *  checks if its a float ( +  -  . ) permited
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function numberFloat($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_numberFloat, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_numberFloat);
            }
        }

        return $this;
    }


    /**
     *  checks if its a integer ( +  - ) permited
     *
     * @param string $errorMsg
     *
     * @return Validator
     */
    function numberInteger($errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = (preg_match($this->pattern_numberInteger, $this->currentObj->value)) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_numberInteger);
            }
        }

        return $this;
    }


    /**
     * @param number $max
     * @param string $errorMsg
     *
     * @return Validator
     */
    function numberMax($max, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = ($this->currentObj->value <= $max) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_numberMax, $max);
            }
        }

        return $this;
    }


    /**
     * @param number $min
     * @param string $errorMsg
     *
     * @return Validator
     */
    function numberMin($min, $errorMsg = null)
    {
        if ($this->isValid && ( ! empty($this->currentObj->value))) {
            $this->isValid = ($this->currentObj->value >= $min) ? true : false;
            if ( ! $this->isValid) {
                $this->setErrorMsg($errorMsg, self::$error_numberMin, $min);
            }
        }

        return $this;
    }

}


?>