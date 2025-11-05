<?php
// index.php
require_once 'config.php';
require_once 'functions-structure.php';

$visitas_file = DATA_DIR . '/visitas.txt';

// PROCESO: formulario para username (si no hay sesión)
$errores = [];
if (empty($_SESSION['username_gamer']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!comprobarTokenCSRF($token)) {
        $errores[] = "Token CSRF inválido.";
    } else {
        $u = $_POST['username'] ?? '';
        $u = limpiar($u);
        if ($u === '') $errores[] = "El username no puede estar vacío.";
        elseif (!validarUsername($u)) $errores[] = "El username debe tener 3-20 caracteres y solo letras, números, guiones y guiones bajos.";
        else {
            $_SESSION['username_gamer'] = $u;
            $_SESSION['nivel_usuario'] = 1;
            $_SESSION['desafios_completados'] = [];
            $_SESSION['timestamp_inicio'] = time();
            // establecer cookies por defecto
            setcookie('tema_preferido', 'dark', time()+30*24*3600, '/');
            setcookie('vista_preferida', 'grid', time()+30*24*3600, '/');
            header('Location: index.php');
            exit;
        }
    }
}

// GESTIÓN COOKIES: ultima_visita y racha_dias (30 días)
$now_str = date('Y-m-d H:i:s');
$today_date = date('Y-m-d');

$ultima_visita = $_COOKIE['ultima_visita'] ?? null;
$racha = isset($_COOKIE['racha_dias']) ? (int)$_COOKIE['racha_dias'] : 0;

if ($ultima_visita) {
    $ultima_date = substr($ultima_visita, 0, 10);
    // si la última visita fue ayer -> +1, si hoy -> no cambiar, si gap -> reset 1
    if ($ultima_date === $today_date) {
        // ya estuvo hoy
    } else {
        $diff = diasEntre($ultima_date, $today_date);
        if ($diff === 1) $racha++;
        else $racha = 1;
    }
} else {
    $racha = 1; // primera vez
}
// actualizar cookies
setcookie('ultima_visita', $now_str, time()+30*24*3600, '/');
setcookie('racha_dias', $racha, time()+30*24*3600, '/');

// CONTADOR visitas (archivo)
$visitas = (int) leerTXT($visitas_file);
$visitas++;
escribirTXT($visitas_file, (string)$visitas, false); // sobrescribe con el número actual

// LISTAR AVATARES (20)
$avatares = listarAvatares(20);

// Mostrar plantilla
myHeader();
?>

<div class="home-content">
  <?php if (!empty($_SESSION['username_gamer'])): ?>
    <h1>¡Bienvenido de nuevo, <?php echo limpiar($_SESSION['username_gamer']); ?>! 🎮</h1>
    <p>Visitas totales al dashboard: <?php echo $visitas; ?></p>
    <p>Última conexión: <?php echo htmlspecialchars($_COOKIE['ultima_visita'] ?? 'Nunca'); ?></p>
    <p>Racha de días: <?php echo (int)$_COOKIE['racha_dias']; ?></p>

    <section class="avatars-section">
      <h2>Avatares del crew</h2>
      <div class="avatars-grid">
        <?php foreach ($avatares as $img): ?>
          <div class="avatar-card">
            <img src="images/streamers/<?php echo urlencode($img); ?>" alt="<?php echo htmlspecialchars($img); ?>">
            <div class="avatar-name"><?php echo pathinfo($img, PATHINFO_FILENAME); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

  <?php else: ?>
    <h1>Bienvenido a Crew Streamers 🎮</h1>
    <p>Elige tu username gamer para empezar:</p>
    <?php if (!empty($errores)): ?>
      <div class="errors">
        <?php foreach ($errores as $e) echo "<p class='err'>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>
    <form method="post" action="index.php" class="form-username">
      <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
      <label>Username gamer: <input type="text" name="username" maxlength="20" required></label>
      <button type="submit">Comenzar</button>
    </form>
  <?php endif; ?>
</div>

<?php
myFooter();
?>
