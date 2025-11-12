<?php
include 'config.php';

// Verificar que el usuario esté logueado
if (!isset($_SESSION['username_gamer'])) {
    header('Location: index.php');
    exit;
}

//Fichero donde se guarda
$archivo_roster = 'data/roster_completo.json';
$resultado = '';

$usuarios = ["auron","Axozer","elmillor","folagor","grefg","ibai","illojuan","knekro","lolito","mario","rubius","shiro","spreen","spurs","staxx","vegeta","viruzz","werlyb","willy","xokas"];
$nombres = ["Raul Fernández", "Carlos Pérez", "Lucía Gómez", "Mario Ruiz", "Ana Martínez", "José López", "Laura Sánchez", "David Ramírez", "Marta Torres", "Javier Díaz", "Sofía Morales", "Andrés Herrera", "Isabel Castro", "Miguel Navarro", "Carolina Rojas", "Fernando Silva", "Paula Ortega", "Ricardo Vázquez", "Elena Jiménez", "Luis Cabrera"];


$juegos = ["Fortnite", "Valorant", "Minecraft", "LOL", "Among Us", "Fall Guys", "Rocket League"];

// Si se pulsa el botón de generar nuevo roster
if (isset($_POST['nuevo_roster'])) {
    if (file_exists($archivo_roster)) {
        unlink($archivo_roster); // Borra el archivo anterior
    }

    $roster = [];
    foreach ($usuarios as $index => $user) {
        $roster[] = [
            'username' => $user,
            'nombre_real' => $nombres[$index],
            'followers' => rand(5000, 100000),
            'avatar' => $user . ".jpg",
            'juego_favorito' => $juegos[array_rand($juegos)]
        ];
    }

    file_put_contents($archivo_roster, json_encode($roster, JSON_PRETTY_PRINT));
} else {
    // Cargar roster existente
    if (file_exists($archivo_roster)) {
        $roster = json_decode(file_get_contents($archivo_roster), true);
    } else {
        $roster = [];
        foreach ($usuarios as $index => $user) {
            $roster[] = [
                'username' => $user,
                'nombre_real' => $nombres[$index],
                'followers' => rand(5000, 100000),
                'avatar' => $user . ".jpg",
                'juego_favorito' => $juegos[array_rand($juegos)]
            ];
        }
        file_put_contents($archivo_roster, json_encode($roster, JSON_PRETTY_PRINT));
    }
}
// Dividir equipos
list($teamChaos, $teamOrder) = dividirEquipos($roster);

// Calcular followers
$totalChaos = calcularFollowers($teamChaos);
$totalOrder = calcularFollowers($teamOrder);

// Mostrar header
mostrarHeader("DESAFÍO 3 - Formación de Equipos para el Torneo");
?>

<main class="dashboard">
    <div class="challenge-container">
        <?php
            formularioDesafio3($teamChaos, $teamOrder, $totalChaos, $totalOrder);
        ?>
    </div>
</main>

<?php
mostrarFooter();
?>
