<?php
include 'config.php'; // Configuración, sesiones y funciones

$archivo = DATA_DIR . '/featured_streamers.json';

// Inicializar featured si no existe o se pulsa reset
if (!file_exists($archivo) || isset($_POST['reset'])) {
    $roster = leerJSON(DATA_DIR . '/roster_completo.json') ?? [];
    $featured = array_slice($roster, 0, 20); // primeros 20
    guardarJSON($archivo, $featured);
} else {
    $featured = leerJSON($archivo) ?? [];
}

// Rotación diaria
if (isset($_POST['rotar'])) {
    if (!empty($featured)) {
        array_shift($featured); // quitar primero
        $featured[] = ['username'=>'invitado_especial','nombre_real'=>'Invitado Especial','followers'=>0,'avatar'=>'invitado_especial.png','juego_favorito'=>'Especial'];
        guardarJSON($archivo, $featured);
        echo "✅ Lista de featured actualizada correctamente\n";
    }
}

// Formularios
echo '<form method="POST">
        <button type="submit" name="rotar">🔄 Rotar Featured</button>
        <button type="submit" name="reset">♻️ Reset Featured</button>
      </form>';

// Mostrar streamers
foreach ($featured as $f) {
    echo $f['username'] . ' | ' . $f['avatar'] . "\n";
}
?>
