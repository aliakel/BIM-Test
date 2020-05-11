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
        $this->tz = $this->getTimeZone(request()->ip());
    }
}
