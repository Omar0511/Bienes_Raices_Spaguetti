<?php     
    //require 'include/funciones.php';
    require '../../include/funciones.php';
    $auth = estaAutenticado();
    
    if (!$auth) {
        header('Location: /');
    }

    //Conexion BD
    require '../../include/config/database.php';
    $conexion = conectarDB();
    
    /*
        Debugueamos la variables super globlales: $_SERVER, $_POST
        echo "<pre>";
            var_dump($_SERVER);
        echo "</pre>";

        echo "<pre>";
            var_dump($_POST);
        echo "</pre>";
    */

    //Consulta para obtener vendedores
    $consulta = "SELECT * FROM  vendedores;";
    $resultado = mysqli_query($conexion, $consulta);

    //Arreglo con mensajes de errores
    $errores = [];
    $caracteres = 30;

    //Se inicializan vacías, esto para almacenar el dato en el campo y no tenga que reescribirlo
    $titulo = '';
    $precio = '';
    $descripcion = '';
    $habitaciones = '';
    $wc = '';
    $estacionamiento = '';
    $vendedorId = '';

    //Ejecuta el código después de que el usuario envía el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /*
            echo "<pre>";
                var_dump($_POST);
            echo "</pre>";

            Es para ver archivos o documentos
            echo "<pre>";
                var_dump($_FILES);
            echo "</pre>";
        */


        //Sanitizando (inyection sql), se agrego el mysqli_real_escape_string
        $titulo = mysqli_real_escape_string($conexion, $_POST['titulo'] );
        $precio = mysqli_real_escape_string($conexion, $_POST['precio'] );
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] );
        $habitaciones = mysqli_real_escape_string($conexion, $_POST['habitaciones'] );
        $wc = mysqli_real_escape_string($conexion, $_POST['wc'] );
        $estacionamiento = mysqli_real_escape_string($conexion, $_POST['estacionamiento'] );
        $vendedorId = mysqli_real_escape_string($conexion, $_POST['vendedorId'] );
        $creado = date('Y/m/d');
        $imagen = $_FILES['imagen'];

        //Validador de errores
        if (!$titulo) {
            $errores[] = "Debes agregar un título";
        }

        if (!$precio) {
            $errores[] = "Debes agregar un precio";
        }

        if ( strlen($descripcion) < $caracteres) {
            $errores[] = "Debes agregar una descripción miníma de 30 carácteres";
        }

        if (!$habitaciones) {
            $errores[] = "Debes agregar el número de habitaciones";
        }

        if (!$wc) {
            $errores[] = "Debes agregar el número de baños";
        }

        if (!$estacionamiento) {
            $errores[] = "Debes agregar el número de estacionamientos";
        }

        if (!$vendedorId) {
            $errores[] = "Debes seleccionar un vendedor";
        }

        if (!$imagen['name'] || $imagen['error']) {
            $errores[] = "La imágen es obligatoria";
        }

        //Validar tamaaño IMG, 1000kb
        $medida = 1000 * 1000;
        if ($imagen['size'] > $medida) {
            $errores[] = "La imágen no puede ser más grande de 100kb";
        }

        /*
            echo "<pre>";
                var_dump($errores);
            echo "</pre>";
        */

        //Revisar que el arreglo de errores este vacío
        if (empty($errores)) {
            //SUBIR ARCHIVOS
            //Crear carpeta en esa ruta
            $carpetaImagenes = '../../imagenes/';

            //Si existe o no existe
            if (!is_dir($carpetaImagenes)) {
                //Crea un directorio
                mkdir($carpetaImagenes);
            }

            /*
                Generar un nombre único
                md5 = Genera un hash de código
                uniqid = genera un id único
                rand = genera código aleatorio
            */

            //$nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";
            $nombreImagen = md5( uniqid( rand(), true ) ) . $imagen['name'];

            //Subir la imagen y que se quede en el servidor
            move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);

            //INSERT a la BD
            $query = "INSERT INTO propiedades (titulo, precio, imagen, descripcion, habitaciones, wc, estacionamiento, creado, vendedorId) 
                        VALUES ('$titulo', '$precio', '$nombreImagen', '$descripcion', '$habitaciones', '$wc', '$estacionamiento', '$creado', '$vendedorId');"
            ;
            
            //echo $query;
            $resultado = mysqli_query($conexion, $query);
            if ($resultado) {
                //echo "Insertado con éxito";

                /* 
                    Te redireccionar a esa pestañana
                    Se agrega Query String para mandar un mensaje, 
                    es lo que esta después del signo ?
                */
                //header('Location: /admin?mensaje=Registrado Correctamente');
                header('Location: /admin?resultado=1');
            }
        }

    }
    
    incluirTemplate('header');
?>    
    <main class="contenedor">
        <h1>Crear</h1>

        <!-- <a href="/admin/index.php" class="boton boton-verde"> <- Volver</a> -->
        <a href="/admin" class="boton boton-verde"> <- Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>
            

        <!-- 
            Nota: se recoienda poner en el name, el mismo nombre del id 
            enctype="multipart/form-data" = se agrega cuando subes archivos, en este caso
            es para la subida de IMAGENES
        -->
        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <fieldset>
                <legend>Información general</legend>

                <!-- Se agrega en el value la variable de PHP para que se quede guardada y no tenga que sobreescribirla -->
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imágen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"> <?php echo $descripcion; ?> </textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number" id="habitaciones" name="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

                <label for="wc">Baños:</label>
                <input type="number" id="wc" name="wc" placeholder="Ej: 2" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number" id="estacionamiento" name="estacionamiento" placeholder="Ej: 5" min="1" max="9" value="<?php echo $estacionamiento; ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <select name="vendedorId">
                    <option value=""> -- Eliga una opción -- </option>
                    <!--
                        <option value="1">Juan</option>
                        <option value="2">Karen</option>
                    -->

                    <?php while ($row = mysqli_fetch_assoc($resultado) ) : ?>
                        <option 
                            <?php echo $vendedorId === $row['id'] ? 'selected' : ''; ?> 
                            value=" <?php echo $row['id']; ?> "
                        > 
                            <?php echo $row['nombre'] . " " . $row['apellido']; ?> 
                        </option>
                    <?php endwhile; ?>
                </select>
            </fieldset>

            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>

    </main>
<?php 
    //include './include/templates/footer.php';
    incluirTemplate('footer');
?>