<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KasusController extends Controller
{
    public function index()
    {
        return view('admin.kasus.index',[
            'title' => "Do Kasus"
        ]);
    }
}
