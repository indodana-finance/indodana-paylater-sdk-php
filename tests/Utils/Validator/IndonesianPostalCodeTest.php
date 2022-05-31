<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class IndonesianPostalCodeTest extends TestCase {
    public function testValidPostalCode() {
        $input = [
            'postalCode' => '12345'
        ];

        $validationResult = Validator::create($input)
            ->key('postalCode', Validator::indonesianPostalCode());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'postalCode',
            $validationResult->getErrorMessages()
        );
    }

    public function testInvalidPostalCode() {
        $input = [
            'postalCode' => '12ab3'
        ];

        $validationResult = Validator::create($input)
            ->key('postalCode', Validator::indonesianPostalCode());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'postalCode',
            $validationResult->getErrorMessages()
        );
    }
}
