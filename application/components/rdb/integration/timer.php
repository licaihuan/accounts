<?php
class Timer
{/*{{{*/
    private $_start_time = 0;
    private $_stop_time  = 0;

    public function __construct()
    {/*{{{*/
    }/*}}}*/

    public function start()
    {/*{{{*/
        $this->_start_time = microtime(true);
    }/*}}}*/

    public function stop()
    {/*{{{*/
        $this->_stop_time = microtime(true);
    }/*}}}*/

    public function spent($num = 2)
    {/*{{{*/
        return number_format(round(($this->_stop_time - $this->_start_time), $num), $num, '.', '');
    }/*}}}*/
}/*}}}*/
?>
