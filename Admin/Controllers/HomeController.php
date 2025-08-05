<?php

namespace Admin\Controllers;

use Tools\Auth;

class HomeController extends Controller
{
    public function dashboard()
    {
        view('admin.dashboard', [
            'heads' => [
                '<link rel="stylesheet" href="assets/css/admin.css">'
            ],
            'title' => 'dashboard'
        ]);
    }
    /**
     * Handle administrator logout
     */
    public function logout()
    {
        // Remove authentication credentials
        Auth::removeAuth();

        // Redirect to forbidden/unauthorized page
        $this->forbidden();
    }
}