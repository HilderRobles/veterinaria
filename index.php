<?php
// C:\wamp64\www\Nueva carpeta\veterinaria\index.php

require_once __DIR__ . '/bootstrap.php';
$dependencies = require __DIR__ . '/bootstrap.php';
$pdo = $dependencies['database'];

$method = $_SERVER['REQUEST_METHOD'];

// 1. CAPTURA Y LIMPIEZA INTELIGENTE DE URL
// Esto extrae solo la parte que viene después de /api/ de forma segura
$uriCompleta = $_SERVER['REQUEST_URI'];

// Removemos los parámetros query de la URL si existen (ej: /api/clientes?id=5 -> /api/clientes)
$uriSinQuery = explode('?', $uriCompleta)[0];

// Buscamos dónde empieza '/api' para ignorar las subcarpetas de Wampserver
$posicionApi = strpos($uriSinQuery, '/api');

if ($posicionApi !== false) {
    // Esto recortará todo lo anterior y te dejará exactamente "/api/clientes..."
    $path = substr($uriSinQuery, $posicionApi);
} else {
    $path = $uriSinQuery;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

// 2. ENRUTADOR DE CLIENTES
if (strpos($path, '/api/clientes') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    
    // Despachamos usando la URL ya limpia y homogeneizada
    \App\Cliente\Infraestructura\ClienteRuta::despachar($method, $path, $input, $pdo);
    exit;
}

// 3. ENRUTADOR DE CITAS
if (strpos($path, '/api/citas') === 0) {
    header('Content-Type: application/json; charset=utf-8');
    \App\Cita\Infraestructura\CitaRuta::despachar($method, $path, $input, $pdo);
    exit;
}



// Si llega aquí, es porque no pertenece al módulo de clientes
header('Content-Type: application/json; charset=utf-8');
http_response_code(404);
echo json_encode(['error' => 'Módulo global no encontrado.', 'url_recibida' => $path]);
exit;