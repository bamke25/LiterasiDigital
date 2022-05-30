<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.member.index',[
            'title' => "My Profile"
        ]);
    }

    public function edit()
    {
        return view('admin.member.edit',[
            'title' => "Edit Profile"
        ]);
    }

    public function change_password()
    {
        return view('admin.member.change_password',[
            'title' => "Change Password"
        ]);
    }
}
