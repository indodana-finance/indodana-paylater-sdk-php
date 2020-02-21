<?php

namespace Indodana;

use Indodana\IndodanaSecurity;

class IndodanaRequest {
  public static function createDefaultHeader($apiKey, $apiSecret)
  {
    $bearerToken = IndodanaSecurity::generateBearerToken(
      $apiKey,
      $apiSecret
    );

    return [
      'Content-type: application/json',
      'Accept: application/json',
      "Authorization: Bearer $bearerToken"
    ];
  }
}
