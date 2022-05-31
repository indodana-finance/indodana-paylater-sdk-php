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
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} is required"]
                );
                return;
            }

            if (is_null(self::$input[$key])) {
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} is required"]
                );
                return;
            }

            if (is_string(self::$input[$key])) {
                if (preg_match($regexp, self::$input[$key]) !== 1) {
                    self::$errors = array_merge(
                        self::$errors,
                        [$key => "{$key} is required"]
                    );
                }
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
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be a valid email"]
                );
            }
        };
    }

    public static function numeric()
    {
        return function ($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (!is_numeric(self::$input[$key])) {
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be numeric"]
                );
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
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be valid Indonesia postal code"]
                );
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
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be one of [" . join(", ", $haystack) . "]"]
                );
            }
        };
    }

    public static function domain()
    {
        return function($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (filter_var(self::$input[$key], FILTER_VALIDATE_URL) === false) {
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be a valid URL"]
                );
            }

            $parse = parse_url(self::$input[$key]);

            if (!array_key_exists('host', $parse)) {
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must have a valid domain"]
                );
            }
        };
    }

    public static function isArray()
    {
        return function($key) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if(!is_array(self::$input[$key])) {
                self::$errors = array_merge(
                    self::$errors,
                    [$key => "{$key} must be an array"]
                );
            }
        };
    }
}
