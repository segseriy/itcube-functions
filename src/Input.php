<?php namespace itcube {
    use \itcube;
    /**
     * Class Input
     * @package itcube
     */
    class Input
    {
        /**
         * @var
         */
        private static $_instance;
        /**
         * @var array
         */
        protected $properties;
        /**
         * @var array
         */
        protected $server;

        /**
         * try {
         *    $input = \itcube\Input::Instance();
         * } catch (Exception $e) {
         *    echo $e->getMessage() . "\n";
         * }
         */
        public function __construct()
        {
            global $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER;

            $this->properties = array();
            $this->server     = $_SERVER;

            if( isset($_POST) && Functions::array_count($_POST) > 0 ) {
                foreach($_POST as $key=>$val) {
                    $this->properties['POST'][ $this->_clean_key($key) ] = $this->_clean_val($val);
                }
            }

            if( isset($_GET) && Functions::array_count($_GET) > 0 ) {
                foreach($_GET as $key=>$val) {
                    $this->properties['GET'][ $this->_clean_key($key) ] = $this->_clean_val($val);
                }
            }

            if( isset($_COOKIE) && Functions::array_count($_COOKIE) > 0 ) {
                foreach($_COOKIE as $key=>$val) {
                    $this->properties['COOKIE'][ $this->_clean_key($key) ] = $this->_clean_val($val);
                }
            }

            if( isset($_FILES) && Functions::array_count($_FILES) > 0 ) {
                foreach($_FILES as $key=>$val) {
                    $this->properties['FILES'][ $this->_clean_key($key) ] = $this->_clean_val($val);
                }
            }

        }

        /**
         * @param null $name
         * @param bool|false $xss
         * @return mixed|null|string
         */
        public function cookie($name = null, $xss = false)
        {
            if( !array_key_exists('COOKIE', $this->properties) ) {
                return null;
            }

            if( $name !== null and isset($this->properties['COOKIE']) and array_key_exists($name, $this->properties['COOKIE']) ) {
                if( $xss ) {
                    return $this->_xss_clean($this->properties['COOKIE'][ $name ]);
                }

                return $this->properties['COOKIE'][ $name ];
            } else if( $name === null ) {
                return $this->properties['COOKIE'];
            }

            return null;
        }

        /**
         * @param null $name
         * @param bool|false $xss
         * @return mixed|null|string
         */
        public function post($name = null, $xss = false)
        {
            if( !array_key_exists('POST', $this->properties) ) {
                return null;
            }

            if( $name !== null and array_key_exists($name, $this->properties['POST']) ) {
                if( $xss ) {
                    return $this->_xss_clean($this->properties['POST'][ $name ]);
                }

                return $this->properties['POST'][ $name ];
            } else if( $name === null ) {
                return $this->properties['POST'];
            }

            return null;
        }

        /**
         * @param null $name
         * @param bool|false $xss
         * @return mixed|null|string
         */
        public function get($name = null, $xss = false)
        {
            if( !array_key_exists('GET', $this->properties) ) {
                return null;
            }

            if( $name !== null and array_key_exists($name, $this->properties['GET']) ) {
                if( $xss ) {
                    return $this->_xss_clean($this->properties['GET'][ $name ]);
                }

                return $this->properties['GET'][ $name ];
            } else if( $name === null ) {
                return $this->properties['GET'];
            }

            return null;
        }

        /**
         * @param string $name
         * @param bool|false $xss
         * @return bool|mixed|string
         */
        public function files($name = '', $xss = false)
        {
            if( !array_key_exists('FILES', $this->properties) ) {
                return false;
            }

            if( empty($name) ) {
                return $this->properties['FILES'];
            }

            if( !isset($this->properties['FILES'][ $name ]) ) {
                return false;
            }

            if( is_array($this->properties['FILES'][$name]) ) {
                foreach ($this->properties['FILES'][$name] as $key=>$val) {
                    if( $xss ) {
                        $this->properties['FILES'][$name][$key] = $this->_xss_clean($val);
                    } else {
                        $this->properties['FILES'][$name][$key] = $val;
                    }
                }
            } else {
                return ($xss) ? $this->_xss_clean( $this->properties['FILES'][$name] ) : $this->properties['FILES'][$name];
            }

            return $this->properties['FILES'][$name];
        }

        /**
         * @return string
         */
        public function request_type()
        {
            if( !empty($this->server['HTTP_ACCEPT']) ) {
                $tmp = explode(',', $this->server['HTTP_ACCEPT']);
                if( !empty($tmp[0]) ) return $tmp[0];
            }

            return '';
        }

        /**
         * @return bool
         */
        public function is_post()
        {
            return ( $this->server['REQUEST_METHOD'] == 'POST' );
        }

        /**
         * @param $str
         * @return mixed
         * @throws \Exception
         */
        protected function _clean_key($str)
        {
            if( !preg_match("/^[a-z0-9:_\\/-]+$/i", $str) ) {
                throw new \Exception("Your request {$str} contains disallowed characters.");
            }

            return $str;
        }

        /**
         * @param $str
         * @return array|mixed
         * @throws \Exception
         */
        protected function _clean_val($str)
        {
            if( is_array($str) ) {
                $_array = array();

                foreach($str as $key=>$val) {
                    $_array[ $this->_clean_key($key) ] = $this->_clean_val($val);
                }

                return $_array;
            }

            /*if( get_magic_quotes_gpc() ) {
                $str = stripslashes($str);
            }*/

            return preg_replace("/\015\012|\015|\012/", "\n", $str);
        }

        /**
         * @param $data
         * @return array|mixed|string
         */
        protected function _xss_clean($data)
        {
            if( is_array($data) ) {
                $_ = array();

                foreach($data as $key=>$val) {
                    $_[ $key ] = $this->_xss_clean($val);
                }

                return $_;
            }

            // Fix &entity\n;
            $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), (string)$data);
            $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
            $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
            $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

            // Remove any attribute starting with "on" or xmlns
            $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

            // Remove javascript: and vbscript: protocols
            $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
            $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
            $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

            // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
            $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
            $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
            $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

            // Remove namespaced elements (we do not need them)
            $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

            do {
                // Remove really unwanted tags
                $old_data = $data;
                $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
            } while( $old_data !== $data );

            // we are done...
            return $data;
        }

        /**
         * @return Input
         */
        public static function Instance()
        {
            if( null === self::$_instance ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
    }

}  