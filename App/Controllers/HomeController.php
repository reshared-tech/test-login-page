<?php

namespace App\Controllers;

use Tools\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Check the user login information
        Auth::checkAuth();
    }

    public function index()
    {
        // Show the home page
        view('home', [
            'title' => 'Welcome'
        ]);
    }
}