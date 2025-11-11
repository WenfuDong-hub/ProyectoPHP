<?php
include 'config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

// Archivos
$archivo_featured = 'data/featured_streamers.json';
$avatares = listarAvatares(20);

// Leer archivo featured, si no existe, inicializar con los 20 avatares
$featured = leerJSON($archivo_featured);
if (empty($featured)) {
    $featured = $avatares;
    guardarJSON($archivo_featured, $featured);
}

$resultado = '';

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['rotar'])) {
        // Guardar el primer streamer y rotar la lista
        $primero = array_shift($featured); // quita el primero
        array_push($featured, $primero);   // lo pone al final

        // Guardar en el archivo JSON
        guardarJSON($archivo_featured, $featured);

        $resultado = "✅ Lista de featured actualizada correctamente";

        // Marcar desafío como completado
        if (!in_array(2, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 2;
            $_SESSION['nivel_usuario']++;
        }
    } elseif (isset($_POST['reset'])) {
        $featured = $avatares;
        guardarJSON($archivo_featured, $featured);
        $resultado = "✅ Featured reseteado al orden original";
    }
}

// Mostrar header
mostrarHeader("Desafío 2 - Rotación de Featured Streamers");
?>

<main class="dashboard">
    <div class="challenge-container">
        <?php
        formularioDesafio2($resultado, $featured, $avatares);
        ?>
    </div>
</main>

<?php
mostrarFooter();
?>
