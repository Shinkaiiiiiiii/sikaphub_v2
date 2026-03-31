<?php

class Controller
{
    // Instantiate a model class
    public function model($model)
    {
        require_once BASE_PATH . 'app/models/' . $model . '.php';
        return new $model();
    }

    // Load a view file and pass data to it
    public function view($view, $data = [])
    {
        if (file_exists(BASE_PATH . 'app/views/' . $view . '.php')) {
            // Extract array keys into variables (e.g., $data['name'] becomes $name)
            extract($data);
            require_once BASE_PATH . 'app/views/' . $view . '.php';
        }
        else {
            die("View does not exist.");
        }
    }
}