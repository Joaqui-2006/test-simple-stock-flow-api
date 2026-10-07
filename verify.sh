#!/bin/bash
set -e

echo "=== 1. Verificando Regla R-01 (Domain sin Illuminate) ==="
if grep -R "Illuminate\\\\" app/Domain; then
    echo "ERROR: ViolaciÃ³n de R-01 en app/Domain"
    exit 1
fi
echo "OK: Domain limpio de Illuminate"

echo "=== 2. Verificando Regla R-03 (Presentation sin Infrastructure) ==="
if grep -R "App\\\\Infrastructure" app/Presentation; then
    echo "ERROR: ViolaciÃ³n de R-03 en app/Presentation"
    exit 1
fi
echo "OK: Presentation sin acoplamiento a Infrastructure"

echo "=== 3. Verificando pruebas unitarias de Dominio ==="
if [ -f vendor/bin/phpunit ]; then
    vendor/bin/phpunit
else
    echo "PHPUnit no instalado en host (se corre en Docker)"
fi

echo "=== VerificaciÃ³n completada con Ã©xito ==="