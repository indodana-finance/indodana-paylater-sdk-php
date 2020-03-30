<?php

namespace Indodana;

use PHPUnit\Framework\TestCase;
use Mockery;
use Indodana\Indodana;
use Indodana\IndodanaHttpClient;
use Indodana\Exceptions\IndodanaSdkException;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class IndodanaTest extends TestCase
{
  private $config = [
    'apiKey'        => 'test',
    'apiSecret'     => 'test'
  ];

  private $sampleResponse = [];
  private $sampleNonce = '1585504035';
  private $sampleSignature = '68bd864cbad49e039d1d32e23377c002b200f13662f35d88ae2e5edafd76d61a';

  protected function setUp()
  {
    $indodanaHttpClientMock = Mockery::mock('alias:Indodana\IndodanaHttpClient');
    $indodanaHttpClientMock->expects()
                           ->get(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'))
                           ->andReturn($this->sampleResponse);

    $indodanaHttpClientMock->expects()
                           ->post(Mockery::type('string'), Mockery::type('array'), Mockery::type('array'))
                           ->andReturn($this->sampleResponse);
  }

  public function testInstantiatingWithoutApiKeyThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new Indodana([
      'apiSecret'   => 'Test'
    ]);
  }

  public function testInstantiatingWithoutApiSecretThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new Indodana([
      'apiKey'      => 'Test'
    ]);
  }

  public function testInstantiatingWithoutEnvironmentReturnsSandboxBaseUrl()
  {
    $indodana = new Indodana($this->config);

    $this->assertSame(
      Indodana::SANDBOX_BASE_URL,
      $indodana->getBaseUrl()
    );
  }

  public function testInstantiatingWithProductionEnvironmentReturnsProductionBaseUrl()
  {
    $config = array_merge($this->config, [
      'environment' => Indodana::PRODUCTION_ENVIRONMENT
    ]);
    $indodana = new Indodana($config);

    $this->assertSame(
      Indodana::PRODUCTION_BASE_URL,
      $indodana->getBaseUrl()
    );
  }

  public function testInstantiatingWithSandboxEnvironmentReturnsSandboxBaseUrl()
  {
    $config = array_merge($this->config, [
      'environment' => Indodana::SANDBOX_ENVIRONMENT
    ]);
    $indodana = new Indodana($config);

    $this->assertSame(
      Indodana::SANDBOX_BASE_URL,
      $indodana->getBaseUrl()
    );
  }

  public function testGetInstallmentOptionsReturnProperResponse()
  {
    $indodana = new Indodana($this->config);

    $this->assertSame(
      $this->sampleResponse,
      $indodana->getInstallmentOptions([])
    );
  }

  public function testCheckoutReturnsProperResponse()
  {
    $indodana = new Indodana($this->config);

    $this->assertSame(
      $this->sampleResponse,
      $indodana->checkout([])
    );
  }

  public function testCheckTransactionStatusReturnsProperResponse()
  {
    $indodana = new Indodana($this->config);

    $this->assertSame(
      $this->sampleResponse,
      $indodana->checkTransactionStatus([])
    );
  }

  public function testRefundReturnsProperResponse()
  {
    $indodana = new Indodana($this->config);

    $this->assertSame(
      $this->sampleResponse,
      $indodana->refund([])
    );
  }

  public function testGetAuthTokenReturnsProperResponse()
  {
    $indodana = new Indodana($this->config);

    $authToken = $indodana->getAuthToken();

    $authTokenSeparatedByWhitespace = explode(' ', $authToken);
    $this->assertEquals(2, count($authTokenSeparatedByWhitespace));

    $authType = $authTokenSeparatedByWhitespace[0];
    $this->assertEquals('Bearer', $authType);

    $authCredentials = $authTokenSeparatedByWhitespace[1];
    $authCredentialsSeparatedByColon = explode(':', $authCredentials);
    $this->assertEquals(3, count($authCredentialsSeparatedByColon));
  }

  private function createCredentials($apiKey, $nonce, $signature)
  {
    return "${apiKey}:${nonce}:${signature}";
  }

  public function testValidateAuthCredentialsWithInvalidCredentialsReturnsFalse()
  {
    $indodana = new Indodana($this->config);

    $credentials = 'invalidCredentials';

    $this->assertFalse(
      $indodana->validateAuthCredentials($credentials)
    );
  }

  public function testValidateAuthCredentialsWithWrongApiKeyReturnFalse()
  {
    $indodana = new Indodana($this->config);

    $credentials = $this->createCredentials(
      'wrongApiKey',
      $this->sampleNonce,
      $this->sampleSignature
    );

    $this->assertFalse(
      $indodana->validateAuthCredentials($credentials)
    );
  }

  public function testValidateAuthCredentialsWithWrongSignatureReturnFalse()
  {
    $indodana = new Indodana($this->config);

    $credentials = $this->createCredentials(
      $this->config['apiKey'],
      $this->sampleNonce,
      'wrongSignature'
    );

    $this->assertFalse(
      $indodana->validateAuthCredentials($credentials)
    );
  }

  public function testValidateAuthCredentialsWithCorrectCredentialsReturnTrue()
  {
    $indodana = new Indodana($this->config);

    $credentials = $this->createCredentials(
      $this->config['apiKey'],
      $this->sampleNonce,
      $this->sampleSignature
    );

    $this->assertTrue(
      $indodana->validateAuthCredentials($credentials)
    );
  }
}
