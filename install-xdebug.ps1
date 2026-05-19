# Script para descargar e instalar Xdebug en XAMPP Windows

Write-Host "═══════════════════════════════════════════" -ForegroundColor Green
Write-Host "  Instalando Xdebug para PHP 8.2.12" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════" -ForegroundColor Green

$phpExtDir = "C:\xampp\php\ext"
$xdebugDll = "$phpExtDir\php_xdebug.dll"
$phpIni = "C:\xampp\php\php.ini"

# 1. Verificar directorio
if (-not (Test-Path $phpExtDir)) {
    Write-Host "❌ Error: No se encontró $phpExtDir" -ForegroundColor Red
    exit 1
}

Write-Host "`n✅ Directorio de extensiones encontrado: $phpExtDir"

# 2. Descargar Xdebug
Write-Host "`n⏳ Descargando Xdebug 3.3.3 para PHP 8.2 TS x64..."
$url = "https://xdebug.org/files/php_xdebug-3.3.3-8.2-vc19-x86_64.dll"
$outFile = $xdebugDll

try {
    Invoke-WebRequest -Uri $url -OutFile $outFile -UseBasicParsing
    Write-Host "✅ Xdebug descargado: $outFile"
} catch {
    Write-Host "❌ Error descargando Xdebug: $_" -ForegroundColor Red
    Write-Host "`n📝 Descárgalo manualmente de: https://xdebug.org/download" -ForegroundColor Yellow
    Write-Host "   Versión requerida: PHP 8.2 TS x86_64" -ForegroundColor Yellow
    Write-Host "   Guardar en: $xdebugDll" -ForegroundColor Yellow
    exit 1
}

# 3. Configurar php.ini
Write-Host "`n⏳ Configurando php.ini..."

$xdebugConfig = @"
[XDebug]
zend_extension=php_xdebug.dll
xdebug.mode=coverage
xdebug.start_with_request=yes
xdebug.log_level=0
"@

# Verificar si ya existe la configuración
if (Select-String -Path $phpIni -Pattern "\[XDebug\]" -Quiet) {
    Write-Host "⚠️  Sección [XDebug] ya existe en php.ini"
    Write-Host "   Removiendo configuración anterior..."
    
    $content = Get-Content $phpIni
    $content = $content -replace '\[XDebug\].*?(?=\n\[|\Z)', '' -join "`n"
    $content | Set-Content $phpIni -Force
}

# Agregar nueva configuración
Add-Content -Path $phpIni -Value "`n$xdebugConfig"
Write-Host "✅ Configuración agregada a php.ini"

# 4. Verificar
Write-Host "`n✅ Instalación completada!"
Write-Host "`n📋 Próximos pasos:"
Write-Host "  1. Reinicia Apache: apachectl restart"
Write-Host "  2. Verifica Xdebug: php -m | Select-String xdebug"
Write-Host "  3. Ejecuta tests: php artisan test --coverage"
Write-Host "  4. Abre reporte: coverage-report/index.html"

Write-Host "`n═══════════════════════════════════════════" -ForegroundColor Green
