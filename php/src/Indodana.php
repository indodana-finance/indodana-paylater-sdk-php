<?php

namespace Indodana;

define ('ROOT_DIR', dirname(dirname(__FILE__)) );
define ('VENDOR_AUTOLOAD_FILE', ROOT_DIR . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

require_once VENDOR_AUTOLOAD_FILE;

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
      'isProduction' => false
    ], $config);

    $configValidator = Validator::create()
      ->key('apiKey', Validator::stringType()->notEmpty())
      ->key('apiSecret', Validator::stringType()->notEmpty())
      ->key('isProduction', Validator::boolType(), false);

    $validationResult = RespectValidationHelper::validate($configValidator, $config);

    if (!$validationResult->isSuccess()) {
      throw new IndodanaSdkException($validationResult->printErrorMessages());
    }

    $this->apiKey = $config['apiKey'];
    $this->apiSecret = $config['apiSecret'];
    $this->setBaseUrl($config['isProduction']);
  }

  private function setBaseUrl($isProduction)
  {
    $this->baseUrl = $isProduction ? self::PRODUCTION_URL : self::SANDBOX_URL;
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

  public function checkTransactionStatus(array $input = []) {
    $url = $this->urlPath('/merchant/v1/transactions/check_status');
    $header = $this->getDefaultHeader();
    $queryParams = $input;

    $responseJson = IndodanaHttpClient::get($url, $header, $queryParams);

    return $responseJson;
  }

  public function getInstallmentOptions(array $input = []) {
    $url = $this->urlPath('/merchant/v1/payment_calculation');
    $header = $this->getDefaultHeader();
    $body = $input;

    $responseJson = IndodanaHttpClient::post($url, $header, $body);

    return $responseJson['payments'];
  }

  public function checkout(array $input = []) {
    $url = $this->urlPath('/merchant/v1/checkout_url');
    $header = $this->getDefaultHeader();
    $body = $input;

    $responseJson = IndodanaHttpClient::post($url, $header, $body);

    return $responseJson;
  }


  public function validateAccessToken($accessToken)
  {
    $bearerTokenParts = explode(':', $accessToken);

    if (count($bearerTokenParts) !== 3) {
      return false;
    }

    $nonce = $bearerTokenParts[1];

    $content = IndodanaSecurity::getContent(
      $this->apiKey,
      $nonce
    );

    $signatureFromIndodana = $bearerTokenParts[2];

    $signatureFromMerchant = IndodanaSecurity::generateSignature(
      $content,
      $this->apiSecret
    );

    return $signatureFromIndodana === $signatureFromMerchant;
  }
}

$indodana = new Indodana([
  'apiKey' => 'sudahlahbossque',
  'apiSecret' => 'q123asdfae1231'
]);

print_r($indodana->validateAccessToken('SUDAHLAHHH:1582033304:779d5a619da2026c4e073b8efd71a4d3cde0037edd213605a2c15cd7f23a23d9'));
