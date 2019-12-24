<?php

namespace PMVC\PlugIn\utf8;

use PMVC\PlugIn;

${_INIT_CONFIG
}[_CLASS] = __NAMESPACE__.'\utf8';

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

    public function toUtf8($val)
    {
        if (\PMVC\isArray($val)) {
            $myval = \PMVC\get($val);
            array_walk_recursive(
                $myval, function (&$item) {
                    $item = $this->convertEncoding($item, 'utf-8');
                }
            );
            return $myval;
        } elseif (is_string($val) ) {
            return $this->convertEncoding($val, 'utf-8');
        } elseif (is_numeric($val) ) { 
            return $val;
        } else {
            return $this->convertEncoding(trim(var_export($val, true)), 'utf-8');
        }
    }

    public function detectOrder($encoding_list = null, $keepDefault=false) 
    {
        $encoder = $this->_encoder;
        $default = $encoder->detectOrder();
        if (is_null($encoding_list)) {
            $encoding_list = $default; 
        } else {
            $isSuccessful = $encoder->detectOrder($encoding_list);
            if (!$isSuccessful) {
                $encoding_list = false;
            } elseif ($keepDefault) {
                $encoder->detectOrder($default);
            }
        }
        return $encoding_list;
    }

    public function detectEncoding($str, $encoding_list = null, $strict = false) 
    {
        if (is_null($encoding_list)) {
            $encoding_list = $this->detectOrder();
        }
        return $this->_encoder->detectEncoding($str, $encoding_list, $strict);
    }

    public function internalEncoding($encoding=null, $keepDefault=false) 
    {
        $encoder = $this->_encoder;
        $default = $encoder->internalEncoding();
        if (is_null($encoding)) {
            $encoding = $default;
        } else {
            $isSuccessful = $encoder->internalEncoding($encoding);
            if (!$isSuccessful) {
                $encoding = false;
            } elseif ($keepDefault) {
                $encoder->internalEncoding($default);
            }
        }
        return $encoding;
    }

    public function convertEncoding($val, $to_encoding=null, $from_encoding=null) 
    {
        $to_encoding = $this->internalEncoding($to_encoding, true);
        if (is_null($from_encoding)) {
            $from_encoding = $this->detectEncoding($val);
        }
        return $this->_encoder->convertEncoding($val, $to_encoding, $from_encoding);
    }

    public function decodeUtf16($str, $to_encoding=null) 
    {
        $to_encoding = $this->internalEncoding($to_encoding, true);
        return $this->_encoder->convertEncoding($str, $to_encoding, 'UTF-16LE');
    }

    public function substr($str, $start, $length=null, $encoding=null) 
    {
        $encoding = $this->internalEncoding($encoding, true);
        return $this->_encoder->substr($str, $start, $length, $encoding);
    }

    public function eregReplace($pattern, $replacement, $string, $option='msr') 
    {
        return $this->_encoder->eregReplace($pattern, $replacement, $string, $option);
    }
}
