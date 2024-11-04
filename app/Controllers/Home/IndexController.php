<?php

namespace App\Controllers\Home;

use App\Controllers\Controller;
use App\Facades\Db;

class IndexController extends Controller
{

    public function index()
    {
        $this->display('/welcome');
    }

}
