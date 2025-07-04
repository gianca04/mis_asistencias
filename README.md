<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Comandos para instalación y configuración inicial

### Creación de proyecto
```bash
composer create-project --prefer-dist laravel/laravel mis_asistencias "11.*"
```

### Instalación de FilamentPHP
```bash
composer require filament/filament
```

### Creación de panel administrativo
```bash
php artisan filament:install --panels
```
> dashboard

### Creación de usuario administrador
```bash
php artisan make:filament-user
```

### Instalación de librería para seguridad Shield
```bash
composer require bezhansalleh/filament-shield
php artisan vendor:publish --tag="filament-shield-config"
php artisan shield:setup
php artisan shield:super-admin
php artisan shield:generate --all
php artisan shield:publish dashboard
```

### Creación de interfaz de Usuario y Resources
```bash
php artisan make:filament-resource User --generate
```

### Generar carpeta para almacenar archivos
```bash
php artisan storage:link
```

### Crear Resource de Reglas
```bash
php artisan make:filament-resource Regla --generate
```

### Crear Resource de Matrícula
```bash
php artisan make:filament-resource Matricula --generate
```

### Crear RelationManager para el formulario de matrículas
```bash
php artisan make:filament-resource Matricula --generate
```

### Instalación de librería para filtrar por rango de fechas
```bash
composer require malzariey/filament-daterangepicker-filter
php artisan vendor:publish --tag="filament-daterangepicker-filter-translations"
php artisan vendor:publish --tag="filament-daterangepicker-filter-views"
```
Documentación: [Malzariey Daterangepicker Filter](https://filamentphp.com/plugins/malzariey-daterangepicker-filter#time-picker)

### Instalación de librería pxlrbt/filament-excel
```bash
composer require pxlrbt/filament-excel
```
Habilitar extensión GD:
```bash
extension=gd
```
Documentación: [Filament Excel](https://filamentphp.com/plugins/pxlrbt-excel)

### Mejorar rendimiento
```bash
php artisan vendor:publish --force --tag=livewire:assets
```

### Comandos para ejecutar el proyecto
```bash
php artisan serve --host 0.0.0.0
npm run dev
```



