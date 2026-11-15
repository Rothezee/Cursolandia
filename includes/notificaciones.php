<?php
function notificar($conn, $usuarioId, $mensaje, $enlace = null){
    $stmt = $conn->prepare('INSERT INTO notificaciones (usuario_id, mensaje, enlace) VALUES (?, ?, ?)');
    $stmt->execute([$usuarioId, $mensaje, $enlace]);
}