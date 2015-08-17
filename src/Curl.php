<?php namespace itcube {

    /**
     * Class Curl
     * @package itcube
     */
    class Curl
    {
        /**
         * @var
         */
        /**
         * @var array
         */
        /**
         * @var array
         */
        /**
         * @var array|string
         */
        protected
            $_session,
            $_options,
            $_result,
            $_url;

        /**
         * @var string
         */
        /**
         * @var int|string
         */
        public
            $useragent,
            $timeout;

        /**
         * @param string $url
         */
        public function __construct($url = '')
        {
            if( !function_exists('curl_init') ) {
                die('cURL Class - PHP was not built with --with-curl, rebuild PHP to use cURL.');
            }

            if(!empty($url)) {
                $this->_url = $url;
            }

            $this->timeout   = 30;
            $this->_options  = array();
            $this->useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
        }

        protected function _init()
        {
            if( !preg_match('!^\w+://! i', $this->_url) ) {
                $this->_url = 'http://'.$this->_url;
            }

            $this->_session = curl_init( (!empty($this->_url))?$this->_url:'' );
//          curl_setopt($this->_session, CURLOPT_REFERER, BASE_URL);
            curl_setopt($this->_session, CURLOPT_USERAGENT, (!empty($this->useragent)) ? $this->useragent : 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1' );
            curl_setopt($this->_session, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($this->_session, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->_session, CURLOPT_HTTPHEADER, array('Expect:'));

            if(!empty($this->_options)) {
                foreach($this->_options as $key=>$val) {
                    curl_setopt($this->_session, $key, $val);
                }
            }

        }

        /**
         * @param string $username
         * @param string $password
         * @return $this
         */
        public function http_login($username = '', $password = '')
        {
            if( !empty($username) and !empty($password) ) {
                $this->option(CURLOPT_USERPWD, $username.':'.$password);
            }

            return $this;
        }

        /**
         * @param $name
         * @param $value
         * @return $this
         */
        public function option($name, $value)
        {
            if( !empty($name) and !empty($value) ) {
                $this->_options[$name] = $value;
            }

            return $this;
        }

        /**
         * @return array|bool|mixed|string
         */
        public function execute()
        {
            $this->_init();
            $this->_result = curl_exec( $this->_session );

            if( curl_errno($this->_session) ) {
                return curl_error($this->_session);
            }

            if( !empty($this->_result) ) {
                return $this->_result;
            }

            return false;
        }

        /**
         * @param $url
         * @param int $port
         * @return $this
         */
        public function proxy($url, $port = 8080)
        {
            if( !empty($url) and is_numeric($port) ) {
                $this->option(CURLOPT_HTTPPROXYTUNNEL, true);
                $this->option(CURLOPT_PROXY, "{$url}:80");
            }

            return $this;
        }

        /**
         * @param string $username
         * @param string $password
         * @return $this
         */
        public function proxy_login($username = '', $password = '')
        {
            if( !empty($username) and !empty($password) ) {
                $this->option(CURLOPT_PROXYUSERPWD, "{$username}:{$password}");
            }

            return $this;
        }

        /**
         * @param string $url
         * @return array|bool|mixed|string
         */
        public function get($url = '')
        {
            if( !empty($url) ) {
                $this->_url = $url;
            }

            if(!empty($this->_url)) {
                return $this->execute();
            }

            return false;
        }

        /**
         * @param array $params
         * @param string $url
         * @return array|bool|mixed|string
         */
        public function post($params = array(), $url = '')
        {
            if( !empty($params) and is_array($params) ) {
                $params = http_build_query($params);
            }

            if(!empty($url)) {
                $this->_url = $url;
            }

            $this->_options[ CURLOPT_POST ] = true;
            $this->_options[ CURLOPT_POSTFIELDS ] = $params;

            if( !empty($this->_url) ) {
                return $this->execute();
            }

            return false;
        }

        /**
         * @param array $params
         * @return $this
         */
        public function set_cookies($params = array())
        {
            if( !empty($params) ) {
                if(is_array($params)) {
                    $params = http_build_query($params);
                }

                $this->option(CURLOPT_COOKIE, $params);
            }

            return $this;
        }

        /**
         * @return bool|mixed
         */
        public function getinfo()
        {
            if( empty($url) ) {
                return false;
            }

            return curl_getinfo($this->_session);
        }

        function __destruct()
        {
            curl_close($this->_session);
        }
    }
}