<?php
// Configurar zona horaria
date_default_timezone_set('America/Caracas');

// Configurar reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Definir URL base del proyecto
define('BASE_URL', '/');

// Cargar dependencias de Composer (DomPDF, etc)
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Cargar el core de la aplicación
require_once __DIR__ . '/core/App.php';

// Iniciar la aplicación
$app = new App();