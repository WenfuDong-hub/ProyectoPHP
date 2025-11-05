<?php
include 'config.php'; // Importamos config.php para usar su configuración

echo "<h1>Bienvenido a " . COMPANY . "</h1>";
echo "<p>Autores: " . AUTORS . "</p>";

// Comprobamos si existe una variable de sesión 'test'
if (isset($_SESSION['test'])) {
  echo "<p>Mensaje de sesión: " . $_SESSION['test'] . "</p>";
} else {
  echo "<p>No hay mensaje de sesión guardado todavía.</p>";
}

echo "<h2>Test de lectura de archivos:</h2>";

// Leer un txt de ejemplo
$archivoTxt = DATA_DIR . '/juegos_trending.txt';
echo "<h3>Contenido de juegos_trending.txt:</h3>";
echo "<pre>" . leerArchivoTexto($archivoTxt) . "</pre>";

// Leer un JSON de ejemplo
$archivoJson = DATA_DIR . '/featured_streamers.json';
$streamers = leerArchivoJSON($archivoJson);

echo "<h3>Primer streamer del JSON:</h3>";
if (!empty($streamers)) {
    echo "<p>Nombre: " . $streamers[0]['nombre'] . "</p>";
    echo "<p>Juego: " . $streamers[0]['juego'] . "</p>";
} else {
    echo "<p>No se pudo leer el JSON o está vacío.</p>";
}

?>
