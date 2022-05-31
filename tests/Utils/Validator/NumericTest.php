<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class NumericTest extends TestCase {
    public function testValidInput() {
        $input = [
            'validInteger' => 10000,
            'validIntegerString' => '10000',
            'validFloat' => 3.14,
            'validFloatString' => '3.14'
        ];

        $validationResult = Validator::create($input)
            ->key('validInteger', Validator::numeric())
            ->key('validIntegerString', Validator::numeric())
            ->key('validFloat', Validator::numeric())
            ->key('validFloatString', Validator::numeric());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertEmpty($validationResult->getErrorMessages());
    }

    public function testInvalidInput() {
        $input = [
            'input_1' => 'not numeric',
            'input_2' => '0b10100111001',
            'input_3' => array(),
            'input_4' => '',
            'input_5' => null
        ];

        $validationResult = Validator::create($input)
            ->key('input_1', Validator::numeric())
            ->key('input_2', Validator::numeric())
            ->key('input_3', Validator::numeric())
            ->key('input_4', Validator::numeric())
            ->key('input_5', Validator::numeric());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertNotEmpty($validationResult->getErrorMessages());
    }
}
