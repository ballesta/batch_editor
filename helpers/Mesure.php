<?php
class Mesure extends Message
{
    var $Dist;
    var $Average;
    var $Max;
    var $Step;
    var $Sprint;
    var $Mobility;
    var $Shoot;
    var $Pass;
    var $Control;

    function __construct ($Dist, $Average, $Max, $Step, $Sprint, $Mobility, $Shoot, $Pass, $Control)
    {
        $this->Dist = $Dist;
        $this->Average = $Average;
        $this->Max = $Max;
        $this->Step = $Step;
        $this->Sprint = $Sprint;
        $this->Mobility = $Mobility;
        $this->Shoot = $Shoot;
        $this->Pass = $Pass;
        $this->Control = $Control;
    }
}
