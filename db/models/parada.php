<?php

class Parada
{
    public $cp;
    public $np;
    public $ed;
    public $py;
    public $px;

    public function __construct($cp, $np, $ed, $py, $px)
    {
        $this->cp = $cp;
        $this->np = $np;
        $this->ed = $ed;
        $this->px = $px;
        $this->py = $py;
    }

}
