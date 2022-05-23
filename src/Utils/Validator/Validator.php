<?php

namespace Indodana\Utils\Validator;

class Validator
{
    private static $errors = [];

    private static $input = [];

    public function __construct($input)
    {
        self::$input = $input;
    }

    public static function create($input) {
        self::$input = [];
        self::$errors = [];

        return new Validator($input);
    }

    public function isSuccess()
    {
        return empty(self::$errors);
    }

    public function getErrorMessages()
    {
        return self::$errors;
    }

    public function printErrorMessages()
    {
        return join(", ", $this->getErrorMessages());
    }

    public function key($key, ...$validators) {
        foreach($validators as $validator) {
            $validator($key);
        }

        return $this;
    }

    public static function required() {
        $regexp = '/^(?!\s*$).+/';

        return function($key) use ($regexp) {
            if (!array_key_exists($key, self::$input)) {
                array_push(self::$errors, "$key is required");
                return;
            }

            // Array - Must contain at least 1 element
            if (is_array(self::$input[$key])) {
                if (count(self::$input[$key]) === 0) {
                    array_push(self::$errors, "$key is required, must not be empty");
                }
                return;
            }

            // Numeric - Must not be 0 (using equal operator '==' to check for float numbers as well)
            if ((is_int(self::$input[$key]) || is_float(self::$input[$key]))) {
                if (self::$input[$key] == 0) {
                    array_push(self::$errors, "$key is required, must not be 0");
                }
                return;
            }

            if (preg_match($regexp, self::$input[$key]) !== 1) {
                array_push(self::$errors, "$key is required");
            }
        };
    }

    public static function email()
    {
        return function($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (!filter_var(self::$input[$key], FILTER_VALIDATE_EMAIL)) {
                array_push(self::$errors, "$key must be valid email");
            }
        };
    }

    public static function numeric()
    {
        return function ($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            $isNumeric = is_int(self::$input[$key]) || is_float(self::$input[$key]);

            if (!$isNumeric) {
                array_push(self::$errors, "$key must be numeric");
            }
        };

    }

    public static function indonesianPostalCode()
    {
        $regexp = '/^(\d{5})$/';

        return function($key) use ($regexp) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (preg_match($regexp, self::$input[$key]) !== 1) {
                array_push(self::$errors, "$key must be valid Indonesia postal code");
            }
        };

    }

    public static function in($haystack = [])
    {
        if(count($haystack) === 0) {
            return function () {};
        }

        $regexp = '/^(' . join('|', $haystack) . ')$/';

        return function($key) use ($regexp, $haystack) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (preg_match($regexp, self::$input[$key]) !== 1) {
                array_push(self::$errors, "$key must be one of [" . join(", ", $haystack) . "]");
            }
        };
    }

    public static function domain()
    {
        return function($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            $parse = parse_url(self::$input[$key]);

            if (array_key_exists('host', $parse)) {
                array_push(self::$errors, "$key must be a valid domain");
            }
        };
    }
}
