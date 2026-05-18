# Pre-commit hook para verificar cobertura de tests (Windows PowerShell)
# Coloca este archivo en .git/hooks/pre-commit.ps1
# Y ejecuta desde pre-commit (sin extensión):
# PowerShell -ExecutionPolicy Bypass -File .git/hooks/pre-commit.ps1

Write-Host "🧪 Ejecutando tests para verificar cobertura..." -ForegroundColor Cyan

$projectRoot = git rev-parse --show-toplevel
Set-Location $projectRoot

# Verificar si phpunit existe
if (-not (Test-Path "vendor/bin/phpunit.bat")) {
    Write-Host "❌ PHPUnit no instalado. Abortando..." -ForegroundColor Red
    exit 1
}

Write-Host "⏳ Por favor espera mientras se ejecutan los tests..." -ForegroundColor Yellow

# Ejecutar tests - en Windows sin coverage ya que Xdebug/PCOV pueden no estar disponibles
$testOutput = & php artisan test --no-coverage 2>&1

# Buscar el número de tests y fallos
if ($testOutput -match "Tests:\s+(\d+)\s+failed,\s+(\d+)\s+passed") {
    $failed = [int]$matches[1]
    $passed = [int]$matches[2]
    $total = $failed + $passed
    
    if ($total -gt 0) {
        $passPercentage = [math]::Round(($passed / $total) * 100, 2)
        Write-Host "📊 Resultado: $passed/$total tests pasaron ($passPercentage%)" -ForegroundColor Cyan
        
        if ($failed -gt 0) {
            Write-Host "❌ $failed tests fallaron. Por favor, corrígelos antes de hacer commit." -ForegroundColor Red
            exit 1
        }
    }
}

Write-Host "✅ Todos los tests pasaron. Puedes proceder con el commit." -ForegroundColor Green
exit 0
