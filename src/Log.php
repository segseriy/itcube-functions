<?php namespace itcube {

    /**
     * Class Log
     * @package itcube
     */
    class Log
    {
        /**
         * @var array
         */
        protected $_logs;
        /**
         * @var
         */
        protected static $_instance;

        function __construct()
        {
            $this->_logs   = array();
            $this->_writed = false;
        }


        /**
         * @param $message
         * @param bool|false $error
         * @return $this
         */
        public function add($message, $error = false)
        {
            $error = ( $error ) ? 'ERROR' : '';
            if( !empty($message) ) {
                $this->_logs[] = array(
                    'time'   => date('Y-m-d H:i:s'),
                    'msg'    => $message,
                    'error'  => $error,
                );
            }

            return $this;
        }

        /**
         * @throws \Exception
         */
        public function write()
        {
            $_tmp = '';

            if( !is_dir(LOGS_DIR) ) {
                if( !@mkdir(LOGS_DIR, 0775, true) ) {
                    throw new \Exception(
                        'Unable to create `' . LOGS_DIR . '` directory'
                    );
                }
            }

            if( !empty($this->_logs) ) {
                foreach($this->_logs as $key=>$val) {
                    $_tmp .= "[{$val['time']}] {$val['error']} {$val['msg']}\n";
                }
                //614400
                //DebugBreak();
                if( is_file(LOGS_DIR . DIR_SEP . 'app.log.txt') ) {
                    $fs = filesize(LOGS_DIR . DIR_SEP . 'app.log.txt');
                    if( filesize(LOGS_DIR . DIR_SEP . 'app.log.txt') >= 614400 ) {
                        $logfile_out = LOGS_DIR . DIR_SEP . 'app-' . date('Ymd');

                        $j = 1;
                        while(true) {
                            if( !is_file($logfile_out . "-{$j}.log.txt.gz") ) {
                                $logfile_out .= "-{$j}.log.txt.gz";
                                break;
                            }
                            $j++;
                        }

                        \itcube\Functions::gz_file_pack(LOGS_DIR . DIR_SEP . 'app.log.txt', $logfile_out);
                        //gz_file_pack(LOGS_DIR . DIR_SEP . 'app.log.txt', $logfile_out);
                        @unlink(LOGS_DIR . DIR_SEP . 'app.log.txt');
                        @file_put_contents( LOGS_DIR . DIR_SEP . 'app.log.txt', '' );
                    }

                    $_tmp .= file_get_contents( LOGS_DIR . DIR_SEP . 'app.log.txt' );
                    file_put_contents( LOGS_DIR . DIR_SEP . 'app.log.txt', $_tmp );

                } else {
                    @file_put_contents( LOGS_DIR . DIR_SEP . 'app.log.txt', $_tmp );
                }

                $this->_logs = array();
            }
        }

        /**
         * @return Log
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