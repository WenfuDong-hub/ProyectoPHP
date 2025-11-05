<?php
// desafio1.php
require_once 'config.php';
require_once 'functions-structure.php';

$errores = [];
$exito = '';
$ganadores_list = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!comprobarTokenCSRF($token)) {
        $errores[] = 'Token CSRF inválido.';
    } else {
        $raw = $_POST['viewers'] ?? '';
        $v = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 50, 'max_range' => 200]]);
        if ($v === false) {
            $errores[] = "❌ ¡Ops! El chat debe tener entre 50 y 200 viewers";
        } else {
            $_SESSION['viewers_chat'] = $v;
            // calcular N ganadores
            $maxN = max(5, floor($v / 10));
            $N = rand(5, $maxN);
            // elegir N avatares aleatorios
            $avatares = listarAvatares(20);
            shuffle($avatares);
            $ganadores_list = array_slice($avatares, 0, $N);
            $exito = "Felicidades a los {$N} ganadores del sorteo";

            // registrar en logs/sorteos.txt
            $time = date('Y-m-d H:i:s');
            $line = "{$time} | viewers={$v} | ganadores={$N} | lista=" . implode(',', $ganadores_list);
            escribirTXT(LOGS_DIR . '/sorteos.txt', $line, true);
        }
    }
}

myHeader();
?>

<section class="challenge">
  <h1>Desafío 1 - Sorteo del Chat</h1>

  <?php if (!empty($errores)): ?>
    <?php foreach ($errores as $e): ?>
        <p class='err'><?php echo $e; ?></p>
    <?php endforeach; ?>
  <?php endif; ?>

  <form method="post" action="desafio1.php" class="form-challenge">
    <input type="hidden" name="csrf_token" value="<?php echo generarTokenCSRF(); ?>">
    <label>¿Cuántos viewers hay en el chat? <input type="number" name="viewers" min="50" max="200" required></label>
    <small>Debe ser un número entre 50 y 200</small>
    <button type="submit">Realizar sorteo</button>
  </form>

  <?php if ($exito): ?>
    <div class="success"><p><?php echo $exito; ?></p></div>
    <div class="winners">
      <?php foreach ($ganadores_list as $g): ?>
        <div class="winner-card">
          <img src="images/streamers/<?php echo urlencode($g); ?>" alt="<?php echo htmlspecialchars($g); ?>">
          <div><?php echo pathinfo($g, PATHINFO_FILENAME); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php myFooter(); ?>