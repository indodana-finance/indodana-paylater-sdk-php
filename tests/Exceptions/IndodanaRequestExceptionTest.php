<?php

namespace Indodana\Exceptions;

use PHPUnit\Framework\TestCase;
use Indodana\Exceptions\IndodanaRequestException;
use Indodana\Exceptions\IndodanaSdkException;

final class IndodanaRequestExceptionTest extends TestCase
{
  public function testInstantiatingWithNonIntegerStatusCodeThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new IndodanaRequestException(
      'test'
    );
  }

  public function testInstantiatingWithoutResponseErrorThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new IndodanaRequestException(
      200,
      []
    );
  }

  public function testInstantiatingWithoutResponseErrorKindThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new IndodanaRequestException(
      200,
      [
        'error' => [
          'message' => 'test'
        ]
      ]
    );
  }

  public function testInstantiatingWithoutResponseErrorMessageThrows()
  {
    $this->expectException(IndodanaSdkException::class);

    new IndodanaRequestException(
      200,
      [
        'error' => [
          'kind' => 'test'
        ]
      ]
    );
  }

  public function testInstantiatingWithProperArgumentsWillSucceed()
  {
    $this->assertInstanceOf(
      IndodanaRequestException::class,
      new IndodanaRequestException(
        200,
        [
          'error' => [
            'kind' => 'test',
            'message' => 'test'
          ]
        ]
      )
    );
  }
}
