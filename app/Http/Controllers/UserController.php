<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){
        $user = UserModel::all(); // Get all data from table m_user
        return view('user', ['data' => $user]);
    }
}
