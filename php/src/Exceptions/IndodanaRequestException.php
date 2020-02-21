<?php

namespace Indodana\Exceptions;

class IndodanaRequestException extends \Exception {
  private $kind;
  private $errorMessage;

  public function __construct(array $error = []) {
    $kind = $error['error']['kind'];
    $errorMessage = $error['error']['message'];

    if (
      !isset($kind) &&
      !isset($errorMessage)
    ) {
      throw new IndodanaSdkException('Received invalid error from Indodana');
    }

    $this->kind = $kind;
    $this->errorMessage = $errorMessage;

    $message = is_string($this->errorMessage) ?
      $this->errorMessage :
      'See .getErrorMessage() for details';

    parent::__construct($message);
  }

  public function getKind() {
    return $this->kind;
  }

  public function getErrorMessage() {
    return $this->errorMessage;
  }
}
