<?php
    //require './include/app.php';    
    require 'app.php';    

    function incluirTemplate(string $nombre, bool $inicio = false) {
        /*
        Al agregar el require del app.php, comentamos la linea
            include "./include/templates/${nombre}.php";
        */
        include TEMPLATES_URL . "/${nombre}.php";
    }

    function estaAutenticado() : bool {
        session_start();

        $auth = $_SESSION['login'];
    
        if ($auth) {
            return true;
        }

        return false;
    }