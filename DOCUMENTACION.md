# Sistema de Gestion - Documentacion Tecnica

## Estructura del Proyecto

```
sistema/
├── config/
│   └── database.php          # Configuracion de conexion a BD
├── controller/
│   ├── LoginController.php   # Autenticacion de usuarios
│   └── UsuarioController.php # CRUD de usuarios
├── core/
│   ├── App.php               # Punto de entrada de la aplicacion
│   ├── Router.php            # Enrutador de URLs amigables
│   ├── Controller.php        # Controlador base (model, view, render, redirect)
│   └── Model.php             # Modelo base (all, find, where, create, update, delete)
├── model/
│   ├── Database.php          # Conexion PDO singleton
│   └── Usuario.php           # Modelo de usuario
├── resource/
│   ├── css/
│   │   └── estilos.css       # Estilos globales (tema oscuro)
│   └── js/
│       └── validaciones.js   # Modulo de validacion reutilizable
├── view/
│   ├── home/
│   │   └── index.php         # Pagina principal
│   ├── layout/
│   │   └── main.php          # Layout con sidebar + navbar
│   ├── login/
│   │   └── login.php         # Formulario de login
│   └── usuario/
│       ├── index.php         # Listado de usuarios
│       └── form.php          # Formulario crear/editar usuario
├── .htaccess                 # Rewrite rules (URLs amigables)
└── index.php                 # Entry point (define BASE_URL, carga App)
```

## Como funciona el Patron MVC

### Flujo de una peticion

```
Usuario accede a: /usuario/form
        │
        ▼
┌─────────────┐
│  .htaccess   │  Redirige todo a index.php?url=usuario/form
└──────┬──────┘
       ▼
┌─────────────┐
│  index.php   │  define BASE_URL, carga App.php
└──────┬──────┘
       ▼
┌─────────────┐
│  App.php     │  Inicia sesion, obtiene url de $_GET, crea Router
└──────┬──────┘
       ▼
┌─────────────┐
│  Router.php  │  Divide la URL, busca controlador y metodo
└──────┬──────┘
       ▼
┌─────────────────────┐
│ UsuarioController    │  Ejecuta form($encodedId)
│   └── $this->model() │  Carga el modelo Usuario
│   └── $this->render()│  Inyecta vista en layout
└──────┬──────────────┘
       ▼
┌─────────────┐
│  view/       │  Vista PHP renderizada en layout/main.php
│  layout/     │  HTML final enviado al navegador
│  main.php    │
└─────────────┘
```

### Model - Vista - Controlador

| Capa | Ubicacion | Responsabilidad |
|------|-----------|-----------------|
| **Model** | `model/` | Acceso a datos, consultas SQL, logica de negocio |
| **View** | `view/` | HTML con PHP embebido, formularios, tablas |
| **Controller** | `controller/` | Recibe peticiones, valida datos, coordina modelo y vista |

### Metodos del Controlador Base

```php
class Controller {
    // Carga un modelo: $this->model('Usuario') retorna instancia de Usuario
    protected function model($model)

    // Renderiza vista SIN layout (para AJAX, login, etc.)
    protected function view($view, $data = [])

    // Renderiza vista DENTRO del layout principal (sidebar + navbar)
    protected function render($view, $data = [])

    // Redirige a otra URL: $this->redirect('usuario')
    protected function redirect($url)

    // Codifica/decodifica IDs para URLs seguras
    protected function encodeId($id)
    protected function decodeId($encoded)
}
```

### Metodos del Modelo Base

```php
class Model {
    // Todos los registros: SELECT * FROM usuario
    public function all()

    // Buscar por ID: SELECT * FROM usuario WHERE id = ?
    public function find($id)

    // Buscar por columna: SELECT * FROM usuario WHERE estado = ?
    public function where($column, $value)

    // Buscar uno: SELECT * FROM usuario WHERE correo = ? LIMIT 1
    public function whereFirst($column, $value)

    // Crear registro
    public function create($data)  // ['nombre' => 'Juan', ...]

    // Actualizar registro
    public function update($id, $data)

    // Eliminar registro
    public function delete($id)
}
```

## URLs Amigables

### Como funciona el enrutamiento

El sistema convierte URLs limpias en llamadas a controladores y metodos:

```
URL del navegador         Controlador              Metodo
─────────────────────────────────────────────────────────────
/login                    LoginController          index()
/login/loginAjax          LoginController          loginAjax()
/usuario                  UsuarioController        index()
/usuario/form             UsuarioController        form()
/usuario/form/eyJpZCI6MX0= UsuarioController     form('eyJpZCI6MX0=')
/usuario/perfil           UsuarioController        perfil()
/home                     HomeController           index()
/ (vacia)                 LoginController          index()  [default]
```

### Regla de enrutamiento

```
URL: /{controlador}/{metodo}/{parametro1}/{parametro2}/...
         │             │           │
         │             │           └─ $this->params[0]
         │             └─ $this->method
         └─ $this->controller (se le agrega "Controller" al final)
```

### Ejemplo practico

```
URL: /usuario/form/eyJpZCI6MX0=

Router separa: ['usuario', 'form', 'eyJpZCI6MX0=']

1. $url[0] = 'usuario'
   → Busca controller/UsuarioController.php
   → Existe → $this->controller = 'UsuarioController'

2. $url[1] = 'form'
   → Verifica si UsuarioController tiene metodo form()
   → Existe → $this->method = 'form'

3. $url[2] = 'eyJpZCI6MX0='
   → Se convierte en parametro: $this->params = ['eyJpZCI6MX0=']

Resultado: call_user_func_array([UsuarioController, 'form'], ['eyJpZCI6MX0='])
```

### Como se generan las URLs en PHP

```php
// En las vistas:
<a href="<?= BASE_URL ?>usuario">Usuarios</a>
<a href="<?= BASE_URL ?>usuario/form">Nuevo Usuario</a>
<a href="<?= BASE_URL ?>usuario/form/<?= $encodedId ?>">Editar</a>

// En los controladores:
$this->redirect('usuario');           // Redirige a /usuario
$this->redirect('usuario/form/' . $encodedId);

// BASE_URL esta definido en index.php
define('BASE_URL', '/');  // Para virtual host apuntando a la carpeta del proyecto
```

### Proteccion de IDs

Los IDs nunca se exponen en la URL directamente. Se codifican en Base64 URL-safe:

```php
// Codificar (en el controlador):
$encodedId = $this->encodeId(42);
// Resultado: "NDI" (no es obvio que es el ID 42)

// Decodificar (al recibir el parametro):
$id = $this->decodeId('NDI');
// Resultado: 42

// En la vista:
<a href="<?= BASE_URL ?>usuario/form/<?= $this->encodeId($usuario['id']) ?>">Editar</a>
```

## Base de Datos

### Configuracion (`config/database.php`)

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### Conexion PDO Singleton (`model/Database.php`)

- Una sola instancia de conexion PDO (patron Singleton)
- Preparacion de sentencias (proteccion contra SQL injection)
- Modo FETCH_ASSOC por defecto
- Errores lanzados como excepciones

```php
$usuarioModel = $this->model('Usuario');
$usuarios = $usuarioModel->all();

// Internamente ejecuta:
// SELECT * FROM usuario
```

## Sistema de Validacion Frontend (`resource/js/validaciones.js`)

Modulo JavaScript reutilizable que funciona con atributos HTML:

| Atributo | Funcion | Ejemplo |
|----------|---------|---------|
| `required` | Campo obligatorio | `<input required>` |
| `type="email"` | Valida formato email | `<input type="email">` |
| `type="tel"` | Valida telefono VE (04XX-XXXXXXX) | `<input type="tel">` |
| `data-minlength="N"` | Minimo N caracteres | `<input data-minlength="8">` |
| `data-maxlength="N"` | Maximo N caracteres | `<input maxlength="100">` |
| `data-alphanumeric` | Solo letras/numeros, min 8 chars | `<input data-alphanumeric>` |
| `data-match="id"` | Debe coincidir con otro campo | `<input data-match="clave">` |
| `data-pattern="rx"` | Regex personalizada | `<input data-pattern="^\d+$">` |
| `data-pattern-msg` | Mensaje para pattern | `<input data-pattern-msg="Solo numeros">` |
| `data-validator="fn"` | Funcion custom | `<input data-validator="miFuncion">` |
| `data-custom-msg` | Mensaje para validator custom | `<input data-custom-msg="Error">` |

### Comportamiento

```
Blur (perder foco)  → Valida si el campo esta vacio o tiene formato invalido
Input (escribir)    → Re-valida en tiempo real si ya tiene error
Submit (enviar)     → Valida TODOS los campos, muestra errores, enfoca el primero
Campo sin tocar     → No muestra errores (estilo Laravel)
```

### Uso

```html
<script src="resource/js/validaciones.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        FormValidation.init('#miFormulario');
    });
</script>
```

### Validadores personalizados

```javascript
window.FormValidators = {
    miValidador: function(value) {
        return value.length >= 5;  // true = valido, false = invalido
    }
};
```

```html
<input data-validator="miValidador" data-custom-msg="Minimo 5 caracteres">
```

## Tema Visual (Linear Style)

El proyecto usa un tema oscuro inspirado en Linear.app:

| Elemento | Color |
|----------|-------|
| Fondo principal | `#0A0A0B` |
| Tarjetas/sidebar | `#1B1B1F` |
| Bordes | `rgba(255,255,255,0.06)` |
| Texto principal | `#FFFFFF` |
| Texto secundario | `#8A8F98` |
| Color primario | `#5E6AD2` |
| Error | `#DC3545` |
| Exito | `#28A745` |

### Dependencias externas

- **Bootstrap 5.3** (JS, no CSS) - Componentes (dropdowns, modals, etc.)
- **DataTables + Buttons** - Tablas con paginacion, busqueda, exportacion
- **SweetAlert2** - Alertas y confirmaciones con tema oscuro
- **Google Fonts (Inter)** - Tipografia

## Agregar un Nuevo Modulo

### 1. Crear el modelo

```php
// model/Producto.php
class Producto extends Model
{
    protected $table = 'producto';
}
```

### 2. Crear el controlador

```php
// controller/ProductoController.php
class ProductoController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('');
        }
    }

    public function index()
    {
        $model = $this->model('Producto');
        $productos = $model->all();
        $this->render('producto/index', [
            'titulo' => 'Productos',
            'productos' => $productos
        ]);
    }
}
```

### 3. Crear la vista

```php
<!-- view/producto/index.php -->
<!-- Este archivo se inyecta en layout/main.php via $contenido -->
```

### 4. Acceder

```
/producto           → ProductoController::index()
/producto/form      → ProductoController::form()
/producto/editar/ID → ProductoController::editar('ID')
```

No es necesario registrar rutas. El Router detecta automaticamente el controlador y metodo por la URL.

---

# Convenciones del Proyecto

## Base de datos: un solo archivo SQL

Todos los cambios de esquema (creacion, modificacion o eliminacion de tablas,
columnas, indices, constraints o seeds) se registran **exclusivamente** en
`sistema.sql`. No se crean archivos `migracion_*.sql` separados.

Cada cambio en el archivo debe:

1. Agregar una entrada al historial de versiones en la cabecera
2. Estar claramente comentado con el bloque `vX.Y - <descripcion>`
3. Usar `CREATE TABLE` (no `IF NOT EXISTS`) porque el script se ejecuta sobre
   una base limpia o se hace backup antes de re-ejecutar
4. Si es un cambio destructivo (DROP, ALTER destructivo), documentarlo en el
   comentario

Para desarrollo, si necesitas aplicar un cambio en una BD existente, podes
agregar el `ALTER` o `DROP` al final del archivo en una nueva seccion versionada.

## Dependencias PHP (Composer)

El sistema usa **DomPDF v3.x** para generar los reportes PDF (desempeño del
estudiante, reporte de sección del docente) y las plantillas descargables
(ERS, Matriz de Stakeholders, Backlog, Matriz de Riesgos).

Las dependencias se gestionan con Composer y se instalan con:

```bash
composer install
```

(equivalente a `composer require dompdf/dompdf` si partes de cero).

El `vendor/` queda en la raiz del proyecto. El `index.php` carga
automaticamente `vendor/autoload.php` si existe. Si DomPDF no esta instalado
y se intenta generar un PDF, se lanzara un error de clase no encontrada.

Composers usados:
- `dompdf/dompdf` ^3.1
- `masterminds/html5` (dependencia)
- `phenx/php-svg-lib` (dependencia)
- `phenx/php-font-lib` (dependencia)

---

# Plan de Trabajo - Sistema Institucional UPTP-LMR (PNFI)

## Vision General

Sistema web institucional para el Departamento de Informatica - PNFI.
**Roles:** 1 = Administrador, 2 = Docente, 3 = Estudiante.

## Roadmap por Fases

### FASE 0 - Refactor Base (transversal)
- [ ] Migrar login de MD5 a `password_hash` / `password_verify`
- [ ] Crear helper de autorizacion por rol (middleware ligero en Controller base)
- [ ] Sidebar dinamico segun rol del usuario logueado
- [ ] Estandarizar uso de IDs codificados (Base64) en todas las URLs
- [ ] Auditoria basica en tabla `historial`

### FASE 1 - Nucleo Academico (prioridad)
Objetivo: sentar la base de datos academica y permitir cargar contenido.

**Modelo de datos a crear:**
- `periodo_academico` (id, nombre, fecha_inicio, fecha_fin, estado)
- `seccion` (id, nombre, id_periodo)
- `seccion_materia` (id, id_seccion, id_materia, id_docente) - cada materia de la seccion tiene su propio docente
- `estudiante_seccion` (id_estudiante, id_seccion)
- `nivel` (id, numero, nombre, descripcion) - 4 fijos
- `unidad` (id, titulo, descripcion, id_materia, id_nivel, orden, estado) - el admin crea unidades por materia
- `unidad_archivo` (id, id_unidad, tipo [mp4/pptx/pdf/html], ruta, nombre_original)
- `progreso_unidad` (id_estudiante, id_unidad, completada, fecha)
- `actividad` (id, id_materia, id_unidad, titulo, descripcion, id_nivel, estado) - el docente crea actividades dentro de una unidad (requiere unidades creadas por el admin)

**Modulos a entregar:**
1. **Periodos Academicos** (admin) - CRUD
2. **Secciones** (admin) - CRUD + asignar docente + asignar estudiantes masivamente
3. **Niveles** (4 fijos) - consulta publica, logica de desbloqueo por completitud >= 80%
4. **Gestor de Contenido** (admin/docente) - CRUD unidades + upload de archivos
5. **Centro de Aprendizaje** (estudiante) - ver unidades por nivel, marcar como completada

### FASE 2 - Modulos del Estudiante
- **Laboratorio de Dinamicas** - 24 dinamicas precargadas, preguntas, opciones, feedback automatico
- **Simulador de Escenarios** - 8 escenarios precargados, entregas, evaluacion del docente
- **Panel del Estudiante** - mapa de niveles, historial, progreso, badges

### FASE 3 - Panel del Docente
- Listado de estudiantes por seccion
- Asignar escenarios a estudiantes
- Evaluar entregas de escenarios (calificacion + retroalimentacion)
- Ver progreso de su seccion

### FASE 4 - Reportes y Plantillas
- Generacion de PDF con DomPDF (reporte de desempeno del estudiante)
- Plantillas descargables: ERS simplificada, matriz de stakeholders, tabla de backlog, matriz de riesgos

### FASE 5 - Seeds
- Script SQL con 8 escenarios hipoteticos
- Script SQL con 24 dinamicas interactivas (6 por nivel)

## Convenciones del Proyecto (extienden lo existente)

### Roles (campo `rol` en tabla `usuario`)

| Codigo | Rol | Acceso |
|--------|-----|--------|
| 1 | Administrador | Todo el sistema, gestion de usuarios, periodos, secciones, contenido |
| 2 | Docente | Gestion de contenido (opcional), seguimiento de su seccion, evaluacion de escenarios |
| 3 | Estudiante | Centro de aprendizaje, laboratorio, simulador, panel propio |

### Tipos de Contenido (campo `tipo` en `unidad_archivo`)

| Codigo | Tipo | Mime/Extension |
|--------|------|----------------|
| mp4 | Video local | video/mp4 |
| pptx | Presentacion | application/vnd.openxmlformats-officedocument.presentationml.presentation |
| pdf | Documento PDF | application/pdf |
| html | Texto HTML dinamico | text/html (campo `contenido_html` en `unidad`) |

### Estructura de carpetas a crear

```
uploads/
  unidades/
    mp4/
    pptx/
    pdf/
    html/
plantillas/             # PDFs descargables estaticos
reportes/               # PDFs generados por DomPDF
resource/
  vendor/               # DomPDF (descarga manual o Composer)
```

## Orden de Ejecucion Sugerido

1. FASE 0 (refactor) -> 1 sesion
2. FASE 1.1 (esquema BD) -> 1 sesion
3. FASE 1.2 a 1.6 -> 3-4 sesiones
4. FASE 2 -> 3-4 sesiones
5. FASE 3 -> 2 sesiones
6. FASE 4 -> 1-2 sesiones
7. FASE 5 -> 1 sesion (carga de seeds)
