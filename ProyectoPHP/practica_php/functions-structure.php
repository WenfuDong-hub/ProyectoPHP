<?php
// functions-structure.php
require_once 'config.php';

function myHeader() {
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

function myFooter() {
    $racha = $_COOKIE['racha_dias'] ?? 0;
    $ultima = $_COOKIE['ultima_visita'] ?? 'Nunca';
    echo <<<HTML
</main>
<footer class="footer-fixed">
  <div>Racha: {$racha} días | Última visita: {$ultima}</div>
  <div>Crew Streamers</div>
</footer>
</body>
</html>
HTML;
}

function println($s) { echo '<p>' . $s . '</p>'; }
?>
