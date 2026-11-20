<?php

namespace Indodana;

use Indodana\Utils\Validator\Validator;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
final class ArrayTypeTest extends TestCase {
    public function testValidArray() {
        $input = [
            'tags' => ['other', 'food', 'beverage']
        ];

        $validationResult = Validator::create($input)
            ->key('tags', Validator::isArray());

        $this->assertTrue($validationResult->isSuccess());
        $this->assertArrayNotHasKey(
            'tags',
            $validationResult->getErrorMessages()
        );
    }

    public function testInvalidArray() {
        $input = [
            'tags' => 'other food beverage',
            'products' => 12345,
            'sellers' => null
        ];

        $validationResult = Validator::create($input)
            ->key('tags', Validator::isArray())
            ->key('products', Validator::isArray())
            ->key('sellers', Validator::isArray());

        $this->assertNotTrue($validationResult->isSuccess());
        $this->assertNotEmpty($validationResult->getErrorMessages());
    }
}
