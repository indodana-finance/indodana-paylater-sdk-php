<?php

namespace Indodana;

use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;

class IndodanaHttpClient
{
  public static function get($url, $header = [], $params = [])
  {
    $queryString = empty($params) ? '' : '?' . http_build_query($params);

    $request = curl_init();

    curl_setopt($request, CURLOPT_URL, $url . $queryString);
    curl_setopt($request, CURLOPT_RETURNTRANSFER, true); // Set true to return response in string
    curl_setopt($request, CURLOPT_HTTPHEADER, $header);

    $response = curl_exec($request);
    $error = curl_error($request);

    if ($error) {
      throw new IndodanaSdkException($error);
    }

    $responseCode = curl_getinfo($request, CURLINFO_HTTP_CODE);

    curl_close($request);

    $decodedResponse = json_decode($response, true);

    if ($responseCode >= 400) {
      throw new IndodanaRequestException(
        $responseCode,
        $decodedResponse
      );
    }

    return $decodedResponse;
  }

  public static function post($url, $header = [], $data = [])
  {
    $body = empty($data) ? '' : json_encode($data);

    $request = curl_init($url);

    curl_setopt($request, CURLOPT_RETURNTRANSFER, true); // Set true to return response in string
    curl_setopt($request, CURLOPT_POST, true);
    curl_setopt($request, CURLOPT_HTTPHEADER, $header);
    curl_setopt($request, CURLOPT_POSTFIELDS, $body);

    $response = curl_exec($request);
    $error = curl_error($request);

    if ($error) {
      throw new IndodanaSdkException($error);
    }

    $responseCode = curl_getinfo($request, CURLINFO_HTTP_CODE);

    curl_close($request);

    $decodedResponse = json_decode($response, true);

    if ($responseCode >= 400) {
      throw new IndodanaRequestException(
        $responseCode,
        $decodedResponse
      );
    }

    return $decodedResponse;
  }
}
