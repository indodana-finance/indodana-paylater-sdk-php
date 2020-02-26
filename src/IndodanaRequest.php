<?php

namespace Indodana;

use Indodana\IndodanaSecurity;

class IndodanaRequest {
  public static function createDefaultHeader($apiKey, $apiSecret)
  {
    $authToken = IndodanaSecurity::generateAuthToken(
      $apiKey,
      $apiSecret
    );

    return [
      'Content-type: application/json',
      'Accept: application/json',
      "Authorization: {$authToken}"
    ];
  }
}
