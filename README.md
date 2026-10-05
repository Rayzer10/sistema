# sistema

Sistema web en PHP.

## Requisitos

- PHP 8+
- MySQL / MariaDB
- [Composer](https://getcomposer.org/)

## Instalación

```bash
composer install
```

Configurar la base de datos en `config/database.php` e importar `sistema.sql`.

## Estructura

- `controller/` — controladores
- `model/` — modelos
- `view/` — vistas
- `core/` — nucleo y helpers
- `plantillas/` — plantillas de reportes
- `resource/` — recursos estaticos
- `uploads/` — archivos cargados por el usuario (no versionado)
- `config/` — configuracion