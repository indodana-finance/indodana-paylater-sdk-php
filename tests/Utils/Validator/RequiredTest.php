<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class RequiredTest extends TestCase
{
    public function testPositiveInput()
    {
        $input = [
            'email' => 'foo@bar.com'
        ];

        $validationResult = Validator::create($input)
            ->key('email', Validator::required());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }

    public function testNegativeNullInput() {
        $input = [
            'email' => null
        ];

        $validationResult = Validator::create($input)
            ->key('email', Validator::required());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }

    public function testNegativeOmittedInput() {
        $input = [];

        $validationResult = Validator::create($input)
            ->key('email', Validator::required());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }
}
