<?php

namespace App\tests\Service;

require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Service\RandomCatUrlGetter;
use PHPUnit\Framework\TestCase;

class RandomCatUrlGetterTest extends TestCase
{
    public function testGetUrlReturns404ImageUrlWhenConnectionToAPINotEstablished()
    {
        $service = new RandomCatUrlGetter();
        $result = $service->getUrl();
        $this->assertSame('/images/404.jpg', $result);
    }
}