<?php

namespace Indodana\RespectValidation;

use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Indodana\RespectValidation\RespectValidationResult;

class RespectValidationHelper {
  public static function validate($rule, $value)
  {
    try {
      $rule->assert($value);

      return new RespectValidationResult(
        true,
        []
      );
    } catch (NestedValidationException $exception) {
      return new RespectValidationResult(
        false,
        $exception->getMessages()
      );
    }
  }
}
