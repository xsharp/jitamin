<?php

/*
 * This file is part of Jitamin.
 *
 * Copyright (C) Jitamin Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../Base.php';

use Jitamin\Foundation\Csv;

class CsvTest extends Base
{
    public function testGetBooleanValue()
    {
        $this->assertEquals(1, Csv::getBooleanValue('1'));
        $this->assertEquals(1, Csv::getBooleanValue('True'));
        $this->assertEquals(1, Csv::getBooleanValue('t'));
        $this->assertEquals(1, Csv::getBooleanValue('TRUE'));
        $this->assertEquals(1, Csv::getBooleanValue('true'));
        $this->assertEquals(1, Csv::getBooleanValue('T'));
        $this->assertEquals(1, Csv::getBooleanValue('Y'));
        $this->assertEquals(1, Csv::getBooleanValue('y'));
        $this->assertEquals(1, Csv::getBooleanValue('yes'));
        $this->assertEquals(1, Csv::getBooleanValue('Yes'));

        $this->assertEquals(0, Csv::getBooleanValue('0'));
        $this->assertEquals(0, Csv::getBooleanValue('123'));
        $this->assertEquals(0, Csv::getBooleanValue('anything'));
    }

    public function testGetEnclosures()
    {
        $this->assertCount(3, Csv::getEnclosures());
        $this->assertCount(4, Csv::getDelimiters());
    }

    public function testReadWrite()
    {
        $filename = tempnam(sys_get_temp_dir(), 'UT');
        $rows = [
            ['Column A', 'Column B'],
            ['value a', 'value b'],
        ];

        $csv = new Csv();
        $csv->write($filename, $rows);
        $csv->setColumnMapping(['A', 'B', 'C']);
        $csv->read($filename, [$this, 'readRow']);

        unlink($filename);

        $this->expectOutputString('"Column A","Column B"'."\n".'"value a","value b"'."\n", $csv->output($rows));
    }

    public function readRow(array $row, $line)
    {
        $this->assertEquals(['value a', 'value b', ''], $row);
        $this->assertEquals(1, $line);
    }
}
