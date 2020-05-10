<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Services\TimeZone;
use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    use TimeZone;

    protected $tz;

    public function __construct()
    {
        $this->tz ='Europe/Madrid';// $this->getTimeZone('46.57.204.179');
    }
}
