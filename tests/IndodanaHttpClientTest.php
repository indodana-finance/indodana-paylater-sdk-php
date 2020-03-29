<?php

namespace Indodana;

use PHPUnit\Framework\TestCase;
use Mockery;
use phpmock\mockery\PHPMockery;
use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;
use Indodana\Indodana;
use Indodana\IndodanaHttpClient;

final class IndodanaHttpClientTest extends TestCase
{
  private $url = 'test-url';

  public function tearDown()
  {
    Mockery::close();
    parent::tearDown();
  }

  public function testGetWithErrorThrows()
  {
    $response = [
      'error' => [
        'kind'  => 'test',
        'message' => 'test'
      ]
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('test error');

    $this->expectException(IndodanaSdkException::class);

    IndodanaHttpClient::get($this->url);
  }

  public function testGetWithResponseCodeGreaterEqualThan400Throws()
  {
    $response = [
      'error' => [
        'kind'  => 'test',
        'message' => 'test'
      ]
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('');
    PHPMockery::mock(__NAMESPACE__, 'curl_getinfo')->andReturn(400);

    $this->expectException(IndodanaRequestException::class);

    IndodanaHttpClient::get($this->url);
  }

  public function testGetProperlyWillSucceed()
  {
    $response = [
      'test' => 'test'
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('');
    PHPMockery::mock(__NAMESPACE__, 'curl_getinfo')->andReturn(200);

    $this->assertSame(
      $response,
      IndodanaHttpClient::get($this->url)
    );
  }

  public function testPostWithErrorThrows()
  {
    $response = [
      'error' => [
        'kind'  => 'test',
        'message' => 'test'
      ]
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('test error');

    $this->expectException(IndodanaSdkException::class);

    IndodanaHttpClient::post($this->url);
  }

  public function testPostWithResponseCodeGreaterEqualThan400Throws()
  {
    $response = [
      'error' => [
        'kind'  => 'test',
        'message' => 'test'
      ]
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('');
    PHPMockery::mock(__NAMESPACE__, 'curl_getinfo')->andReturn(400);

    $this->expectException(IndodanaRequestException::class);

    IndodanaHttpClient::post($this->url);
  }

  public function testPostProperlyWillSucceed()
  {
    $response = [
      'test' => 'test'
    ];

    PHPMockery::mock(__NAMESPACE__, 'curl_exec')->andReturn(json_encode($response));
    PHPMockery::mock(__NAMESPACE__, 'curl_error')->andReturn('');
    PHPMockery::mock(__NAMESPACE__, 'curl_getinfo')->andReturn(200);

    $this->assertSame(
      $response,
      IndodanaHttpClient::post($this->url)
    );
  }
}
