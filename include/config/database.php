<?php 
    // : mysqli =Indica que retornara una instancia de mysqli
    function conectarDB() : mysqli {
        $conexion = mysqli_connect('localhost', 'root', 'Ingresa tu password', 'bienesraicescrud');

        /*
            if($conexion) {
                echo "Conection";
            } else {
                echo "Error de conexión";
            }
        */

        if(!$conexion) {
            echo "Error de conexión";
            exit;
        }

        return $conexion;
    }