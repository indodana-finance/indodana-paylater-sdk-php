<?php

namespace Indodana;

use Respect\Validation\Validator;
use Indodana\IndodanaHttpClient;
use Indodana\IndodanaRequest;
use Indodana\IndodanaApiSecurity;
use Indodana\Exceptions\IndodanaSdkException;
use Indodana\RespectValidation\RespectValidationHelper;

class Indodana
{
  const PRODUCTION_ENVIRONMENT = 'PRODUCTION';
  const SANDBOX_ENVIRONMENT = 'SANDBOX';

  const PRODUCTION_BASE_URL = 'https://api.indodana.com/chermes/merchant';
  const SANDBOX_BASE_URL = 'https://sandbox01-api.indodana.com/chermes/merchant';

  const BASE_URL_BY_ENVIRONMENT = [
    self::PRODUCTION_ENVIRONMENT  => self::PRODUCTION_BASE_URL,
    self::SANDBOX_ENVIRONMENT     => self::SANDBOX_BASE_URL
  ];

  private $apiKey;
  private $apiSecret;
  private $baseUrl;

  public function __construct(array $config = [])
  {
    $config = array_merge([
      'environment' => self::SANDBOX_ENVIRONMENT
    ], $config);

    $configValidator = Validator::create()
      ->key('apiKey', Validator::stringType()->notEmpty())
      ->key('apiSecret', Validator::stringType()->notEmpty())
      ->key('environment', Validator::in(self::getAvailableEnvironments()));

    $validationResult = RespectValidationHelper::validate($configValidator, $config);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaSdkException($validationResult->printErrorMessages());
    }

    $this->apiKey = $config['apiKey'];
    $this->apiSecret = $config['apiSecret'];
    $this->setBaseUrl($config['environment']);
  }

  private function setBaseUrl($environment)
  {
    $this->baseUrl = self::BASE_URL_BY_ENVIRONMENT[$environment];
  }

  private function urlPath($path)
  {
    return $this->baseUrl . $path;
  }

  private function getDefaultHeader()
  {
    return IndodanaRequest::createDefaultHeader(
      $this->apiKey,
      $this->apiSecret
    );
  }

  public static function getAvailableEnvironments()
  {
    return array_keys(self::BASE_URL_BY_ENVIRONMENT);
  }

  public function getInstallmentOptions(array $input = [])
  {
    $url = $this->urlPath('/v1/payment_calculation');
    $header = $this->getDefaultHeader();
    $body = $input;

    $response = IndodanaHttpClient::post($url, $header, $body);

    return $response;
  }

  public function checkout(array $input = [])
  {
    $url = $this->urlPath('/v2/checkout_url');
    $header = $this->getDefaultHeader();
    $body = $input;

    $response = IndodanaHttpClient::post($url, $header, $body);

    return $response;
  }

  public function checkTransactionStatus(array $input = [])
  {
    $url = $this->urlPath('/v1/transactions/check_status');
    $header = $this->getDefaultHeader();
    $queryParams = $input;

    $response = IndodanaHttpClient::get($url, $header, $queryParams);

    return $response;
  }

  public function refund(array $input = [])
  {
    $url = $this->urlPath('/v2/order_cancellation');
    $header = $this->getDefaultHeader();
    $body = $input;

    $response = IndodanaHttpClient::post($url, $header, $body);

    return $response;
  }

  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  public function getAuthToken()
  {
    return IndodanaApiSecurity::generateAuthToken(
      $this->apiKey,
      $this->apiSecret
    );
  }

  public function validateAuthCredentials($clientCredentials)
  {
    $clientCredentialParts = explode(':', $clientCredentials);

    if (count($clientCredentialParts) !== 3) {
      return false;
    }

    $clientApiKey = $clientCredentialParts[0];

    if ($clientApiKey !== $this->apiKey) {
      return false;
    }

    $clientNonce = $clientCredentialParts[1];

    $content = IndodanaApiSecurity::generateContent(
      $this->apiKey,
      $clientNonce
    );

    $clientSignature = $clientCredentialParts[2];

    $correctSignature = IndodanaApiSecurity::generateSignature(
      $content,
      $this->apiSecret
    );

    return $clientSignature === $correctSignature;
  }
}
