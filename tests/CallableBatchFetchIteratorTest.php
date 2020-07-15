<?php

namespace HughCube\BatchFetchIterator\Tests;

use HughCube\BatchFetchIterator\CallableBatchFetchIterator;
use HughCube\PHPUnit\VM\TestCase;

class CallableBatchFetchIteratorTest extends TestCase
{
    public function testIterator()
    {
        $iterator = new CallableBatchFetchIterator();

        $callCount = 0;
        $items = [['one' => 1], ['two' => 2], ['three' => 3], ['four' => 4]];

        $itemCount = count($items);

        $iterator->setFetchCallable(
            function () use (&$items, &$callCount) {
                $callCount++;
                return array_pop($items);
            }
        );

        $values = [];
        foreach ($iterator as $key => $value) {
            $values[$key] = $value;
        }
        $this->assertSame($callCount, $itemCount + 1);
        $this->assertSame($values, [4, 3, 2, 1]);
    }

    public function testIteratorPreserveKeys()
    {
        $callCount = 0;
        $items = [['one' => 1], ['two' => 2], ['three' => 3], ['four' => 4]];
        $itemCount = count($items);

        $iterator = new CallableBatchFetchIterator();
        $iterator->setPreserveKeys(true)->setFetchCallable(
            function () use (&$items, &$callCount) {
                $callCount++;
                return array_pop($items);
            }
        );

        $values = [];
        foreach ($iterator as $key => $value) {
            $values[$key] = $value;
        }
        $this->assertSame($callCount, $itemCount + 1);
        $this->assertSame($values, ['four' => 4, 'three' => 3, 'two' => 2, 'one' => 1]);
    }
}
