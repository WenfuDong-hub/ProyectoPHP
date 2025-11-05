<?php
// config.php
// Este archivo guarda la configuración general del proyecto.

// Esto sirve para poder recordar información del usuario 
session_start();

const ROOT = "/PROYECTOPHP";
const COMPANY = "Proveçana";
const AUTORS = "Khawar y Wenfu";

// --- Rutas principales ---
const BASE_DIR = __DIR__; // Carpeta donde está este archivo 
const DATA_DIR = BASE_DIR . '/data'; // Carpeta donde guardaremos archivos 
const LOGS_DIR = BASE_DIR . '/logs'; // Carpeta donde se guardarán los registros 
const IMAGES_STREAMERS = BASE_DIR . '/images/streamers'; // Carpeta con las imágenes de los streamers

// =============================
// FUNCIONES BÁSICAS DE ARCHIVOS
// =============================

// Leer archivos de texto (.txt)
function leerArchivoTexto($ruta) {
    if (!file_exists($ruta)) {
        error_log("Error: el archivo '$ruta' no existe.", 3, LOGS_DIR . '/errores.log');
        return "Archivo no encontrado.";
    }
    return file_get_contents($ruta);
}

// Leer archivos JSON (.json)
function leerArchivoJSON($ruta) {
    if (!file_exists($ruta)) {
        error_log("Error: el archivo '$ruta' no existe.", 3, LOGS_DIR . '/errores.log');
        return [];
    }

    $contenido = file_get_contents($ruta);
    return json_decode($contenido, true); // true = array asociativo
}

// Escribir texto en un archivo (para logs o registros)
function escribirArchivo($ruta, $mensaje) {
    $fecha = date("Y-m-d H:i:s");
    $linea = "[$fecha] $mensaje\n";
    file_put_contents($ruta, $linea, FILE_APPEND);
}
?>
