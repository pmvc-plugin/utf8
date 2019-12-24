<?php
namespace PMVC\PlugIn\utf8;

use PHPUnit_Framework_TestCase;

const MULTIBYTE_STRING='或許會成功';

class Utf8Test extends PHPUnit_Framework_TestCase
{
    private $_plug = 'utf8';
    function testPlugin()
    {
        ob_start();
        print_r(\PMVC\plug($this->_plug));
        $output = ob_get_contents();
        ob_end_clean();
        $this->assertContains($this->_plug,$output);
    }

    function testSubstr()
    {
      $p = \PMVC\plug($this->_plug);
      $acture = $p->substr(MULTIBYTE_STRING, 0, 1);
      $this->assertEquals('或', $acture);
      $this->assertEquals(3, strlen($acture));
    }

    function testDetectOrder()
    {
      $p = \PMVC\plug($this->_plug);
      $original = $p->detectOrder();
      $a = ['UTF-8'];
      $b = $p->detectOrder($a);
      $this->assertEquals($a, $b);
      $p->detectOrder($original);
      $this->assertEquals($original, $p->detectOrder());
    }

    function testDetect()
    {
      $p = \PMVC\plug($this->_plug);
      $s = file_get_contents(__DIR__.'/big5.txt');
      $code = $p->detectEncoding($s, ['big-5', 'utf-8']); 
      $this->assertEquals('BIG-5', $code);
    }

    function testToUtf8()
    {
      $p = \PMVC\plug($this->_plug);
      $s = file_get_contents(__DIR__.'/big5.txt');
      $new_s = $p->toUtf8($s, 'big-5');
      $code = $p->detectEncoding($new_s, ['big-5', 'utf-8']); 
      $this->assertEquals('UTF-8', $code);
    }
}
