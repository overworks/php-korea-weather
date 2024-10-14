<?php

namespace Minhyung\KoreaWeather\Tests;

use Minhyung\KoreaWeather\Service;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    /** @var \Minhyung\KoreaWeather\Service */
    protected $service;

    protected function setUp(): void
    {
        $serviceKey = $_ENV['SERVICE_KEY'];
        if (! $serviceKey) {
            $this->markTestSkipped('service key not exists');
        }

        $this->service = new Service($serviceKey);
    }

    public function testUltraSrtNcst()
    {
        $result = $this->service->getUltraSrtNcst(55, 127);
        $this->assertArrayHasKey('items', $result);
    }

    public function testUltraSrtFcst()
    {
        $result = $this->service->getUltraSrtFcst(55, 127);
        $this->assertArrayHasKey('items', $result);
    }

    public function testVilageFcst()
    {
        $result = $this->service->getVilageFcst(55, 127);
        $this->assertArrayHasKey('items', $result);
    }

    public function testFcstVersion()
    {
        $result = $this->service->getFcstVersion('ODAM', '2024-10-14 08:00:00');
        $this->assertArrayHasKey('items', $result);
    }
}