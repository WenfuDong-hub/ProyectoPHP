<?php
// index.php
require_once 'config.php';

// Ruta del archivo de visitas
$visitas_file = 'data/visitas.txt';

// Procesar formulario de username 
$errores = [];

// Manejar logout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout(); // registrar en log

    // destruir sesión completamente
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();

    // redirigir al login
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['username_gamer']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  if ($u === '') {
    $errores[] = "El username no puede estar vacío.";
  } elseif (!preg_match('/^[A-Za-z0-9_-]{3,20}$/', $u)) {
    $errores[] = "El username debe tener entre 3 y 20 caracteres y solo letras, números, guiones o guiones bajos.";
  } else {
    $_SESSION['username_gamer'] = $u;
    $_SESSION['nivel_usuario'] = 1;
    $_SESSION['desafios_completados'] = [];
    $_SESSION['timestamp_inicio'] = time();

    // Cookies por defecto
    setcookie('tema_preferido', 'dark', time() + 30 * 24 * 3600, '/');
    setcookie('vista_preferida', 'grid', time() + 30 * 24 * 3600, '/');

    // Registrar log de inicio
    logAccion("LOGIN - Usuario: $u");

    header('Location: index.php');
    exit;
  }
}

// GESTIÓN DE COOKIES: última visita y racha
$now_str = date('Y-m-d H:i:s');
$today = date('Y-m-d');

$ultima_visita = $_COOKIE['ultima_visita'] ?? null;
$racha = isset($_COOKIE['racha_dias']) ? (int)$_COOKIE['racha_dias'] : 0;

if ($ultima_visita) {
  $ultima_fecha = substr($ultima_visita, 0, 10);
  if ($ultima_fecha === $today) {
    // ya visitó hoy
  } elseif ($ultima_fecha === date('Y-m-d', strtotime('-1 day'))) {
    $racha++;
  } else {
    $racha = 1;
  }
} else {
  $racha = 1;
}

setcookie('ultima_visita', $now_str, time() + 30 * 24 * 3600, '/');
setcookie('racha_dias', $racha, time() + 30 * 24 * 3600, '/');

// CONTADOR de visitas
$visitas = 0;
if (file_exists($visitas_file)) {
  $visitas = (int) leerTXT($visitas_file);
}
$visitas++;
file_put_contents($visitas_file, $visitas);

// LISTAR AVATARES (20)
$avatares = listarAvatares(20);

// Mostrar estructura HTML
mostrarHeader();
?>

<div class="home-content">
  <?php if (!empty($_SESSION['username_gamer'])): ?>
    <h1>¡Bienvenido de nuevo, <?= htmlspecialchars($_SESSION['username_gamer']) ?>! 🎮</h1>
    <p>Visitas totales al dashboard: <?= $visitas ?></p>
    <p>Última conexión: <?= htmlspecialchars($_COOKIE['ultima_visita'] ?? 'Nunca') ?></p>
    <p>Racha de días: <?= (int)($_COOKIE['racha_dias'] ?? 1) ?></p>

    <?php if (!empty($avatares)): ?>
      <?php foreach ($avatares as $nombre_avatar): ?>
        <?php
        $ruta_web = ROOT . '/images/streamers/' . htmlspecialchars($nombre_avatar);
        ?>
        <img src="<?= $ruta_web ?>" alt="Avatar" style="width: 150px; height: 150px; margin: 10px;">
      <?php endforeach; ?>
    <?php endif; ?>

  <?php else: ?>
    <section class="login-screen">
      <div class="login-card glass">
        <h1 class="title">🎮 Crew Streamers</h1>
        <p class="subtitle">Elige tu <strong>username gamer</strong> para comenzar tu aventura:</p>

        <?php if (!empty($errores)): ?>
          <div class="errors">
            <?php foreach ($errores as $e) echo "<p class='err'>⚠️ " . htmlspecialchars($e) . "</p>"; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="index.php" class="login-form">
          <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
          <input type="text" name="username" maxlength="20" placeholder="Escribe tu nickname..." required class="input-gamer">
          <button type="submit" class="btn-gamer">🚀 Comenzar</button>
        </form>
      </div>
    </section>
  <?php endif; ?>

</div>

<?php
mostrarFooter();
?>