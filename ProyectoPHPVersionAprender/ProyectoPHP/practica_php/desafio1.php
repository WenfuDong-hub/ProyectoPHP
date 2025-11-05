<?php
include 'config.php'; // Incluimos la configuración

$errores = [];
$viewers = 0;

// Procesar formulario si se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Comprobamos CSRF
    if (!comprobarTokenCSRF($_POST['csrf'] ?? '')) {
        $errores[] = "❌ Token CSRF inválido, recarga la página.";
    }

    // Validación
    $input = $_POST['viewers'] ?? '';
    if (empty($input)) {
        $errores[] = "❌ ¡Ops! Debes introducir el número de viewers.";
    } elseif (!filter_var($input, FILTER_VALIDATE_INT)) {
        $errores[] = "❌ El valor debe ser numérico.";
    } elseif ($input < 50 || $input > 200) {
        $errores[] = "❌ ¡Ops! El chat debe tener entre 50 y 200 viewers.";
    } else {
        $viewers = (int)$input;
        $_SESSION['viewers'] = $viewers; // Guardamos en sesión
    }
}

// Generar token CSRF para el formulario
$token = generarTokenCSRF();
?>

<form method="POST" action="">
    <label for="viewers">¿Cuántos viewers hay en el chat?</label>
    <input type="number" name="viewers" id="viewers" value="<?= htmlspecialchars($viewers) ?>" min="50" max="200" required>
    <input type="hidden" name="csrf" value="<?= $token ?>">
    <button type="submit">¡Enviar!</button>
</form>

<!-- Mostrar ganadores -->
<?php
if ($viewers > 0 && empty($errores)) {

    // Número de ganadores aleatorio entre 5 y floor(viewers/10)
    $maxGanadores = max(5, floor($viewers / 10));
    $numGanadores = rand(5, $maxGanadores);

    // Leer roster de streamers
    $roster = leerJSON(DATA_DIR . '/roster_completo.json') ?? [];

    if (!empty($roster)) {
        shuffle($roster); // Mezclar array
        $ganadores = array_slice($roster, 0, $numGanadores);

        echo "<h2>🎉 ¡Felicidades a los $numGanadores ganadores del sorteo!</h2>";
        echo "<div class='ganadores'>";
        foreach ($ganadores as $g) {
            echo "<div class='ganador'>
                    <img src='images/streamers/{$g['avatar']}' width='80'><br>
                    {$g['username']}
                  </div>";
        }
        echo "</div>";

        // Guardar log
        $log = date('Y-m-d H:i:s') . " | Viewers: $viewers | Ganadores: " . implode(", ", array_column($ganadores, 'username'));
        escribirTXT(LOGS_DIR . '/sorteos.txt', $log);
    } else {
        echo "<p>No hay streamers en el roster.</p>";
    }
}
?>