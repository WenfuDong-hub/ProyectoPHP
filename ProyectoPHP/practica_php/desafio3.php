<?php
include 'config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}