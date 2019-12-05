<?php

namespace PMVC\PlugIn\utf8;

use PMVC\PlugIn;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__.'\utf8';

class utf8 extends PlugIn
{
    private $_encoder;

    public function init()
    {
      if (!$this['encoder']) {
        $this['encoder'] = 'mbstring';
      }
      $this->setEncoder();
    }

    public function setEncoder()
    {
      $this->_encoder = \PMVC\plug($this['encoder']);
    }

    public function internalEncoding($encoding=null, $keepDefault=false) {
      $encoder = $this->_encoder;
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
      return $this->_encoder->convertEncoding($val, $to_encoding, $from_encoding);
    }

    public function decodeUtf16($str, $to_encoding=null) {
      $to_encoding = $this->internalEncoding($to_encoding, true);
      return $this->_encoder->convertEncoding($str, $to_encoding, 'UTF-16LE');
    }

    public function substr($str, $start, $length=null, $encoding=null) {
      $encoding = $this->internalEncoding($encoding, true);
      return $this->_encoder->substr($str, $start, $length, $encoding);
    }
}
