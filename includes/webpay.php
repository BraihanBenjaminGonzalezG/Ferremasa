<?php
// includes/webpay.php
require __DIR__ . '/../vendor/autoload.php';

use Transbank\Webpay\Configuration;

// Configuración para entorno de pruebas (sandbox)
Configuration::forTestingWebpayPlus();

// En producción usarías:
// Configuration::forProduction($commerceCode, $apiKey, $apiSecret);

