<?php

namespace BeInMedia\Http\Controllers;

use BeInMedia\Services\TimeZone;
use Illuminate\Routing\Controller;

/**
 * Class BaseController
 * @package BeInMedia\Http\Controllers
 */
class BaseController extends Controller
{
    use TimeZone;

    /**
     * @var string
     */
    protected $tz;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->tz = 'Asia/Damascus';//$this->getTimeZone(request()->ip());
    }
}
