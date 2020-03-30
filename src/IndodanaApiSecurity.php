<?php

namespace Indodana;

class IndodanaApiSecurity {
  public static function generateAuthToken($apiKey, $apiSecret)
  {
    $nonce = time();

    $content = self::generateContent(
      $apiKey,
      $nonce
    );

    $signature = self::generateSignature(
      $content,
      $apiSecret
    );

    return "Bearer ${content}:${signature}";
  }

  public static function generateContent($apiKey, $nonce)
  {
    return "${apiKey}:${nonce}";
  }

  public static function generateSignature($content, $apiSecret)
  {
    return hash_hmac('sha256', $content, $apiSecret);
  }
}
