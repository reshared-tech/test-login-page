<?php

namespace Tools;

class Validator
{
    protected $errors;

    public function email($data, $key)
    {
        if (empty($val = $this->string($data, $key))) {
            return null;
        }

        if (!filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$key] = 'Invalid email format';
        }

        return $val;
    }

    public function string($data, $key, $min = 1, $max = 255)
    {
        $val = trim($data[$key]);

        $len = strlen($val);

        if ($len === 0) {
            $this->errors[$key] = "Please input $key";
        } elseif ($len < $min) {
            $this->errors[$key] = "$key must be at least $min characters";
        } elseif ($len > $max) {
            $this->errors[$key] = "$key must be less than $max characters";
        }

        if ($this->hasError()) {
            return null;
        }

        return $val;
    }

    public function number($data, $key, $default = null)
    {
        if (isset($data[$key])) {
            return (int)$data[$key];
        }
        return $default;
    }

    public function hasError()
    {
        return !empty($this->errors);
    }

    public function errors()
    {
        return $this->errors;
    }
}