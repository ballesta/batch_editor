<?php
class Start extends Message
{
    var $Version_soft;
    var $Version_hardware;
    var $Period;
    var $Battery;

    function __construct ($Version_soft, $Version_hardware, $Period, $Battery)
    {
        $this->Version_soft = $Version_soft;
        $this->Version_hardware = $Version_hardware;
        $this->Period = $Period;
        $this->Battery = $Battery;
    }
}
