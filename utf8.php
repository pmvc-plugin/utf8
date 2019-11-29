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

    public function internalEncoding($encoding=null) {
      $encoder = \PMVC\plug($this['encoder']);
      if (is_null($encoding)) {
        $encoding = $encoder->internalEncoding();
      } else {
        $encoding = $encoder->internalEncoding($encoding); 
      }
      return $encoding;
    }

    public function convertEncoding($val, $to_encoding, $from_encoding=null) {
      $from_encoding = $this->internalEncoding($from_encoding);
      return \PMVC\plug($this['encoder'])->convertEncoding($val, $to_encoding, $from_encoding);
    }

    public function decodeUtf16($str) {
      return \PMVC\plug($this['encoder'])->convertEncoding($val, $to_encoding, 'UTF-16LE');
    }

    public function substr($str, $start, $length=null, $encoding=null) {
      $encoding = $this->internalEncoding($encoding);
      return \PMVC\plug($this['encoder'])->substr($str, $start, $length, $encoding);
    }
}
