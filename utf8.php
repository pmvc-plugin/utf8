<?php

namespace PMVC\PlugIn\utf8;

use PMVC\PlugIn;

${_INIT_CONFIG}[_CLASS] = __NAMESPACE__ . '\utf8';

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

    public function toUtf8(
        $val,
        $from_encoding_list = null,
        $bAlreadyInit = false
    ) {
        $getValue = function ($val) use ($bAlreadyInit) {
            return $bAlreadyInit ? $val : \PMVC\get($val);
        };
        $isObj = is_object($val) && !$bAlreadyInit;
        $myval = $isObj || \PMVC\isArray($val) ? $getValue($val) : false;
        if (!empty($myval)) {
            array_walk_recursive($myval, function (&$item) use (
                $from_encoding_list,
                $getValue
            ) {
                if (is_string($item)) {
                    if (!$this->detectEncoding($item, 'utf-8', true)) {
                        $item = $this->convertEncoding(
                            $item,
                            'utf-8',
                            $from_encoding_list
                        );
                    }
                } elseif (!\PMVC\testString($item)) {
                    $item = $this->toUtf8(
                        $getValue($item),
                        $from_encoding_list,
                        true
                    );
                }
            });
            if ($isObj) {
                $myval = (object) $myval;
            }
            return $myval;
        } elseif (is_string($val)) {
            if (!$this->detectEncoding($val, 'utf-8', true)) {
                return $this->convertEncoding(
                    $val,
                    'utf-8',
                    $from_encoding_list
                );
            } else {
                return $val;
            }
        } elseif (\PMVC\testString($val)) {
            return $val;
        } else {
            $myval = print_r($val, true);
            if (!$this->detectEncoding($myval, 'utf-8', true)) {
                $myval = $this->convertEncoding(
                    $myval,
                    'utf-8',
                    $from_encoding_list
                );
            }
            return $myval;
        }
    }

    public function detectOrder($encoding_list = null, $keepDefault = false)
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
            $encoding_list = join(', ', $this->detectOrder());
        }
        $result = $this->_encoder->detectEncoding(
            $str,
            $encoding_list,
            $strict
        );

        return $result;
    }

    public function internalEncoding($encoding = null, $keepDefault = false)
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

    public function convertEncoding(
        $val,
        $to_encoding = null,
        $from_encoding = null
    ) {
        $to_encoding = $this->internalEncoding($to_encoding, true);
        $from_encoding = $this->detectEncoding($val, $from_encoding, true);
        if (empty($from_encoding)) {
            $from_encoding = null;
        }
        return $this->_encoder->convertEncoding(
            $val,
            $to_encoding,
            $from_encoding
        );
    }

    public function decodeUtf16($str, $to_encoding = null)
    {
        $to_encoding = $this->internalEncoding($to_encoding, true);
        return $this->_encoder->convertEncoding($str, $to_encoding, 'UTF-16LE');
    }

    public function substr($str, $start, $length = null, $encoding = null)
    {
        $encoding = $this->internalEncoding($encoding, true);
        return $this->_encoder->substr($str, $start, $length, $encoding);
    }

    public function eregReplace(
        $pattern,
        $replacement,
        $string,
        $option = 'msr'
    ) {
        return $this->_encoder->eregReplace(
            $pattern,
            $replacement,
            $string,
            $option
        );
    }
}
