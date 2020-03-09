<?php

namespace Indodana;

use Respect\Validation\Validator;
use Indodana\IndodanaHttpClient;
use Indodana\IndodanaRequest;
use Indodana\IndodanaSecurity;
use Indodana\Exceptions\IndodanaSdkException;
use Indodana\RespectValidation\RespectValidationHelper;

class Indodana
{
  const PRODUCTION_URL = 'https://api.indodana.com/chermes';
  const SANDBOX_URL = 'https://sandbox01-api.indodana.com/chermes';

  private $apiKey;
  private $apiSecret;
  private $baseUrl;

  public function __construct(array $config = [])
  {
    $config = array_merge([
      'livemode' => false
    ], $config);

    $configValidator = Validator::create()
      ->key('apiKey', Validator::stringType()->notEmpty())
      ->key('apiSecret', Validator::stringType()->notEmpty())
      ->key('livemode', Validator::boolType(), false);

    $validationResult = RespectValidationHelper::validate($configValidator, $config);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaSdkException($validationResult->printErrorMessages());
    }

    $this->apiKey = $config['apiKey'];
    $this->apiSecret = $config['apiSecret'];
    $this->setBaseUrl($config['livemode']);
  }

  private function setBaseUrl($livemode)
  {
    $this->baseUrl = $livemode ?
      self::PRODUCTION_URL :
      self::SANDBOX_URL;
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

  public function checkTransactionStatus(array $input = [])
  {
    $url = $this->urlPath('/merchant/v1/transactions/check_status');
    $header = $this->getDefaultHeader();
    $queryParams = $input;

    $responseJson = IndodanaHttpClient::get($url, $header, $queryParams);

    return $responseJson;
  }

  public function getInstallmentOptions(array $input = [])
  {
    $url = $this->urlPath('/merchant/v1/payment_calculation');
    $header = $this->getDefaultHeader();
    $body = $input;

    $responseJson = IndodanaHttpClient::post($url, $header, $body);

    return $responseJson;
  }

  public function checkout(array $input = [])
  {
    $url = $this->urlPath('/merchant/v1/checkout_url');
    $header = $this->getDefaultHeader();
    $body = $input;

    $responseJson = IndodanaHttpClient::post($url, $header, $body);

    return $responseJson;
  }

  public function getBaseUrl()
  {
    return $this->baseUrl;
  }

  public function getAuthToken()
  {
    return IndodanaSecurity::generateBearerToken(
      $this->apiKey,
      $this->apiSecret
    );
  }

  public function validateAuthCredentials($credentials)
  {
    $credentialParts = explode(':', $credentials);

    if (count($credentialParts) !== 3) {
      return false;
    }

    $nonce = $credentialParts[1];

    $content = IndodanaSecurity::generateContent(
      $this->apiKey,
      $nonce
    );

    $signatureFromIndodana = $credentialParts[2];

    $signatureFromMerchant = IndodanaSecurity::generateSignature(
      $content,
      $this->apiSecret
    );

    return $signatureFromIndodana === $signatureFromMerchant;
  }
}
