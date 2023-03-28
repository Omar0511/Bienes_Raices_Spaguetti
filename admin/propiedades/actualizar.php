<?php 
    require '../../include/funciones.php';
    $auth = estaAutenticado();
    
    if (!$auth) {
        header('Location: /');
    }

    //Validar la URL por ID
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);
    if(!$id) {
        header('Location: /admin');
    }

    require '../../include/config/database.php';
    $conexion = conectarDB();

    //Obtener datos de la propiedad
    $consultaPropiedades = "SELECT * FROM propiedades WHERE id = {$id};";
    $resultadoPropiedades = mysqli_query($conexion, $consultaPropiedades);
    $propiedad = mysqli_fetch_assoc($resultadoPropiedades);
    
    $consulta = "SELECT * FROM  vendedores;";
    $resultado = mysqli_query($conexion, $consulta);

    $errores = [];
    $caracteres = 30;

    //Se inicializan con los valores de la BD
    $titulo = $propiedad['titulo'];
    $precio = $propiedad['precio'];
    $descripcion = $propiedad['descripcion'];
    $habitaciones = $propiedad['habitaciones'];
    $wc = $propiedad['wc'];
    $estacionamiento = $propiedad['estacionamiento'];
    $vendedorId = $propiedad['vendedorId'];
    $imagenPropiedad = $propiedad['imagen'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titulo = mysqli_real_escape_string($conexion, $_POST['titulo'] );
        $precio = mysqli_real_escape_string($conexion, $_POST['precio'] );
        $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion'] );
        $habitaciones = mysqli_real_escape_string($conexion, $_POST['habitaciones'] );
        $wc = mysqli_real_escape_string($conexion, $_POST['wc'] );
        $estacionamiento = mysqli_real_escape_string($conexion, $_POST['estacionamiento'] );
        $vendedorId = mysqli_real_escape_string($conexion, $_POST['vendedorId'] );
        $creado = date('Y/m/d');
        $imagen = $_FILES['imagen'];

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

        $medida = 1000 * 1000;
        if ($imagen['size'] > $medida) {
            $errores[] = "La imágen no puede ser más grande de 100kb";
        }

        if (empty($errores)) {

            $carpetaImagenes = '../../imagenes/';

            if (!is_dir($carpetaImagenes)) {
                mkdir($carpetaImagenes);
            }

            $nombreImagen = '';

            if ($imagen['name']) {
                //Eliminar archivos
                unlink($carpetaImagenes . $propiedad['imagen']);

                $nombreImagen = md5( uniqid( rand(), true ) ) . $imagen['name'];

                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen);
            } else {
                $nombreImagen = $propiedad['imagen'];
            }

            $query = "UPDATE propiedades 
                        SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}',
                        descripcion = '${descripcion}', habitaciones = ${habitaciones}, 
                        wc = ${wc}, estacionamiento = ${estacionamiento}, 
                        vendedorId = ${vendedorId}
                        WHERE id = ${id};
            ";

            $resultado = mysqli_query($conexion, $query);
            if ($resultado) {

                header('Location: /admin?resultado=2');
            }
        }

    }
        
    incluirTemplate('header');
?>    
    <main class="contenedor">
        <h1>Actualizar Propiedad</h1>

        <a href="/admin" class="boton boton-verde"> <- Volver</a>

        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach; ?>
            
        <form class="formulario" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Información general</legend>

                <!-- Se agrega en el value la variable de PHP para que se quede guardada y no tenga que sobreescribirla -->
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" placeholder="Título Propiedad" value="<?php echo $titulo; ?>">

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" placeholder="Precio Propiedad" value="<?php echo $precio; ?>">

                <label for="imagen">Imágen:</label>
                <input type="file" id="imagen" accept="image/jpeg, image/png" name="imagen">

                <img src="/imagenes/<?php echo $imagenPropiedad; ?>" alt="Imágen propiedad" class="imagen-small">

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

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>

    </main>
<?php 

    incluirTemplate('footer');
?>