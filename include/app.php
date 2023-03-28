<?php
    /*
        Nos ayuda a eliminar los include de los archivos, 
        ponemos como se llamara y la ruta donde los encontraremos
        __DIR__ = Toma la direccion de la ubicacion actual
    */
    define('TEMPLATES_URL', __DIR__ . './templates');
    //define('FUNCIONES_URL', '/include/funciones.php');
    define('FUNCIONES_URL', __DIR__ . '/${nombre}.php');