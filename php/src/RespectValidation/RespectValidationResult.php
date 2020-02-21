<?php

namespace Indodana\RespectValidation;

class RespectValidationResult {
  public $success;
  private $errorMessages;

  function __construct(
    $success,
    array $errorMessages
  ) {
    $this->success = $success;
    $this->errorMessages = $errorMessages;
  }

  public function isSuccess() {
    return $this->success;
  }

  public function printErrorMessages() {
    return join(", ", $this->errorMessages);
  }
}
