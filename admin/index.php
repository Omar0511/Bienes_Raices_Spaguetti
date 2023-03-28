<?php 
    //require 'include/funciones.php';
    require '../include/funciones.php';
    $auth = estaAutenticado();
    
    if (!$auth) {
        header('Location: /');
    }

    //Imoporta conexión
    require '../include/config/database.php';
    $conexion = conectarDB();

    //Escribir Query
    $query = "SELECT * FROM propiedades;";

    //Consultar BD
    $consulta = mysqli_query($conexion, $query);

    /*
        Leemos el query string que viene del header de crear.php
        los 2 signos ?? indican algo negativo, es decir;
        sino existe agregará null,
        anteriormente era con isset
        isset($_GET['resultado'] ?? null):
    */
    $resultado = $_GET['resultado'] ?? null;

    //Eliminar propiedades
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if ($id) {

            //Eliminar archivo
            $query = "SELECT imagen FROM propiedades WHERE id = ${id};";
            $resultado = mysqli_query($conexion, $query);
            $propiedad = mysqli_fetch_assoc($resultado);
            unlink('../imagenes/' . $propiedad['imagen']);

            //Eliminar propiedad
            $query = "DELETE FROM propiedades WHERE id = ${id};";

            $resultado = mysqli_query($conexion, $query);

            if ($resultado) {
                header('Location: /admin?resultado=3');
            }
        }
    }

    incluirTemplate('header');
?>    
    <main class="contenedor">
        <h1>Administrador de Bienes Raíces</h1>

        <!-- intval es como un parseo, parse_int, int.parse, etc -->
        <?php if (intval($resultado) === 1) : ?>
            <p class="alerta exito">Creado exitosamente!</p>
        <?php elseif(intval($resultado) === 2) : ?>
            <p class="alerta exito">Actualizado exitosamente</p>
        <?php elseif(intval($resultado) === 3) : ?>
            <p class="alerta exito">Eliminado exitosamente</p>
        <?php endif; ?>

        <!-- <a href="../admin/propiedades/crear.php" class="boton-verde">Nueva propiedad</a> -->
        <a href="/admin/propiedades/crear.php" class="boton boton-verde">Nueva propiedad</a>

        <table class="propiedades">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Imágen</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            
            <!--
                <tbody>  -- Mostrar resultados 
                    <tr>
                        <td>1</td>
                        <td>Casa en la playa</td>
                        <td> <img src="../build/img/anuncio1.jpg" alt="" class="imagen-tabla"> </td>
                        <td>$1200</td>
                        <td>
                            <a href="" class="boton boton-rojo-block">Eliminar</a>
                            <a href="../admin/propiedades/actualizar.php" class="boton boton-amarillo-block">Actualizar</a>
                        </td>
                    </tr>
                </tbody>
            -->

            <tbody>
                <?php while($propiedad = mysqli_fetch_assoc($consulta)): ?>
                    <tr>
                        <td> <?php echo $propiedad['id']; ?> </td>
                        <td> <?php echo $propiedad['titulo']; ?> </td>
                        <td> <img src="/imagenes/<?php echo $propiedad['imagen']; ?>" alt="Imágen de propiedad" class="imagen-tabla"> </td>
                        <td> $ <?php echo $propiedad['precio']; ?>  </td>
                        <td>                            

                            <form method="POST" class="w-100">

                                <!-- hidden = indica que es oculto, obtenemos el ID para ELIMINAR -->
                                <input type="hidden" name="id" value="<?php echo $propiedad['id']; ?>">

                                <input type="submit" class="boton boton-rojo-block" value="Eliminar">
                            </form>

                            <a href="../admin/propiedades/actualizar.php?id=<?php echo $propiedad['id']; ?>" class="boton boton-amarillo-block">Actualizar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>            

        </table>
    </main>
<?php 
    //Cerrar conexión a la BD
    mysqli_close($conexion);

    //include './include/templates/footer.php';
    incluirTemplate('footer');
?>