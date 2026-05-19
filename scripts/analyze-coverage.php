#!/usr/bin/env php
<?php

/**
 * Script para analizar y reportar cobertura de tests
 *
 * Uso: php scripts/analyze-coverage.php
 */
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

echo "═══════════════════════════════════════════════════════════════\n";
echo "  📊 Análisis de Cobertura de Tests - DataPet\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// 1. Verificar si PCOV o Xdebug están instalados
echo "1️⃣  Verificando driver de cobertura...\n";
$drivers = [];
if (extension_loaded('pcov')) {
    $drivers[] = '✅ PCOV';
} elseif (extension_loaded('xdebug')) {
    $drivers[] = '✅ Xdebug';
} else {
    echo "❌ No se encontró PCOV ni Xdebug\n";
    echo "   Instala uno de estos para obtener reportes de cobertura:\n";
    echo "   - PCOV: composer require --dev pcov/clopper\n";
    echo "   - Xdebug: pecl install xdebug\n\n";
    $drivers[] = '⚠️  Sin driver (tests sin cobertura)';
}

echo '   Driver disponible: '.implode(', ', $drivers)."\n\n";

// 2. Contar tests
echo "2️⃣  Analizando tests...\n";

$testFiles = glob('tests/**/*.php');
$testMethods = 0;

foreach ($testFiles as $file) {
    $content = file_get_contents($file);
    $matches = [];
    preg_match_all('/public\s+function\s+test_/', $content, $matches);
    $testMethods += count($matches[0]);
}

echo '   Archivos de test encontrados: '.count($testFiles)."\n";
echo '   Métodos de test encontrados: '.$testMethods."\n\n";

// 3. Archivos de código a cubrir
echo "3️⃣  Código que se debe cubrir:\n";

$codeFiles = [];
$totalLines = 0;

// Usar glob para encontrar archivos recursivamente
$patterns = [
    'app/Models/*.php',
    'app/Http/Controllers/*.php',
    'app/Http/Middleware/*.php',
    'app/Http/Requests/*.php',
    'app/Services/*.php',
];

foreach ($patterns as $pattern) {
    foreach (glob($pattern) as $file) {
        $lines = count(file($file)) - 1;
        $codeFiles[] = ['file' => $file, 'lines' => $lines];
        $totalLines += $lines;
    }
}

usort($codeFiles, function ($a, $b) {
    return $b['lines'] <=> $a['lines'];
});

echo '   Archivos a cubrir: '.count($codeFiles)."\n";
echo '   Total de líneas: '.$totalLines."\n\n";

if (count($codeFiles) > 0) {
    echo "   Top 10 archivos por tamaño:\n";
    foreach (array_slice($codeFiles, 0, 10) as $file) {
        $filename = str_replace('app/', '', $file['file']);
        echo "      • $filename (".$file['lines']." líneas)\n";
    }
}

// 4. Recomendaciones
echo "\n4️⃣  Recomendaciones:\n";

$ratio = count($codeFiles) > 0 ? ($testMethods / count($codeFiles)) : 0;

if ($ratio < 2) {
    echo '   ⚠️  Tests insuficientes (promedio: '.round($ratio, 2)." tests/archivo)\n";
    echo "      Se recomienda al menos 2 tests por archivo\n";
} else {
    echo '   ✅ Cantidad de tests adecuada ('.round($ratio, 2)." tests/archivo)\n";
}

echo "\n5️⃣  Próximos pasos:\n";
echo "   1. Instala PCOV: composer require --dev pcov/clopper\n";
echo "   2. Ejecuta: php artisan test --coverage\n";
echo "   3. Abre: coverage-report/index.html\n";
echo "   4. Mejora los archivos por debajo del 80%\n\n";

echo "═══════════════════════════════════════════════════════════════\n";
echo "  ✨ Comienza a escribir tests con alta cobertura\n";
echo "═══════════════════════════════════════════════════════════════\n";
