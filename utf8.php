<?php

namespace PMVC\PlugIn\utf8;

use PMVC\PlugIn;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\utf8';

class utf8 extends PlugIn
{
    public function init()
    {
      if (!$this['encoder']) {
        $this['encoder'] = 'mbstring';
      }
    }

    public function internalEncoding($encoding=null, $keepDefault=false) {
      $encoder = \PMVC\plug($this['encoder']);
      $default = $encoder->internalEncoding();
      if (is_null($encoding)) {
        $encoding = $default;
      } else {
        $encoding = $encoder->internalEncoding($encoding);
        if ($keepDefault) {
          $encoder->internalEncoding($default);
        }
      }
      return $encoding;
    }

    public function convertEncoding($val, $to_encoding, $from_encoding=null) {
      $from_encoding = $this->internalEncoding($from_encoding, true);
      return \PMVC\plug($this['encoder'])->convertEncoding($val, $to_encoding, $from_encoding);
    }

    public function decodeUtf16($str, $to_encoding=null) {
      $to_encoding = $this->internalEncoding($to_encoding, true);
      return \PMVC\plug($this['encoder'])->convertEncoding($str, $to_encoding, 'UTF-16LE');
    }

    public function substr($str, $start, $length=null, $encoding=null) {
      $encoding = $this->internalEncoding($encoding, true);
      return \PMVC\plug($this['encoder'])->substr($str, $start, $length, $encoding);
    }
}
