<?php
// config.php
// Configuración global: sesiones, funciones de archivos, CSRF, sanitización y logging.

const ROOT = "/PROYECTOPHP";
const COMPANY = "Proveçana";
const AUTORS = "Khawar y Wenfu";

session_start(); // iniciar sesión siempre



// --- CSRF ---
function generarTokenCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function comprobarTokenCSRF($token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Rutas base ---
define('BASE_DIR', __DIR__);
define('DATA_DIR', BASE_DIR . '/data');
define('LOGS_DIR', BASE_DIR . '/logs');
define('IMAGES_STREAMERS', BASE_DIR . '/images/streamers');

// Asegurar carpetas
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(LOGS_DIR)) mkdir(LOGS_DIR, 0755, true);
if (!is_dir(IMAGES_STREAMERS)) mkdir(IMAGES_STREAMERS, 0755, true);

// --- Gestión de archivos: JSON, TXT, CSV ---
function leerJSON(string $archivo) {
    if (!file_exists($archivo)) return null;
    $c = file_get_contents($archivo);
    return json_decode($c, true);
}
function guardarJSON(string $archivo, $datos): bool {
    $dir = dirname($archivo);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $json = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents($archivo, $json) !== false;
}
function leerTXT(string $archivo): string {
    if (!file_exists($archivo)) return '';
    return file_get_contents($archivo);
}
function escribirTXT(string $archivo, string $contenido, bool $append = true): bool {
    $dir = dirname($archivo);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if ($append) {
        return file_put_contents($archivo, $contenido . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
    } else {
        return file_put_contents($archivo, $contenido, LOCK_EX) !== false;
    }
}
function leerCSV(string $archivo): array {
    if (!file_exists($archivo)) return [];
    $rows = [];
    if (($h = fopen($archivo, 'r')) !== false) {
        while (($data = fgetcsv($h, 1000, ',')) !== false) {
            $rows[] = $data;
        }
        fclose($h);
    }
    return $rows;
}
function guardarCSV(string $archivo, array $datos): bool {
    $dir = dirname($archivo);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $h = fopen($archivo, 'w');
    if ($h === false) return false;
    foreach ($datos as $row) {
        fputcsv($h, $row);
    }
    fclose($h);
    return true;
}

// --- Logging ---
function logAccion(string $mensaje, string $archivo = LOGS_DIR . '/errores.log') {
    $time = date('Y-m-d H:i:s');
    escribirTXT($archivo, "[$time] $mensaje", true);
}

// --- Sanitización ---
function limpiar(string $s): string {
    return htmlspecialchars(trim($s), ENT_QUOTES, 'UTF-8');
}

// --- Helper: lista de imágenes (hasta 20) ---
function listarAvatares(int $limit = 20): array {
    $imgs = [];
    if (!is_dir(IMAGES_STREAMERS)) return $imgs;
    $files = scandir(IMAGES_STREAMERS);
    foreach ($files as $f) {
        if (preg_match('/\.(png|jpe?g|gif)$/i', $f)) $imgs[] = $f;
    }
    sort($imgs); // orden
    return array_slice($imgs, 0, $limit);
}

// --- Helper: comprobar username válido ---
function validarUsername(string $u): bool {
    return preg_match('/^[A-Za-z0-9_-]{3,20}$/', $u) === 1;
}

// --- Helper: calcular diferencia dias entre fechas (Y-m-d) ---
function diasEntre($fecha1, $fecha2): int {
    $d1 = new DateTime($fecha1);
    $d2 = new DateTime($fecha2);
    return (int)$d1->diff($d2)->format('%a');
}

?>
