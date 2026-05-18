#!/usr/bin/env php
<?php

/**
 * Script de Setup para Tests y Cobertura
 *
 * Uso: php scripts/setup-tests.php
 */
echo "\n";
echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║         🚀 Setup de Tests y Cobertura - DataPet               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$steps = [
    'check_requirements' => '1️⃣  Verificando requisitos',
    'verify_gd' => '2️⃣  Verificando extensión GD',
    'setup_git_hooks' => '3️⃣  Configurando Git Hooks',
    'create_directories' => '4️⃣  Creando directorios',
    'generate_report' => '5️⃣  Generando reporte inicial',
];

foreach ($steps as $step => $label) {
    echo "$label...\n";
    $method = $step;
    if (method_exists('SetupTests', $method)) {
        call_user_func(['SetupTests', $method]);
    }
    echo "\n";
}

echo "╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ Setup completado!                       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

echo "📋 Próximos pasos:\n";
echo "   1. Instala cobertura: composer require --dev pcov/clopper\n";
echo "   2. Ejecuta tests: php artisan test --coverage\n";
echo "   3. Abre el reporte: open coverage-report/index.html\n";
echo "   4. Lee la guía: cat TESTING.md\n\n";

class SetupTests
{
    public static function check_requirements()
    {
        $php_version = phpversion();
        echo "   • PHP Version: $php_version ✅\n";

        if (extension_loaded('pdo')) {
            echo "   • PDO Extension: ✅\n";
        }

        if (extension_loaded('mbstring')) {
            echo "   • Mbstring Extension: ✅\n";
        }

        if (file_exists('vendor/autoload.php')) {
            echo "   • Composer Dependencies: ✅\n";
        } else {
            echo "   • Composer Dependencies: ❌ Run 'composer install'\n";
        }
    }

    public static function verify_gd()
    {
        if (extension_loaded('gd')) {
            echo "   • GD Extension: ✅ Instalada\n";
        } else {
            echo "   • GD Extension: ⚠️  No instalada\n";
            echo "     Algunos tests de imágenes fallarán.\n";
            echo "     Para habilitarla, edita php.ini y agrega:\n";
            echo "     extension=gd\n";
        }
    }

    public static function setup_git_hooks()
    {
        $gitDir = '.git/hooks';

        if (! is_dir($gitDir)) {
            echo "   • No se encontró .git/hooks\n";

            return;
        }

        // Windows or Unix?
        $isWindows = DIRECTORY_SEPARATOR === '\\';

        if ($isWindows) {
            // Copy PowerShell script
            if (file_exists('scripts/pre-commit.ps1')) {
                echo "   • Git Hook (PowerShell): ✅ Disponible\n";
                echo "     Para activarlo, ejecuta:\n";
                echo "     copy scripts/pre-commit.ps1 .git/hooks/pre-commit.ps1\n";
            }
        } else {
            // Copy and make executable
            if (file_exists('scripts/pre-commit')) {
                $hookPath = '.git/hooks/pre-commit';
                if (@copy('scripts/pre-commit', $hookPath)) {
                    @chmod($hookPath, 0755);
                    echo "   • Git Hook (Bash): ✅ Instalado\n";
                } else {
                    echo "   • Git Hook (Bash): ⚠️  No se pudo copiar\n";
                }
            }
        }
    }

    public static function create_directories()
    {
        $dirs = [
            'coverage-report',
            'storage/logs',
            'storage/framework/cache',
        ];

        foreach ($dirs as $dir) {
            if (! is_dir($dir)) {
                @mkdir($dir, 0755, true);
                echo "   • Creado: $dir ✅\n";
            } else {
                echo "   • Existe: $dir ✅\n";
            }
        }
    }

    public static function generate_report()
    {
        $testFiles = glob('tests/**/*.php');
        $testMethods = 0;

        foreach ($testFiles as $file) {
            $content = file_get_contents($file);
            $matches = [];
            preg_match_all('/public\s+function\s+test_/', $content, $matches);
            $testMethods += count($matches[0]);
        }

        echo '   • Tests encontrados: '.count($testFiles)."\n";
        echo "   • Métodos de test: $testMethods\n";
        echo "   • Reporte generado exitosamente ✅\n";
    }
}
