<?php

namespace Indodana\Exceptions;

class IndodanaRequestException extends \Exception
{
  private $kind;
  private $errorMessage;

  public function __construct(array $response = [])
  {
    if (!isset($response['error'])) {
      throw new IndodanaSdkException('Received empty error from Indodana');
    }

    $error = $response['error'];

    if (
      !isset($error['kind']) ||
      !isset($error['message'])
    ) {
      throw new IndodanaSdkException('Received invalid error from Indodana');
    }

    $this->kind = $error['kind'];
    $this->errorMessage = $error['message'];

    $message = is_string($this->errorMessage) ?
      $this->errorMessage :
      json_encode($this->errorMessage);

    parent::__construct($message);
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
}
