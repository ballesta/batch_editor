<?php
class EventPass extends Message
{
    var $ID;
    var $Speed;

    function __construct ($ID, $Speed)
    {
        $this->ID = $ID;
        $this->Speed = $Speed;
    }
}
