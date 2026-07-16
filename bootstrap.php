<?php
// bootstrap.php

// =========================================================================
// 1. ARRANCAR EL MOTOR (Autoload de Composer)
// =========================================================================
// Mapea automáticamente tus clases en 'src/' bajo el namespace 'Src\'
// gracias a la configuración que hicimos en el composer.json
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// =========================================================================
// 2. CONFIGURAR EL ENTORNO (.env vs Producción)
// =========================================================================
// Si encuentra el archivo .env (entorno local), carga las variables en $_ENV.
// Si no existe (producción), se lo salta y confía en el sistema operativo.
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// =========================================================================
// 3. INICIALIZAR LA INFRAESTRUCTURA GLOBAL
// =========================================================================
try {
    // Usamos estrictamente la superglobal $_ENV (más segura y fácil de probar en tests)
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']};charset=utf8mb4",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    
    // Configuramos PDO para que lance excepciones si hay errores de SQL (crucial para tus tests)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Desactivamos emulación de preparación para usar consultas preparadas reales y seguras
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Si la base de datos falla, detenemos el sistema con un mensaje limpio
    http_response_code(500);
    echo json_encode([
        'error' => 'Error interno del servidor',
        'details' => $_ENV['APP_DEBUG'] === 'true' ? $e->getMessage() : 'Error de conexión'
    ]);
    exit;
}

// =========================================================================
// 4. INSTANCIAR MÓDULOS (INYECCIÓN DE DEPENDENCIAS - ADR-003)
// =========================================================================

// ---> MÓDULO: GESTIÓN DE MASCOTAS (Edison) <---
$repositorioMascota = new \App\Mascota\Infraestructura\RepositorioMascotaMySQL($pdo);
$registrarMascotaUseCase = new \App\Mascota\Aplicacion\RegistrarMascota($repositorioMascota);
$mascotaController = new \App\Mascota\Infraestructura\MascotaController($registrarMascotaUseCase);

// =========================================================================
// 5. RETORNAR DEPENDENCIAS Y CONTROLADORES
// =========================================================================
// Retornamos los servicios globales y los controladores listos para que el index.php (el enrutador) los use.
return [
    'database' => $pdo,
    'controllers' => [
        'mascota' => $mascotaController,
        // Aquí tus compañeros irán agregando los suyos (Ej: 'cita' => $citaController)
    ]
];