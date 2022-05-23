<?php

namespace Indodana\Utils\Validator;

/**
 * @method static Validator in($haystack)
 */
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

    public static function numeric($isOptional = false)
    {
        $regexp = '/^([1-9][0-9]*)$/';

        if ($isOptional) {
            $regexp = '/^(0|[1-9][0-9]*)$/';
        }

        return function ($key) use ($regexp, $isOptional) {
            if (!array_key_exists($key, self::$input)) {
                return;
            }

            if (preg_match($regexp, self::$input[$key]) !== 1) {
                $message = "$key must be numeric";
                if (!$isOptional) {
                    $message .= " and greater than 0";
                }

                array_push(self::$errors, $message);
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
}
