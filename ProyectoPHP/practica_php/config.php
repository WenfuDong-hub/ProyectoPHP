<?php

// config.php
// Configuración global: sesiones, funciones de archivos, CSRF, sanitización y logging.

const COMPANY = "Proveçana";
const AUTORS = "Khawar y Wenfu";

//Rutas de ficheros
const ROOT = "/PHP/ProyectoPHP-main/ProyectoPHP/practica_php";
const BASE_DIR = __DIR__;
const IMAGES_STREAMERS = BASE_DIR . '/images/streamers';

session_start();

/////////////////////////////////////////////////////////////////////////////////////////
// Funcion para que funcione el logout en todas las paginas
/////////////////////////////////////////////////////////////////////////////////////////

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    // Registrar en log
    logout();

    // Limpiar todas las variables de sesión
    $_SESSION = [];

    // Borrar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    // Destruir la sesión en el servidor
    session_destroy();

    // Redirigir al home
    header('Location: index.php');
    exit;
}

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para archivos
/////////////////////////////////////////////////////////////////////////////////////////

function leerJSON($archivo)
{
    if (file_exists($archivo)) {
        $contenido = file_get_contents($archivo);
        return json_decode($contenido, true);
    }
    return [];
}

function guardarJSON($archivo, $datos)
{
    $json = json_encode($datos, JSON_PRETTY_PRINT);
    file_put_contents($archivo, $json);
}

function listarAvatares(int $limit = 20): array
{
    $imgs = [];
    if (!is_dir(IMAGES_STREAMERS)) return $imgs;
    $files = scandir(IMAGES_STREAMERS);
    foreach ($files as $f) {
        if (preg_match('/\.(png|jpe?g|gif)$/i', $f)) $imgs[] = $f;
    }
    sort($imgs); // orden
    return array_slice($imgs, 0, $limit);
}

function leerTXT($archivo)
{
    if (file_exists($archivo)) {
        return file_get_contents($archivo);
    }
    return "";
}

function escribirTXT($archivo, $contenido)
{
    file_put_contents($archivo, $contenido . PHP_EOL, FILE_APPEND);
}

function logAccion($mensaje)
{
    $fecha = date('Y-m-d H:i:s');
    $log = "[$fecha] $mensaje" . PHP_EOL;
    file_put_contents('logs/errores.log', $log, FILE_APPEND);
}

function generarTokenCSRF()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function comprobarTokenCSRF($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function limpiar($texto)
{
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

/////////////////////////////////////////////////////////////////////////////////////////
// Funcion para LOGOUT
/////////////////////////////////////////////////////////////////////////////////////////

function logout()
{
    // Registrar en log la acción de logout
    if (isset($_SESSION['username_gamer'])) {
        $username = $_SESSION['username_gamer'];
        $duracion_sesion = time() - $_SESSION['timestamp_inicio'];
        $minutos = floor($duracion_sesion / 60);

        $mensaje_log = "LOGOUT - Usuario: $username, Duración sesión: $minutos minutos, Nivel: {$_SESSION['nivel_usuario']}";
        logAccion($mensaje_log);
    }
}

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para ESTRUCTURA o ESQUELETO del SITE
/////////////////////////////////////////////////////////////////////////////////////////

function mostrarHeader($titulo_pagina = "Crew de Streamers")
{

    $username = $_SESSION['username_gamer'] ?? 'Invitado';
    $nivel = $_SESSION['nivel_usuario'] ?? 1;
    echo <<<HTML
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crew Streamers</title>
<link rel="stylesheet" href="css/gaming-styles.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron&family=Rajdhani&display=swap" rel="stylesheet">
</head>
<body class="theme-dark">
<header class="header-fixed">
  <div class="header-inner">
    <div class="brand">🎮 Crew Streamers</div>
    <nav class="main-nav">
      <a href="index.php">🏠 Home</a>
      <a href="desafio1.php">🎯 Desafío 1</a>
      <a href="desafio2.php">🔥 Desafío 2</a>
      <a href="desafio3.php">⚡ Desafío 3</a>
      <a href="desafio4.php">🏆 Desafío 4</a>
      <a href="desafio5.php">💎 Desafío 5</a>
    </nav>
    <div class="user-info">{$username} <span class="nivel">| Nivel {$nivel}</span></div>
  </div>
</header>
<main class="container">
HTML;
}

function mostrarFooter()
{
    $racha = $_COOKIE['racha_dias'] ?? 0;
    $ultima = $_COOKIE['ultima_visita'] ?? 'Nunca';
    echo <<<HTML
</main>
<footer class="footer-fixed">
  <div>Racha: {$racha} días | Última visita: {$ultima}</div>
  <div>Crew Streamers</div>
  <form method="post" style="display:inline;">
    <button type="submit" name="logout" class="btn-logout">Cerrar Sesión</button>
</form>
</footer>
</body>
</html>
HTML;
}

/////////////////////////////////////////////////////////////////////////////////////////
// Funciones para imprimir HTMLs (formularios...)
/////////////////////////////////////////////////////////////////////////////////////////

function formularioDesafio1($error, $resultado, $ganadores)
{

    $viewers_chat = isset($_SESSION['viewers_chat']) ? $_SESSION['viewers_chat'] : '';


    $form = <<<HTML
        <div class="form-section">
            <h2>Configuración del Sorteo</h2>
           
            <form method="POST">
                <label for="viewers">¿Cuántos viewers hay en el chat? (50-200)</label>
                <input type="number" id="viewers" name="viewers"
                       value="{$viewers_chat}">
    HTML;

    // Si hay error, añadir bloque de error
    if ($error) {
        $form .= <<<HTML
            <div class="error">$error</div>
    HTML;
    }

    //Cierra el formulario
    $form .= <<<HTML
                <button type="submit">Iniciar Sorteo</button>
            </form>
        </div>
    HTML;

    echo $form;

    if ($resultado) {
?>
        <div class="result-section">
            <h2><?= $resultado ?></h2>

            <?php if (isset($ganadores)) : ?>
                <div class="ganadores-grid">
                    <?php foreach ($ganadores as $ganador) : ?>
                        <img src="<?= $ganador ?>" alt="Ganador" class="avatar">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <p class="success">✅ Sorteo registrado en el log correctamente</p>
            <p class="success">🎉 ¡Desafío completado! Nivel subido a <?= $_SESSION['nivel_usuario'] ?></p>
        </div>
<?php
    }
}

function formularioDesafio2($resultado, $featured, $avatares)
{
    $form = <<<HTML
    <div class="form-section">
        <h1>🔥 DESAFÍO 2 - Rotación de Featured Streamers</h1>

        <form method="post" class="form-challenge">
            <button type="submit" name="rotar">🔄 Rotar Featured</button>
            <button type="submit" name="reset">♻️ Reset Featured</button>
        </form>
HTML;

    if ($resultado) {
        $form .= <<<HTML
        <p class="success">{$resultado}</p>
HTML;
    }

    $form .= <<<HTML
        <h2>Featured Streamers</h2>
        <div class="avatars-grid">
HTML;

    foreach ($featured as $f) {
        // Saltar el invitado especial en el listado principal
        if ($f === 'invitado_especial.png') continue;

        $ruta_web = 'images/streamers/' . htmlspecialchars($f);
        $nombre = pathinfo($f, PATHINFO_FILENAME);
        $form .= <<<HTML
            <div class="avatar-card">
                <img src="{$ruta_web}" alt="Avatar">
                <p>{$nombre}</p>
            </div>
HTML;
    }

    $form .= <<<HTML
        </div>
HTML;

    // Mostrar invitado especial separado
    if (in_array('invitado_especial.png', $featured)) {
        $ruta_especial = 'images/invitado_especial/invitado_especial.png';
        $form .= <<<HTML
        <h2>Invitado Especial</h2>
        <div class="avatars-grid">
            <div class="avatar-card">
                <img src="{$ruta_especial}" alt="Invitado Especial">
                <p>Invitado Especial</p>
            </div>
        </div>
HTML;
    }

    $form .= <<<HTML
        <h2>Todos los Streamers</h2>
        <div class="avatars-grid">
HTML;

    foreach ($avatares as $f) {
        $ruta_web = 'images/streamers/' . htmlspecialchars($f);
        $nombre = pathinfo($f, PATHINFO_FILENAME);
        $form .= <<<HTML
            <div class="avatar-card">
                <img src="{$ruta_web}" alt="Avatar">
                <p>{$nombre}</p>
            </div>
HTML;
    }

    $form .= <<<HTML
        </div>
    </div>
HTML;

    echo $form;
}
