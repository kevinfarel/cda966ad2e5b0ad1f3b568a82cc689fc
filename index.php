<?php
    $controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';
    $action = isset($_GET['action']) ? $_GET['action'] : 'index';

    $controllerClass = ucfirst($controller) . 'Controller';
    include "controllers/$controllerClass.php";

    $controllerInstance = new $controllerClass();
    $controllerInstance->$action(1); // Assuming user ID 1

    // Optionally, you can add more routing and error handling here.
?>
