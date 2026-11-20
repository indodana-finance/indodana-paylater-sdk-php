<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class EmailTest extends TestCase {
    public function testValidEmail() {
        $input = [
            'email' => 'foo@bar.com'
        ];

        $validationResult = Validator::create($input)
            ->key('email', Validator::email());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }

    public function testInvalidEmail() {
        $input = [
            'email' => 'invalid@email'
        ];

        $validationResult = Validator::create($input)
            ->key('email', Validator::email());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }

    public function testEmptyEmail() {
        $input = [
            'email' => ''
        ];

        $validationResult = Validator::create($input)
            ->key('email', Validator::email());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }

    public function testOmittedEmail() {
        $input = [];

        $validationResult = Validator::create($input)
            ->key('email', Validator::email());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'email',
            $validationResult->getErrorMessages()
        );
    }
}
