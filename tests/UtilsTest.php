<?php

namespace Minhyung\KoreaWeather\Tests;

use Minhyung\KoreaWeather\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testConvertMap()
    {
        $result = Utils::convertMap(126.929810, 37.488201);
        $this->assertEquals(59, $result['x']);
        $this->assertEquals(125, $result['y']);
    }
}