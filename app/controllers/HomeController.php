<?php

// Inherit from the Base Controller
require_once BASE_PATH . 'app/core/Controller.php';

class HomeController extends Controller
{
    /**
     * Renders the public-facing landing page.
     */
    public function index()
    {
        $this->view('home/index');
    }
}
