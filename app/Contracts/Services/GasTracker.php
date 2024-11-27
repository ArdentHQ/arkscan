<?php

namespace App\Contracts\Services;

interface GasTracker
{
    public function low(); //: int;

    public function average(); //: int;

    public function high(); //: int;
}
