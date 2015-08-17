<?php namespace itcube {

    /**
     * Class XML
     * @package itcube
     */
    final class XML
    {
        /**
         * @var
         */
        private static $dom;

        /**
         * Initialize DOMDocument
         *
         * @param string $xml
         * @return bool
         */
        private static function _init($xml)
        {
            if(is_string($xml)) {
                self::$dom = new \DOMDocument();
                self::$dom->loadXml($xml);

                return true;
            }

            return false;
        }

        /**
         * Process function
         *
         * @param object $node
         * @return array
         */
        private static function _process($node)
        {
            $occurance = array();
            $result = array();

            if($node->childNodes != null) {
                foreach($node->childNodes as $key=>$child) {
                    if(array_key_exists($child->nodeName,$occurance)) {
                        $occurance[ $child->nodeName ]++;
                    } else {
                        $occurance[ $child->nodeName ] = 1;
                    }
                }
            }

            if($node->nodeType == XML_TEXT_NODE) {
                $result = html_entity_decode( htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'),
                    ENT_COMPAT,'UTF-8');
            } else if($node->nodeType == XML_CDATA_SECTION_NODE) {
                $result = html_entity_decode( htmlentities($node->nodeValue, ENT_COMPAT, 'UTF-8'),
                    ENT_COMPAT,'UTF-8');
            } else {
                if($node->hasChildNodes()) {
                    $children = $node->childNodes;

                    for($i=0; $i<$children->length; $i++) {
                        $child = $children->item($i);

                        if($child->nodeName != '#text' and $child->nodeName != '#cdata-section') {
                            if($occurance[$child->nodeName] > 1) {
                                $result[$child->nodeName][] = self::_process($child);
                            } else {
                                $result[$child->nodeName] = self::_process($child);
                            }
                        } else if ($child->nodeName == '#cdata-section') {
                            $text = self::_process($child);

                            if (trim($text) != '') {
//                                $result[$child->nodeName] = self::_process($child);
                                $result['#text'] = $text;
                            }
                        } else if ($child->nodeName == '#text') {
                            $text = self::_process($child);

                            if (trim($text) != '') {
//                                $result[$child->nodeName] = self::_process($child);
                                $result[$child->nodeName] = $text;
                            }
                        }
                    }
                }

                if($node->hasAttributes()) {
                    $attributes = $node->attributes;

                    if(!is_null($attributes)) {
                        foreach ($attributes as $key => $attr) {
                            $result['@'.$attr->name] = $attr->value;
                        }
                    }
                }
            }

            return $result;
        }

        /**
         * Public function
         * Load xml data from strinf
         *
         * @param string $_xml
         * @return array
         */
        public static function FromString($_xml)
        {
            if(!empty($_xml) and self::_init($_xml)) {
                $_result = self::_normalize_data_in_array(self::_process(self::$dom));
                self::$dom = null;
                return $_result;
            } else {
                return array();
            }
        }

        /**
         * Public function
         * Load xml data from file
         *
         * @param string $_file
         * @return array
         */
        public static function FromFile($_file)
        {
            if(!file_exists($_file)) {
                return false;
            }

            $_xml = file_get_contents($_file);

            if(self::_init($_xml)) {
                $_result = self::_normalize_data_in_array(self::_process(self::$dom));
                self::$dom = null;
                return $_result;
            } else {
                return array();
            }
        }

        /**
         * Normalize data into array
         *
         * @param array $_array
         * @return mixed
         */
        private static function _normalize_data_in_array($_array)
        {
            if(!empty($_array) and is_array($_array)) {
                $_ret = array();
                foreach($_array as $_key=>$_val) {
                    if(is_numeric($_val)) {
                        if(preg_match('/[.]/is',$_val)) {
                            $_ret[$_key] = (float)$_val;
                        } else {
                            $_ret[$_key] = (int)$_val;
                        }
                    } else if(is_array($_val)) {
                        $_ret[$_key] = self::_normalize_data_in_array($_val);
                    } else if(strtolower($_val) == 'true') {
                        $_ret[$_key] = true;
                    } else if(strtolower($_val) == 'false') {
                        $_ret[$_key] = false;
                    } else {
                        $_ret[$_key] = $_val;
                    }
                }

                return $_ret;
            }

            return false;
        }
    }
}