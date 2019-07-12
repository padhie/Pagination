<?php

namespace Test\Service\Adapter;

use Pagination\Service\Adapter\ArrayAdapter;
use PHPUnit\Framework\TestCase;

class ArrayAdapterTest extends TestCase
{
    public function testInit(): void
    {
        try {
            new ArrayAdapter([]);
        } catch (\Throwable $throwable) {
            $this->fail();
        }

        self::assertTrue(true);
    }

    public function testGetInputData(): void
    {
        $array = ['a','b'];
        $arrayAdapter = new ArrayAdapter($array);

        self::assertSame($array, $arrayAdapter->getInputData());
    }

    public function dataProviderGetAmount(): array
    {
        return [
            [['a'], 1],
            [['a','b'], 2],
            [['q','w','e','r','t','z','u','i','o','p','ü','+','a','s','d'], 15],
        ];
    }

    /**
     * @dataProvider dataProviderGetAmount
     */
    public function testGetAmount(array $data, $expect): void
    {
        $arrayAdapter = new ArrayAdapter($data);

        self::assertSame($expect, $arrayAdapter->getAmount());
    }

    public function dataProviderApply(): array
    {
        return [
            [0, 1, ['a']],
            [3, 10, ['d','e','f','g','h','i','j']],
            [3, 3, ['d','e','f']],
        ];
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(int $offset, int $limit, array $expect): void
    {
        $array = ['a','b','c','d','e','f','g','h','i','j'];
        $arrayAdapter = new ArrayAdapter($array);

        $arrayAdapter->apply($offset, $limit);

        self::assertSame($expect, $arrayAdapter->getAppliedData());
    }
}