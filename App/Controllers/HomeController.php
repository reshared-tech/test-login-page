<?php

namespace App\Controllers;

use Tools\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Check the user login information
        Auth::checkAuth();

        // Show the home page
        view('home', [
            'title' => 'Welcome'
        ]);
    }
}