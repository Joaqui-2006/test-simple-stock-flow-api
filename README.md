# test-simple-stock-flow-api

> **Prueba tÃ©cnica Â· Ficha ADSO 3413974**  
> Backend en PHP con Laravel 11 implementado bajo Arquitectura Onion.

---

### 1. QuÃ© es esto
Es el servicio de backend y API REST de *Simple Stock Flow*. Se encarga de gestionar el catÃ¡logo de productos con control de stock estricto, registrar ventas atÃ³micas inmutables con congelaciÃ³n de precio/nombre, procesar reportes agregados y autenticar usuarios mediante JWT. **No se ocupa** de pagos, envÃ­os ni de la interfaz visual del usuario.

### 2. CÃ³mo se levanta
El servicio estÃ¡ completamente dockerizado. Desde la carpeta hermana `test-simple-stock-flow-infra`:
```bash
# 1. Configurar variables de entorno
cp .env.example .env

# 2. Levantar el ecosistema completo
docker compose up -d api
```
La API estarÃ¡ accesible de inmediato en `http://localhost:8000`.

### 3. DÃ³nde estÃ¡n los datos
* **Motor:** MySQL 8.4
* **Base de datos:** `stockflow`
* **Host:** `db` (o `127.0.0.1` desde el host)
* **Puerto:** `3306`
* **Usuario:** `stockflow_user`
* **Credenciales:** Definidas en el archivo `.env` (`DB_PASSWORD`).
Para conectarte con un cliente SQL:
```bash
mysql -h 127.0.0.1 -P 3306 -u stockflow_user -p stockflow
```

### 4. CÃ³mo se prueba
Para ejecutar las pruebas y la verificaciÃ³n arquitectÃ³nica de capas:
```bash
docker compose run --rm api bash verify.sh
```
O directamente con PHPUnit dentro del contenedor:
```bash
docker compose run --rm api vendor/bin/phpunit
```

### 5. QuÃ© falta
El alcance de la especificaciÃ³n estÃ¡ 100% implementado. De acuerdo al spec, quedan explÃ­citamente fuera de alcance por diseÃ±o: devoluciones, pasarelas de pago externas, impuestos dinÃ¡micos y registro de compradores.