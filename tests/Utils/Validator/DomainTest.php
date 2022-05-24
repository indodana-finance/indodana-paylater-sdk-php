<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
final class DomainTest extends TestCase {
    public function testValidUrl() {
        $input = [
            'website' => 'https://google.com/'
        ];

        $validationResult = Validator::create($input)
            ->key('website', Validator::domain());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'website',
            $validationResult->getErrorMessages()
        );
    }

    public function testInvalidUrl() {
        $input = [
            'website' => 'store url'
        ];

        $validationResult = Validator::create($input)
            ->key('website', Validator::domain());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertArrayHasKey(
            'website',
            $validationResult->getErrorMessages()
        );
    }
}
