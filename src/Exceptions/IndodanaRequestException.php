<?php

namespace Indodana\Exceptions;

class IndodanaRequestException extends \Exception
{
  private $kind;
  private $errorMessage;

  public function __construct(
    $statusCode,
    array $response = []
  ) {
    if (!is_integer($statusCode)) {
      throw new IndodanaSdkException(
        '"statusCode" argument is not integer.'
      );
    }

    $jsonResponse = json_encode($response);

    // Sanity check in case Indodana response payload is faulty
    if (!isset($response['error'])) {
      throw new IndodanaSdkException(
        "Received invalid response from Indodana. Status code: ${statusCode}. Response: ${jsonResponse}"
      );
    }

    $error = $response['error'];

    // Sanity check in case Indodana error payload is faulty
    if (
      !isset($error['kind']) ||
      !isset($error['message'])
    ) {
      throw new IndodanaSdkException(
        "Received invalid error from Indodana. Status code ${statusCode}. Response: ${jsonResponse}"
      );
    }

    $this->kind = $error['kind'];
    $this->errorMessage = $error['message'];

    $message = is_string($this->errorMessage) ?
      $this->errorMessage :
      json_encode($this->errorMessage);

    parent::__construct($message);
  }

  /**
   * @codeCoverageIgnore
   */
  public function getKind()
  {
    return $this->kind;
  }

  /**
   * @codeCoverageIgnore
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
}
