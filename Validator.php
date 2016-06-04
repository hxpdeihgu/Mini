<?php
/**
 * 验证器类
 * @Created by uqiauto.com.
 * @author: hxp
 * @Date: 16/5/12
 * @Time: 上午11:55
 */
namespace Validator;

use InvalidArgumentException;
class Validator
{
    /**
     * @var string
     */
    const ERROR_DEFAULT = '无效的';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @var array
     */
    protected $_validations = array();

    /**
     * @var array
     */
    protected $_labels = array();

    /**
     * @var string
     */
    protected static $_lang;

    /**
     * @var string
     */
    protected static $_langDir;

    /**
     * @var array
     */
    protected static $_rules = array();

    /**
     * @var array
     */
    protected static $_ruleMessages = array();

    /**
     * @var array
     */
    protected $validUrlPrefixes = array('http://', 'https://', 'ftp://');

    /**
     * Setup validation
     *
     * @param  array                     $data
     * @param  array                     $fields
     * @param  string                    $lang
     * @param  string                    $langDir
     * @throws \InvalidArgumentException
     */
    public function __construct($data=array(), $fields = array(), $lang = null, $langDir = null)
    {
        //设置字段属性值
        $this->_fields = !empty($fields) ? array_intersect_key($data, array_flip($fields)) : $data;

        //设置语言
        $lang = $lang ?: static::lang();

        // 设置语言路径
        $langDir = $langDir ?: static::langDir();

        // 加载语言文件
        $langFile = rtrim($langDir, '/') . '/' . $lang . '.php';
        if (stream_resolve_include_path($langFile) ) {
            $langMessages = include $langFile;
            static::$_ruleMessages = array_merge(static::$_ruleMessages, $langMessages);
        } else {
            throw new \InvalidArgumentException("fail to load language file '$langFile'");
        }
    }

    /**
     * 设置字段
     * @param $data
     * @return [$this]
     */
    public function fields($data){
        $this->_fields = array_merge($this->_fields, $data);
        return $this;
    }

    /**
     * 加载语言
     *
     * @param  string $lang
     * @return string
     */
    public static function lang($lang = null)
    {
        if ($lang !== null) {
            static::$_lang = $lang;
        }

        return static::$_lang ?: 'config.lang';
    }

    /**
     * 获取语言地址
     *
     * @param  string $dir
     * @return string
     */
    public static function langDir($dir = null)
    {
        if ($dir !== null) {
            static::$_langDir = $dir;
        }

        return static::$_langDir ?: __DIR__ . '/';
    }

    /**
     * 验证时间是否必须
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateRequired($field, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }

    /**
     * 验证是否相等
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateEquals($field, $value, array $params)
    {
        $field2 = $params[0];

        return isset($this->_fields[$field2]) && $value == $this->_fields[$field2];
    }

    /**
     * 验证是否不同
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDifferent($field, $value, array $params)
    {
        $field2 = $params[0];

        return isset($this->_fields[$field2]) && $value != $this->_fields[$field2];
    }


    /**
     * 验证是否是数组
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateArray($field, $value)
    {
        return is_array($value);
    }

    /**
     * 验证是否是数字
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateNumeric($field, $value)
    {
        return is_numeric($value);
    }

    /**
     * 验证是否是整型
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateInteger($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_INT) !== false;
    }

    /**
     * 验证长度
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateLength($field, $value, $params)
    {
        $length = $this->stringLength($value);
        // Length between
        if (isset($params[1])) {
            return $length >= $params[0] && $length <= $params[1];
        }
        // Length same
        return ($length !== false) && $length == $params[0];
    }

    /**
     * 验证字符串长度在什么中间
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return boolean
     */
    protected function validateLengthBetween($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0] && $length <= $params[1];
    }

    /**
     * 验证字符串长度最小
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $params
     *
     * @return boolean
     */
    protected function validateLengthMin($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length >= $params[0];
    }

    /**
     * 验证字符串长度最大
     *
     * @param string $field
     * @param mixed  $value
     * @param array  $params
     *
     * @return boolean
     */
    protected function validateLengthMax($field, $value, $params)
    {
        $length = $this->stringLength($value);

        return ($length !== false) && $length <= $params[0];
    }

    /**
     * 获取字符串长度
     *
     * @param  string $value
     * @return int|false
     */
    protected function stringLength($value)
    {
        if (!is_string($value)) {
            return false;
        } elseif (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }

        return strlen($value);
    }

    /**
     * 验证一个字段的大小大于最小值
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateMin($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($params[0], $value, 14) == 1);
        } else {
            return $params[0] <= $value;
        }
    }

    /**
     * 验证一个字段的大小小于最大值
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateMax($field, $value, $params)
    {
        if (!is_numeric($value)) {
            return false;
        } elseif (function_exists('bccomp')) {
            return !(bccomp($value, $params[0], 14) == 1);
        } else {
            return $params[0] >= $value;
        }
    }

    /**
     * 验证的字段包含在一个列表值
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateIn($field, $value, $params)
    {
        $isAssoc = array_values($params[0]) !== $params[0];
        if ($isAssoc) {
            $params[0] = array_keys($params[0]);
        }

        $strict = false;
        if (isset($params[1])) {
            $strict = $params[1];
        }

        return in_array($value, $params[0], $strict);
    }

    /**
     * 验证一个字段不包含在一个值列表
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateNotIn($field, $value, $params)
    {
        return !$this->validateIn($field, $value, $params);
    }

    /**
     * 验证字段包含一个给定的字符串
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateContains($field, $value, $params)
    {
        if (!isset($params[0])) {
            return false;
        }
        if (!is_string($params[0]) || !is_string($value)) {
            return false;
        }

        return (strpos($value, $params[0]) !== false);
    }

    /**
     * 验证一个字段是一个有效的IP地址
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateIp($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_IP) !== false;
    }

    /**
     * 验证一个字段是一个有效的电子邮件地址
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateEmail($field, $value)
    {
        return filter_var($value, \FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * 验证的字段是一个有效的URL地址
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateUrl($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                return filter_var($value, \FILTER_VALIDATE_URL) !== false;
            }
        }

        return false;
    }

    /**
     * 验证一个字段是一个活跃的URL通过验证DNS记录
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateUrlActive($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                $host = parse_url(strtolower($value), PHP_URL_HOST);

                return checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA') || checkdnsrr($host, 'CNAME');
            }
        }

        return false;
    }

    /**
     * 验证一个字段只包含字母字符
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateAlpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * 验证一个字段只包含字母和数字字符
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateAlphaNum($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * 验证一个字段只包含字母数字字符,破折号,下划线
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateSlug($field, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * 验证一个字段传递一个正则表达式检查
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateRegex($field, $value, $params)
    {
        return preg_match($params[0], $value);
    }

    /**
     * 验证一个字段是一个有效的日期
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateDate($field, $value)
    {
        $isDate = false;
        if ($value instanceof \DateTime) {
            $isDate = true;
        } else {
            $isDate = strtotime($value) !== false;
        }

        return $isDate;
    }

    /**
     * 验证一个字段匹配日期格式
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateFormat($field, $value, $params)
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0 && $parsed['warning_count'] === 0;
    }

    /**
     * 验证日期是一个给定的日期之前
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateBefore($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime < $ptime;
    }

    /**
     * 验证日期是一个给定的日期之后
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @internal param array $fields
     * @return bool
     */
    protected function validateDateAfter($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);

        return $vtime > $ptime;
    }

    /**
     * 验证字段包含一个布尔值。
     *
     * @param  string $field
     * @param  mixed  $value
     * @return bool
     */
    protected function validateBoolean($field, $value)
    {
        return (is_bool($value)) ? true : false;
    }



    /**
     *  得到数组的字段和数据
     *
     * @return array
     */
    public function data()
    {
        return $this->_fields;
    }

    /**
     * 得到一系列的错误消息
     *
     * @param  null|string $field
     * @return array|bool
     */
    public function errors($field = null)
    {
        if ($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }

        return $this->_errors;
    }

    /**
     * 添加一个错误到错误消息数组
     *
     * @param string $field
     * @param string $msg
     * @param array  $params
     */
    public function error($field, $msg, array $params = array())
    {
        $msg = $this->checkAndSetLabel($field, $msg, $params);

        $values = array();
        // 打印值需要在字符串格式
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            } else {
                if (is_object($param)) {
                    $param = get_class($param);
                }
            }
            // 使用自定义标签而不是字段名称如果设置
            if (is_string($params[0])) {
                if (isset($this->_labels[$param])) {
                    $param = $this->_labels[$param];
                }
            }
            $values[] = $param;
        }

        $this->_errors[$field][] = vsprintf($msg, $values);
    }
    /**
     * 重置对象属性
     */
    public function reset()
    {
        $this->_fields = array();
        $this->_errors = array();
        $this->_validations = array();
        $this->_labels = array();
    }

    protected function getPart($data, $identifiers)
    {
        // Catches the case where the field is an array of discrete values
        if (is_array($identifiers) && count($identifiers) === 0) {
            return array($data, false);
        }

        $identifier = array_shift($identifiers);

        // Glob match
        if ($identifier === '*') {
            $values = array();
            foreach ($data as $row) {
                list($value, $multiple) = $this->getPart($row, $identifiers);
                if ($multiple) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }

            return array($values, true);
        }

        // Dead end, abort
        elseif ($identifier === NULL || ! isset($data[$identifier])) {
            return array(null, false);
        }

        // Match array element
        elseif (count($identifiers) === 0) {
            return array($data[$identifier], false);
        }

        // We need to go deeper
        else {
            return $this->getPart($data[$identifier], $identifiers);
        }
    }

    /**
     * 运行验证
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->_validations as $v) {
            foreach ($v['fields'] as $field) {
                list($values, $multiple) = $this->getPart($this->_fields, explode('.', $field));

                // Don't validate if the field is not required and the value is empty
                if ($this->hasRule('optional', $field) && isset($values)) {
                    //Continue with execution below if statement
                } elseif ($v['rule'] !== 'required' && !$this->hasRule('required', $field) && (! isset($values) || $values === '' || ($multiple && count($values) == 0))) {
                    continue;
                }

                // Callback is user-specified or assumed method on class
                if (isset(static::$_rules[$v['rule']])) {
                    $callback = static::$_rules[$v['rule']];
                } else {
                    $callback = array($this, 'validate' . ucfirst($v['rule']));
                }

                if (!$multiple) {
                    $values = array($values);
                }

                $result = true;
                foreach ($values as $value) {
                    $result = $result && call_user_func($callback, $field, $value, $v['params'], $this->_fields);
                }

                if (!$result) {
                    $this->error($field, $v['message'], $v['params']);
                }
            }
        }

        return count($this->errors()) === 0;
    }

    /**
     * 确定一个字段被给定的验证规则。
     *
     * @param  string  $name  The name of the rule
     * @param  string  $field The name of the field
     * @return boolean
     */
    protected function hasRule($name, $field)
    {
        foreach ($this->_validations as $validation) {
            if ($validation['rule'] == $name) {
                if (in_array($field, $validation['fields'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 注册新验证规则的回调函数
     *
     * @param  string                    $name
     * @param  mixed                     $callback
     * @param  string                    $message
     * @throws \InvalidArgumentException
     */
    public static function addRule($name, $callback, $message = self::ERROR_DEFAULT)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Second argument must be a valid callback. Given argument was not callable.');
        }

        static::$_rules[$name] = $callback;
        static::$_ruleMessages[$name] = $message;
    }

    /**
     * 方便来添加一个验证规则
     *
     * @param  string                    $rule
     * @param  array                     $fields
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function rule($rule, $fields,$message = '')
    {
        if (!isset(static::$_rules[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
            }
        }

        // Ensure rule has an accompanying message
        if(empty($message)){
            $message = isset(static::$_ruleMessages[$rule]) ? static::$_ruleMessages[$rule] : self::ERROR_DEFAULT;
        }


        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 3);

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array) $fields,
            'params' => (array) $params,
            'message' => '{field} ' . $message
        );

        return $this;
    }
    /**
     * @param  array  $labels
     * @return string
     */
    public function labels($labels = array())
    {
        $this->_labels = array_merge($this->_labels, $labels);

        return $this;
    }

    /**
     * @param  string $field
     * @param  string $msg
     * @param  array  $params
     * @return array
     */
    protected function checkAndSetLabel($field, $msg, $params)
    {
        if (isset($this->_labels[$field])) {
            $msg = str_replace('{field}', $this->_labels[$field], $msg);

            if (is_array($params)) {
                $i = 1;
                foreach ($params as $k => $v) {
                    $tag = '{field'. $i .'}';
                    $label = isset($params[$k]) && (is_numeric($params[$k]) || is_string($params[$k])) && isset($this->_labels[$params[$k]]) ? $this->_labels[$params[$k]] : $tag;
                    $msg = str_replace($tag, $label, $msg);
                    $i++;
                }
            }
        } else {
            $msg = str_replace('{field}', ucwords(str_replace('_', ' ', $field)), $msg);
        }

        return $msg;
    }

    /**
     * 便利方法与数组添加多个验证规则
     *
     * @param array $rules
     */
    public function rules($rules)
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, 'rule'), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }
}
