<?php
namespace itcube{

    /**
     * Class Functions
     * @package itcube
     */
    class Functions{
        /**
         * @param $_
         * @param null $mode
         * @return int
         */
        public static function array_count($_, $mode = null)
        {
            return (!is_array($_)) ? 0 : count($_, $mode);
        }//array_count  

        /**
         * @param $count
         * @param $form1
         * @param $form2
         * @param $form3
         * @return mixed
         */
        public static function format_by_count($count, $form1, $form2, $form3) //(count, word for count = 1, word for count = 4, word for count = 5)
        {
            $count = abs($count) % 100;
            $lcount = $count % 10;
            if ($count >= 11 && $count <= 19) return($form3);
            if ($lcount >= 2 && $lcount <= 4) return($form2);
            if ($lcount == 1) return($form1);
            return $form3;
        }

        /**
         * extract the fractional part of a fractional number
         *
         * @param mixed $num
         * @return mixed
         */
        public static function fract($num)
        {
            if( is_numeric($num) ) {
                $num -= floor( (float)$num );
                return (int)str_replace('0.', '', (string)$num);
            }

            return null;
        }
        /**
         * explode float number
         *
         * @param mixed $num
         * @return mixed
         */
        public static function float_extract($num)
        {
            return ( is_float($num) ) ? explode('.', (string)$num) : false;
        }
        /**
         * true если num четное
         *
         * @param mixed $num
         * @return bool
         */
        public static function is_even($num)
        {
            if( !is_numeric($num) ) return false;
            if( preg_match('/[\.]/s', $num) ) {
                return ( self::fract($num) & 1 ) ? false : true;
            } else {
                return ( $num & 1 ) ? false : true;
            }
        }

        /**
         * @param $_array - incomin array
         * @param $_arrays_count - count outcomin arrays
         * @return array[$_arrays_count]
         */
        public static function split_array($_array,$_arrays_count=2){
            //DebugBreak();
            $elCount   = count($_array);
            $arElCount = ceil($elCount/$_arrays_count);
            $resArray  = array();
            $i    = 0;
            for( $ar = 0; $ar < $_arrays_count; $ar++ ){

                for( $tdEl = 0; $tdEl < $arElCount; $tdEl++){
                    if( $_array[$i] ){
                        $resArray[$ar][$tdEl] = $_array[$i];
                    }else{
                        $resArray[$ar][$tdEl] = NULL;
                    }
                    $i++;
                }
            }
            return $resArray;
        }//split_array

        /**
         * @return mixed|string
         */
        public static function get_ip_address()
        {
            global $_SERVER;

            if( !empty($_SERVER['REMOTE_ADDR']) and !empty($_SERVER['HTTP_CLIENT_IP']) ) {
                $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
            } else if( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
                $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
            } else if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
                $ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ipaddr = $_SERVER['REMOTE_ADDR'];
            }

            if( $ipaddr === false ) {
                $ipaddr = '0.0.0.0';
                return $ipaddr;
            }

            if( strstr($ipaddr, ',') ) {
                $x = explode(',', $ipaddr);
                $ipaddr = end( $x );
            }

            if( filter_var($ipaddr, FILTER_VALIDATE_IP) === false ) {
                $ipaddr = '0.0.0.0';
            }

            return $ipaddr;
        }//get_ip_address

        /**
         * @param $file_in
         * @param null $file_out
         * @return bool
         */
        public static function gz_file_pack($file_in, $file_out = null)
        {
            if( !is_file($file_in) ) {
                return false;
            }

            $content = file_get_contents($file_in);

            if( !isset($file_out) ) {
                $file_out = $file_in . '.gz';
            }

            if( $gz = gzopen($file_out, 'w9') ) {
                gzwrite($gz, $content);
                gzclose($gz);
                return true;
            } else {
                return false;
            }
        }//gz_file_pack        

        public static function http_cache_off()
        {
            if( !headers_sent() ) {
                header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: no-cache, must-revalidate');
                header('Cache-Control: post-check=0,pre-check=0', false);
                header('Cache-Control: max-age=0', false);
                header('Pragma: no-cache');
            }
        }//http_cache_off

        /**
         * @param int $lenght
         * @return string
         */
        public static function salt_generation( $lenght = 18 )
        {
            return substr( sha1(mt_rand()), 0, $lenght );
        }//salt_generation

        /**
         * @param $password
         * @param null $salt
         * @return bool|string
         */
        public static function mkpass($password, $salt = null)
        {
            if( !isset($password) ) return false;
            if( null === $salt ) {
                $salt = substr( sha1( mt_rand() ), 0, 16 );
            }

            return crypt( md5($password), '$1$' . $salt . '$' );
        }//mkpass

        /**
         * @param $code
         * @return bool
         */
        public static function set_http_status($code)
        {
            $status = self::get_http_status($code);

            if( $status != false and !headers_sent() ) {
                header('HTTP/1.1 ' . $status);
                header('Status: ' . $status);
                return true;
            }

            return false;
        }//set_http_status


        /**
         * @return bool
         */
        public static function is_ajax()
        {
            global $_SERVER;

            if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) and $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
                return true;
            } else if( !empty($_SERVER['X_REQUESTED_WITH']) and $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
                return true;
            } else if( !empty($_SERVER['HTTP_ACCEPT']) and (false !== strpos($_SERVER['HTTP_ACCEPT'], 'text/x-ajax')) ) {
                return true;
            }

            return false;
        }//is_ajax

        /**
         * @param $string
         * @param $length
         * @return string
         */
        public static function crop_string($string, $length)
        {
            $string = strip_tags($string);

            if( function_exists('mb_strlen') ) {
                $len    = (mb_strlen($string) > $length) ? mb_strripos( mb_substr($string, 0, $length), ' ' ) : $length;
                $result = mb_substr($string, 0, $len);
                return (mb_strlen($string) > $length) ? $result . '...' : $result;
            }

            $result = iconv('utf-8', 'windows-1251', $string);
            $length = strripos( substr($result, 0, $length), ' ');
            return iconv('windows-1251', 'utf-8', substr($result, 0, $length) );
        }//crop_string

        /**
         * @return string
         */
        public static function uuid()
        {
            return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                mt_rand( 0, 0x0fff ) | 0x4000,
                mt_rand( 0, 0x3fff ) | 0x8000,
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            );
        }//uuid

        /**
         * @param $uuid
         * @return bool
         */
        public static function is_uuid($uuid)
        {
            return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
        }//is_uuid

        public static function dump()
        {
            $args = func_get_args();

            echo "\n<pre style=\"border:1px solid #ccc;padding:10px;" .
                "margin:10px;font:14px courier;background:whitesmoke;" .
                "display:block;border-radius:4px;\">\n";

            $trace = debug_backtrace(false);
            $offset = (@$trace[2]['function'] === 'dump_d') ? 2 : 0;

            echo "<span style=\"color:red\">" .
                @$trace[1+$offset]['class'] . "</span>:" .
                "<span style=\"color:blue;\">" .
                @$trace[1+$offset]['function'] . "</span>:" .
                @$trace[0+$offset]['line'] . " " .
                "<span style=\"color:green;\">" .
                @$trace[0+$offset]['file'] . "</span>\n";

            if ( ! empty($args)) {
                call_user_func_array('var_dump', $args);
            }

            echo "</pre>\n";

        }//dump end  

        public static function dumpd()
        {
            $args = func_get_args();
            self::dump($args);
            die();
        }//dumpd end 

        /**
         * @param $_int_val
         * @return bool|string
         */
        public static function int_to_byte( $_int_val )
        {
            if( !is_int($_int_val) ) return false;
            $hex = dechex($_int_val);
            if( strlen($hex) & 1 ) $hex = '0' . $hex;

            $_   = '';
            $add = (4 - (strlen($hex) / 2));

            for( $j = 0; $j < $add; $j++ ) { $hex = '00' . $hex;}

            for( $j = 0; $j < strlen($hex); $j+=2 ) {
                $_ .= chr( hexdec($hex{$j} . $hex{$j+1}) );
            }

            return $_;
        } //int_to_byte

        /**
         * @param $str
         * @return number
         */
        public static function byte_to_int( $str )
        {
            $hex = '';

            for( $j = 0; $j < strlen($str); $j++ ) {
                $_ = dechex( ord( $str{$j} ) );
                if( strlen($_) & 1 ) $_ = '0' . $_;
                $hex .= $_;
            }

            return hexdec($hex);
        }//byte_to_int  

        /**
         * @param $file_path
         * @return bool|string
         */
        public static function get_file_extension($file_path) {
            $basename = basename($file_path); // get filename
            if ( strrpos($basename, '.')!==false ) { // check if file is't dot
                // cut part of string after last dot in filename
                $file_extension = substr($basename, strrpos($basename, '.')+1);
            } else {
                // if there is no dot in filename return false
                $file_extension = false;
            }
            return $file_extension;
        }// get_file_extension

        /**
         * zip files
         *
         * @param array $_files  - array of full paths of files
         * @param string $_zip_name - full path to zip archive
         * @return bool
         */
        public static function zip_files(array $_files, $_zip_name) {
            if( $_zip_name == '' ){
                return false;
            }
            if( extension_loaded('zip') ){
                $zip = new \ZipArchive();
                if( $zip->open($_zip_name, \ZipArchive::CREATE)!==true ){
                    return false;
                }
                foreach( $_files as $item ){
                    $zip->addFile($item,basename($item));
                }
                $zip->close();
                return file_exists($_zip_name);
            }else{
                return false;
            }
        }// zip_files

        /**
         * @param $str
         * @return mixed
         */
        public static function pregtrim($str) {
            return preg_replace("/[^\x20-\xFF]/","",@strval($str));
        } //pregtrim

        /**
         * @param $url
         * @return bool|mixed|string
         */
        public static function checkurl($url) {
            // cut unnecessary symbols and trim
            $url=trim(self::pregtrim($url));
            // если пусто - выход
            if (strlen($url)==0) return false;
            //проверяем УРЛ на правильность
            if (!preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
                "(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
                "org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
                "!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&".
                "?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i",$url,$ok))
                return false; // если не правильно - выход
            // если нет протокала - добавить
            if (!strstr($url,"://")) $url="http://".$url;
            // заменить протокол на нижний регистр: hTtP -> http
            $url=preg_replace("~^[a-z]+~ie","strtolower('\\0')",$url);
            return $url;
        }  //checkurl

        /**
         * Get ststus by code number
         *
         * @param integer $code
         * @return string
         */
        public static function get_http_status($code)
        {
            if( !is_numeric($code) ){ return false;}

            $_HTTP_STATUS = array (
                100 => '100 Continue',
                101 => '101 Switching Protocols',
                102 => '102 Processing',
                200 => '200 OK',
                201 => '201 Created',
                202 => '202 Accepted',
                203 => '203 Non-nCore_Authoritative Information',
                204 => '204 No Content',
                205 => '205 Reset Content',
                206 => '206 Partial Content',
                207 => '207 Multi Status',
                226 => '226 IM Used',
                300 => '300 Multiple Choices',
                301 => '301 Moved Permanently',
                302 => '302 Found',
                303 => '303 See Other',
                304 => '304 Not Modified',
                305 => '305 Use Proxy',
                306 => '306 (Unused)',
                307 => '307 Temporary Redirect',
                400 => '400 Bad Request',
                401 => '401 Unauthorized',
                402 => '402 Payment Required',
                403 => '403 Forbidden',
                404 => '404 Not Found',
                405 => '405 Method Not Allowed',
                406 => '406 Not Acceptable',
                407 => '407 Proxy nCore_Authentication Required',
                408 => '408 Request Timeout',
                409 => '409 Conflict',
                410 => '410 Gone',
                411 => '411 Length Required',
                412 => '412 Precondition Failed',
                413 => '413 Request Entity Too Large',
                414 => '414 Request-URI Too Long',
                415 => '415 Unsupported Media Type',
                416 => '416 Requested Range Not Satisfiable',
                417 => '417 Expectation Failed',
                420 => '420 Policy Not Fulfilled',
                421 => '421 Bad Mapping',
                422 => '422 Unprocessable Entity',
                423 => '423 Locked',
                424 => '424 Failed Dependency',
                426 => '426 Upgrade Required',
                449 => '449 Retry With',
                500 => '500 Internal Server Error',
                501 => '501 Not Implemented',
                502 => '502 Bad Gateway',
                503 => '503 Service Unavailable',
                504 => '504 Gateway Timeout',
                505 => '505 HTTP Version Not Supported',
                506 => '506 Variant Also Varies',
                507 => '507 Insufficient Storage',
                509 => '509 Bandwidth Limit Exceeded',
                510 => '510 Not Extended'
            );

            if( !empty($_HTTP_STATUS[$code]) ) {
                return $_HTTP_STATUS[$code];
            }

            return false;
        }

        public static function memory_clear()
        {
            if( function_exists('gc_collect_cycles') ) {
                gc_enable();
                $_ = gc_collect_cycles();
                gc_disable();

                return (int)$_;
            }

            return 0;
        }

        /**
         * @param $obj
         * @return bool
         */
        public static function obj_to_array( $obj )
        {
            if( !is_object($obj) ) return false;

            $publics = function($obj) {
                return get_object_vars($obj);
            };

            $values = $publics($obj);

            foreach($values as $key=>$val) {
                if( is_object($val) ) $values[ $key ] = obj_to_array($val);
            }

            return $values;
        }

    }//class Functions
}//namespace