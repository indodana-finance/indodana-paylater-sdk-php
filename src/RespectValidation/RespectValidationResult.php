<?php

namespace Indodana\RespectValidation;

class RespectValidationResult
{
  private $success;
  private $errorMessages;

  function __construct(
    $success,
    array $errorMessages
  ) {
    $this->success = $success;
    $this->errorMessages = $errorMessages;
  }

  public function isSuccess()
  {
    return $this->success;
  }

  public function getErrorMesssages()
  {
    return $this->errorMessages;
  }

  public function printErrorMessages()
  {
    return join(", ", $this->getErrorMesssages());
  }
}
