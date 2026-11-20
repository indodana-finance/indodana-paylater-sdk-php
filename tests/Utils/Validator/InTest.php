<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class InTest extends TestCase {
    private $haystack = ['foo', 'bar'];

    public function testValidInput() {
        $input = [
            'option' => 'foo'
        ];

        $validationResult = Validator::create($input)
            ->key('option', Validator::in($this->haystack));

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'option',
            $validationResult->getErrorMessages()
        );
    }

    public function testInvalidInput() {
        $input = [
            'option' => 'qux'
        ];

        $validationResult = Validator::create($input)
            ->key('option', Validator::in($this->haystack));

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'option',
            $validationResult->getErrorMessages()
        );
    }
}
