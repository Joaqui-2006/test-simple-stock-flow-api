# test-simple-stock-flow-api

> **Prueba técnica · Ficha ADSO 3413974**  
> Backend en PHP con Laravel 11 implementado bajo Arquitectura Onion.

---

### 1. Qué es esto
Es el servicio de backend y API REST de *Simple Stock Flow*. Se encarga de gestionar el catálogo de productos con control de stock estricto, registrar ventas atómicas inmutables con congelación de precio/nombre, procesar reportes agregados y autenticar usuarios mediante JWT. **No se ocupa** de pagos, envíos ni de la interfaz visual del usuario.

### 2. Cómo se levanta
El servicio está completamente dockerizado. Desde la carpeta hermana `test-simple-stock-flow-infra`:
```bash
# 1. Configurar variables de entorno
cp .env.example .env

# 2. Levantar el ecosistema completo
docker compose up -d api
```
La API estará accesible de inmediato en `http://localhost:8000`.

### 3. Dónde están los datos
* **Motor:** MySQL 8.4
* **Base de datos:** `stockflow`
* **Host:** `db` (o `127.0.0.1` desde el host)
* **Puerto:** `3307` desde el host (o `3306` internamente en Docker)
* **Usuario:** `stockflow_user`
* **Credenciales:** Definidas en el archivo `.env` (`DB_PASSWORD`).
Para conectarte con un cliente SQL desde el equipo host:
```bash
mysql -h 127.0.0.1 -P 3307 -u stockflow_user -p stockflow
```

### 4. Cómo se prueba
Para ejecutar las pruebas y la verificación arquitectónica de capas:
```bash
docker compose run --rm api bash verify.sh
```
O directamente con PHPUnit dentro del contenedor:
```bash
docker compose run --rm api vendor/bin/phpunit
```

### 5. Qué falta
El alcance de la especificación está 100% implementado. De acuerdo al spec, quedan explícitamente fuera de alcance por diseño: devoluciones, pasarelas de pago externas, impuestos dinámicos y registro de compradores.