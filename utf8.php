<?php

namespace PMVC\PlugIn\utf8;

use PMVC\PlugIn;

// \PMVC\l(__DIR__.'/xxx.php');

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\utf8';

class utf8 extends PlugIn
{
    public function init()
    {
      if (!$this['encoder']) {
        $this['encoder'] = 'mbstring';
      }
    }

    public function substr($str, $start, $length=null, $encoding=null) {
      $encoder = \PMVC\plug($this['encoder']);
      if (is_null($encoding)) {
        $encoding = $encoder->internalEncoding(); 
      }
      return \PMVC\plug($this['encoder'])->substr($str, $start, $length, $encoding);
    }
}
