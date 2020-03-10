<?php

namespace Indodana;

use Indodana\IndodanaApiSecurity;

class IndodanaRequest {
  public static function createDefaultHeader($apiKey, $apiSecret)
  {
    $authToken = IndodanaApiSecurity::generateAuthToken(
      $apiKey,
      $apiSecret
    );

    return [
      'Content-type: application/json',
      'Accept: application/json',
      "Authorization: ${authToken}"
    ];
  }
}
