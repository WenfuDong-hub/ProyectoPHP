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

    // --- BOTÓN ROTAR FEATURED ---
    if (isset($_POST['rotar'])) {
        // Eliminar el primer streamer (el destacado del día anterior)
        array_shift($featured);

        // Añadir el nuevo invitado especial al final
        if (!in_array('invitado_especial.png', $featured)) {
            $featured[] = 'invitado_especial.png';
        }

        // Guardar en el archivo JSON
        guardarJSON($archivo_featured, $featured);

        $resultado = "✅ Lista de featured actualizada correctamente";

        // Marcar desafío como completado
        if (!in_array(2, $_SESSION['desafios_completados'])) {
            $_SESSION['desafios_completados'][] = 2;
            $_SESSION['nivel_usuario']++;
        }
    }

    // --- BOTÓN RESET FEATURED ---
    elseif (isset($_POST['reset'])) {
        // Recuperar los avatares originales desde la carpeta de streamers
        $featured = listarAvatares(20);

        // Asegurarse de quitar el invitado especial si estuviera
        $featured = array_filter($featured, fn($f) => $f !== 'invitado_especial.png');

        // Guardar el orden original en el JSON
        guardarJSON($archivo_featured, array_values($featured));

        // Recargar el array desde el archivo para reflejar los cambios
        $featured = leerJSON($archivo_featured);

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
