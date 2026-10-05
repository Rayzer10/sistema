<?php
/**
 * generar_manual.php
 *
 * Genera el manual de uso del sistema en formato .docx usando PHPWord.
 * Salida: manual_sistema.docx
 *
 * Ejecutar: php generar_manual.php
 *   o desde el navegador: /generar_manual.php (solo si requiereAuth desactivado)
 */

require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\JcTable;

$phpWord = new PhpWord();

// Configuracion de estilos
$phpWord->setDefaultFontName('Calibri');
$phpWord->setDefaultFontSize(11);

// Colores institucionales
$colorPrimario  = '5E6AD2';
$colorSecundario = '2A2B32';
$colorTexto     = '1a1a1a';
$colorClaro     = '8A8F98';

// =============== ESTILOS ===============

$phpWord->addTitleStyle(1, ['bold' => true, 'size' => 22, 'color' => $colorPrimario], ['spaceAfter' => 200]);
$phpWord->addTitleStyle(2, ['bold' => true, 'size' => 16, 'color' => $colorSecundario], ['spaceBefore' => 240, 'spaceAfter' => 120]);
$phpWord->addTitleStyle(3, ['bold' => true, 'size' => 13, 'color' => '4A4B52'], ['spaceBefore' => 160, 'spaceAfter' => 80]);

$estiloNormal = ['size' => 11, 'color' => $colorTexto];
$estiloCodigo = ['name' => 'Consolas', 'size' => 9, 'color' => 'C7254E'];
$estiloNota   = ['size' => 10, 'color' => '4A4B52', 'italic' => true];

$phpWord->addParagraphStyle('Normal', $estiloNormal + ['spaceAfter' => 120, 'lineHeight' => 1.3]);
$phpWord->addParagraphStyle('Codigo', $estiloCodigo + ['spaceAfter' => 60]);
$phpWord->addParagraphStyle('Nota', $estiloNota + ['spaceAfter' => 100]);
$phpWord->addParagraphStyle('Centrado', $estiloNormal + ['alignment' => Jc::CENTER]);
$phpWord->addParagraphStyle('Lista', $estiloNormal + ['spaceAfter' => 60]);

$phpWord->addFontStyle('Negrita', ['bold' => true]);
$phpWord->addFontStyle('CodigoTexto', $estiloCodigo);
$phpWord->addFontStyle('Resaltado', ['bold' => true, 'color' => $colorPrimario]);

// =============== PORTADA ===============

$portada = $phpWord->addSection();
$portada->addParagraph('', ['spaceAfter' => 1500]);

$portada->addText(
    'UPTP-LMR',
    ['bold' => true, 'size' => 28, 'color' => $colorPrimario],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 100]
);
$portada->addText(
    'Departamento de Informática',
    ['size' => 16, 'color' => $colorSecundario],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 80]
);
$portada->addText(
    'Programa Nacional de Formación (PNFI)',
    ['size' => 13, 'color' => $colorClaro, 'italic' => true],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 800]
);

$portada->addText(
    'Manual de Uso del Sistema',
    ['bold' => true, 'size' => 24, 'color' => $colorSecundario],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
);
$portada->addText(
    'Sistema Institucional de Gestión del Aprendizaje',
    ['size' => 14, 'color' => $colorClaro],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 1000]
);

$portada->addText(
    'Versión 1.0',
    ['size' => 11, 'color' => $colorClaro],
    ['alignment' => Jc::CENTER, 'spaceAfter' => 50]
);
$portada->addText(
    date('d/m/Y'),
    ['size' => 11, 'color' => $colorClaro],
    ['alignment' => Jc::CENTER]
);

$portada->addPageBreak();

// =============== CONTENIDO ===============

$seccion = $phpWord->addSection();

function H1($sec, $txt) { $sec->addTitle($txt, 1); }
function H2($sec, $txt) { $sec->addTitle($txt, 2); }
function H3($sec, $txt) { $sec->addTitle($txt, 3); }
function P($sec, $txt, $style = 'Normal') { $sec->addText($txt, $style); }
function LI($sec, $txt) {
    $p = $sec->addText('•  ' . $txt, 'Lista');
}
function COD($sec, $txt) { $sec->addText($txt, 'Codigo'); }
function NOTA($sec, $txt) { $sec->addText('Nota: ' . $txt, 'Nota'); }

// ----------------------------------------------------------------------------
// 1. INTRODUCCION
// ----------------------------------------------------------------------------

H1($seccion, '1. Introducción');

H2($seccion, '1.1 Propósito del sistema');
P($seccion, 'El Sistema Institucional de Gestión del Aprendizaje del Departamento de Informática del PNFI-UPTP-LMR es una plataforma web que integra las herramientas necesarias para acompañar el proceso formativo de los estudiantes en la materia de Ingeniería de Requisitos. Reemplaza el uso disperso de cuadernos, archivos y correos por un único entorno con control de progreso, evaluación formativa y trazabilidad.');

H2($seccion, '1.2 Alcance');
P($seccion, 'El sistema cubre:');
LI($seccion, 'Actividades: escenarios de evaluación por materia, con partes (contenido introductorio + evaluación de opción múltiple con contador de tiempo) asignadas a secciones.');
LI($seccion, 'Resolución secuencial por el estudiante: cada parte se desbloquea al completar la anterior, con calificación automática.');
LI($seccion, 'Panel del docente con seguimiento, asignación y evaluación de entregas.');
LI($seccion, 'Panel de progreso del estudiante con historial y reportes en PDF.');
LI($seccion, 'Gestión académica (períodos, secciones con varias materias, materias, usuarios).');
LI($seccion, 'Plantillas descargables: ERS, Matriz de Stakeholders, Backlog y Matriz de Riesgos.');

H2($seccion, '1.3 Usuarios del sistema');
P($seccion, 'El sistema define tres roles:');
P($seccion, '1. Administrador. Gestiona usuarios, períodos académicos, secciones, materias, niveles y las unidades de contenido (que los docentes usan como base para crear sus actividades). Acceso total al sistema.');
P($seccion, '2. Docente. Crea y asigna actividades de evaluación por materia, hace seguimiento de los estudiantes de su sección, revisa entregas y descarga reportes.');
P($seccion, '3. Estudiante. Accede a sus actividades asignadas por materia, las resuelve parte por parte, consulta su panel de progreso y descarga plantillas y su reporte en PDF.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 2. PRIMEROS PASOS
// ----------------------------------------------------------------------------

H1($seccion, '2. Primeros pasos');

H2($seccion, '2.1 Acceso al sistema');
P($seccion, 'Abra el navegador y diríjase a la URL del sistema. La pantalla inicial muestra el formulario de inicio de sesión.');

H3($seccion, 'Credenciales de acceso iniciales');
P($seccion, 'La primera vez que se despliega el sistema existen tres cuentas de prueba preconfiguradas:');
P($seccion, '   Administrador:  admin@admin.com  /  admin123');
P($seccion, '   Docente:        docente@admin.com  /  docente123');
P($seccion, '   Docente 2:      docente2@admin.com  /  docente123');
P($seccion, '   Estudiante:     estudiante@admin.com  /  estudiante123');
P($seccion, '   Estudiante 2:   estudiante2@admin.com  /  estudiante123');
NOTA($seccion, 'Por seguridad, cambie estas contraseñas desde Mi Perfil antes de pasar a producción.');

H2($seccion, '2.2 Inicio de sesión');
P($seccion, '1. Ingrese el correo electrónico.');
P($seccion, '2. Ingrese la contraseña.');
P($seccion, '3. Pulse Iniciar Sesión.');
P($seccion, 'Si las credenciales son correctas, el sistema redirige al Panel Principal correspondiente al rol del usuario. Si son incorrectas, se muestra un mensaje de error.');

H2($seccion, '2.3 Cierre de sesión');
P($seccion, 'En la esquina superior derecha pulse sobre su nombre y luego en Cerrar Sesión. La sesión se destruye y se vuelve a la pantalla de login.');

H2($seccion, '2.4 Cambio de contraseña (Mi Perfil)');
P($seccion, '1. Pulse sobre su nombre en la esquina superior derecha.');
P($seccion, '2. Seleccione Mi Perfil.');
P($seccion, '3. Modifique los campos deseados. El campo Nueva contraseña es opcional; solo complételo si desea cambiarla.');
P($seccion, '4. Pulse Actualizar Perfil.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 3. MODULO DE ADMINISTRACION
// ----------------------------------------------------------------------------

H1($seccion, '3. Módulo de Administración');

P($seccion, 'El administrador tiene acceso total al sistema. Desde el menú lateral izquierdo accede a los módulos: Períodos Académicos, Materias, Secciones, Gestor de Contenido, Niveles y Usuarios.');

H2($seccion, '3.1 Períodos Académicos');

H3($seccion, 'Listado');
P($seccion, 'Muestra todos los períodos registrados con su nombre, fechas de inicio y fin, cantidad de secciones asociadas y estado (Activo o Cerrado).');

H3($seccion, 'Crear un período');
P($seccion, '1. Pulse Nueva Período en la esquina superior derecha de la tarjeta.');
P($seccion, '2. Complete el nombre (por ejemplo, 2026-I o Enero-Abril 2026).');
P($seccion, '3. Seleccione la fecha de inicio y la fecha de fin. La fecha de fin debe ser posterior a la de inicio.');
P($seccion, '4. Elija el estado: Activo (visible y usable) o Cerrado (histórico).');
P($seccion, '5. Pulse Guardar.');

H3($seccion, 'Editar y eliminar');
P($seccion, 'Para modificar un período use el botón con icono de lápiz. Para eliminar, use el icono de papelera. Si el período tiene secciones asociadas, el sistema bloquea la eliminación y muestra un mensaje indicando que primero debe eliminar las secciones.');

H2($seccion, '3.2 Materias');

H3($seccion, 'Crear materia');
P($seccion, '1. Acceda a Materias desde el menú lateral.');
P($seccion, '2. Pulse Nueva Materia.');
P($seccion, '3. Ingrese el nombre (por ejemplo, Ingeniería de Requisitos o Bases de Datos).');
P($seccion, '4. Pulse Guardar.');

H3($seccion, 'Asociar materias a docentes');
P($seccion, 'La asociación docente-materia se hace al editar el usuario con rol Docente:');
P($seccion, '1. Vaya a Usuarios y edite al docente.');
P($seccion, '2. En la sección Materias que imparte marque las casillas correspondientes.');
P($seccion, '3. Pulse Guardar.');
P($seccion, 'Un docente puede impartir varias materias, y una materia puede ser impartida por varios docentes.');

H2($seccion, '3.3 Secciones');

H3($seccion, 'Crear sección');
P($seccion, '1. Vaya a Secciones desde el menú lateral.');
P($seccion, '2. Pulse Nueva Sección.');
P($seccion, '3. Ingrese el nombre (por ejemplo, Sección A o Grupo 1).');
P($seccion, '4. El sistema asigna automáticamente el Período Académico activo. Si no existe ninguno, el sistema muestra un aviso y bloquea la creación.');
P($seccion, '5. Marque las Materias que se imparten en la sección.');
P($seccion, '6. Para cada materia marcada, seleccione el Docente que la dicta. Una sección puede tener varias materias impartidas por docentes distintos.');
P($seccion, '7. Pulse Guardar.');

H3($seccion, 'Asignar estudiantes a una sección');
P($seccion, '1. En el listado de Secciones, pulse Estudiantes sobre la sección correspondiente.');
P($seccion, '2. Seleccione el período (sólo se mostrarán las del período activo por defecto).');
P($seccion, '3. Marque los estudiantes que desee agregar en el panel izquierdo y pulse Agregar a la sección.');
P($seccion, '4. Para remover, use el icono X junto al estudiante en el panel derecho.');

H2($seccion, '3.4 Gestor de Contenido');

H3($seccion, 'Crear una unidad de contenido');
P($seccion, '1. Vaya a Gestor de Contenido desde el menú lateral.');
P($seccion, '2. Pulse Nueva Unidad.');
P($seccion, '3. Seleccione la Materia a la que pertenece la unidad.');
P($seccion, '4. Complete el título, descripción, nivel (1 a 4), orden y estado (Publicado o Borrador).');
P($seccion, '5. Opcionalmente suba un video MP4, un documento PDF, una presentación PPTX y/o redacte HTML dinámico en el editor de texto enriquecido.');
P($seccion, '6. Pulse Guardar.');

H3($seccion, 'Tipos de archivo soportados');
P($seccion, '   Video MP4: máximo 100 MB.');
P($seccion, '   Documento PDF: cualquier tamaño razonable.');
P($seccion, '   Presentación PPTX: cualquier tamaño razonable.');
P($seccion, '   HTML dinámico: contenido redactado directamente, sin necesidad de archivo.');

H3($seccion, 'Editar y reemplazar archivos');
P($seccion, 'Al editar una unidad puede subir un nuevo archivo del mismo tipo; el sistema reemplaza automáticamente el anterior. Para eliminar un archivo use el icono X junto al nombre.');

H2($seccion, '3.5 Niveles');
P($seccion, 'Los cuatro niveles (Fundamentos, Análisis, Aplicación, Síntesis y Evaluación) son fijos del sistema. La página de Niveles es de solo lectura: muestra cada nivel, su descripción y la cantidad de unidades publicadas.');

H2($seccion, '3.6 Usuarios');

H3($seccion, 'Listado y filtros');
P($seccion, 'El listado muestra todos los usuarios con su rol y estado. Para los docentes se muestran también las materias que imparten, como etiquetas debajo del badge de rol.');

H3($seccion, 'Crear usuario');
P($seccion, '1. Pulse Nuevo.');
P($seccion, '2. Complete nombre, correo, teléfono (opcional) y contraseña (mínimo 8 caracteres alfanuméricos).');
P($seccion, '3. Seleccione el rol. Si elige Docente aparecerán las casillas de Materias que imparte.');
P($seccion, '4. Pulse Guardar.');

H3($seccion, 'Editar y eliminar');
P($seccion, 'Para modificar un usuario use el botón con icono de lápiz. No se puede eliminar el propio usuario autenticado (autoprotección).');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 4. MODULO DEL DOCENTE
// ----------------------------------------------------------------------------

H1($seccion, '4. Módulo del Docente');

P($seccion, 'El docente ve un menú lateral con las opciones: Actividades, Seguimiento, Asignar Escenarios, Evaluar Entregas y Plantillas.');

H2($seccion, '4.1 Actividades');

H3($seccion, 'Listado por materia');
P($seccion, 'Al ingresar a Actividades, el docente elige una de sus materias para ver sus actividades. Cada actividad muestra la unidad a la que pertenece, su estado (Publicada o Borrador), cantidad de partes, secciones asignadas y entregas recibidas.');

H3($seccion, 'Crear una actividad');
P($seccion, '1. Pulse Nueva Actividad.');
P($seccion, '2. Ingrese el título y la descripción.');
P($seccion, '3. Seleccione la Materia (solo las que imparte).');
P($seccion, '4. Seleccione la Unidad de esa materia en la que se enmarca la actividad. Las unidades las crea el administrador: si la materia no tiene unidades, el formulario muestra un aviso y no permite guardar.');
P($seccion, '5. Opcionalmente elija un Nivel de agrupación.');
P($seccion, '6. Elija el estado: Publicada (visible para los estudiantes) o Borrador.');
P($seccion, '7. Marque las Secciones que tendrán acceso. Solo los estudiantes de esas secciones podrán ver y resolver la actividad. Al cambiar la materia, la lista de unidades y de secciones se actualiza automáticamente.');
P($seccion, '8. Pulse Guardar.');

H3($seccion, 'Crear partes');
P($seccion, '1. Desde el listado, pulse Partes sobre la actividad.');
P($seccion, '2. Pulse Nueva Parte.');
P($seccion, '3. Ingrese el título, el orden y el estado (Activa o Inactiva).');
P($seccion, '4. En la Sección introductoria redacte el contenido (HTML) que el estudiante debe revisar antes de la evaluación.');
P($seccion, '5. Opcionalmente indique un Tiempo límite de evaluación en minutos: si se define, la evaluación tendrá un contador regresivo.');
P($seccion, '6. Pulse Guardar Parte.');

H3($seccion, 'Agregar preguntas a una parte');
P($seccion, '1. Abra la parte desde el listado de partes.');
P($seccion, '2. Pulse Nueva Pregunta.');
P($seccion, '3. Ingrese el enunciado, el orden y los puntos que vale la pregunta.');
P($seccion, '4. Complete las cuatro opciones y marque la Correcta (exactamente una). Cada opción puede tener una retroalimentación opcional.');
P($seccion, '5. Pulse Guardar Pregunta. Repita para cada pregunta de la evaluación.');

H3($seccion, 'Revisar entregas');
P($seccion, 'Desde el listado pulse Entregas sobre la actividad. La tabla muestra por cada estudiante su sección, estado (En progreso o Completada), partes completadas y la calificación obtenida cuando ha finalizado. La calificación se calcula automáticamente.');

H2($seccion, '4.2 Seguimiento de estudiantes');

H3($seccion, 'Vista general');
P($seccion, 'Muestra una tabla por cada sección a cargo del docente. Para cada estudiante se ven:');
LI($seccion, 'Unidades del Centro de Aprendizaje completadas.');
LI($seccion, 'Dinámicas intentadas y promedio de calificaciones.');
LI($seccion, 'Escenarios asignados, entregados y promedio de evaluaciones.');

H3($seccion, 'Reporte de sección en PDF');
P($seccion, 'En la esquina superior derecha de cada sección hay un botón PDF. Al pulsarlo se genera un PDF con el desempeño agregado de todos los estudiantes de esa sección, descargable o imprimible.');

H2($seccion, '4.3 Asignar escenarios');

H3($seccion, 'Crear asignación');
P($seccion, '1. Pulse Nueva Asignación.');
P($seccion, '2. Seleccione la Sección. La lista se filtra a las secciones a cargo del docente.');
P($seccion, '3. El sistema carga los estudiantes de esa sección; marque los que recibirán el escenario.');
P($seccion, '4. Seleccione el Escenario del banco disponible.');
P($seccion, '5. Opcionalmente indique una fecha límite y observaciones o indicaciones para el estudiante.');
P($seccion, '6. Pulse Asignar.');

H3($seccion, 'Estados de una asignación');
LI($seccion, 'Pendiente: el estudiante aún no ha entregado.');
LI($seccion, 'Entregado: el estudiante subió su respuesta, esperando evaluación.');
LI($seccion, 'Evaluado: el docente ya colocó calificación y retroalimentación.');

H2($seccion, '4.4 Evaluar entregas');

H3($seccion, 'Listado');
P($seccion, 'Por defecto se muestran las entregas pendientes (estado Entregado). Use los botones superiores para alternar entre Pendientes y Evaluados.');

H3($seccion, 'Proceso de evaluación');
P($seccion, '1. Pulse Evaluar sobre la entrega correspondiente.');
P($seccion, '2. Lea la respuesta del estudiante y, si adjuntó un archivo, descárguelo y revíselo.');
P($seccion, '3. Ingrese la calificación entre 0 y 100.');
P($seccion, '4. Escriba la retroalimentación (mínimo 10 caracteres).');
P($seccion, '5. Pulse Guardar evaluación. La asignación pasa a estado Evaluado y el estudiante recibe la notificación al iniciar sesión.');

H2($seccion, '4.5 Plantillas');
P($seccion, 'El docente puede descargar las mismas plantillas que el estudiante: ERS Simplificada, Matriz de Stakeholders, Tabla de Backlog y Matriz de Riesgos. Cada plantilla se descarga como un PDF con campos para llenar.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 5. MODULO DEL ESTUDIANTE
// ----------------------------------------------------------------------------

H1($seccion, '5. Módulo del Estudiante');

P($seccion, 'El estudiante ve un menú lateral con las opciones: Actividades, Mi Progreso y Plantillas.');

H2($seccion, '5.1 Actividades');

H3($seccion, 'Listado por materia');
P($seccion, 'Al ingresar a Actividades el estudiante ve sus materias. Al elegir una, se muestran las actividades asignadas a su sección por el docente. Las actividades en estado Borrador o no asignadas no aparecen.');

H3($seccion, 'Ver una actividad');
P($seccion, 'Al pulsar Ver Actividad se muestra el progreso general: las partes completadas, la parte actual y las partes bloqueadas. Las partes se resuelven en orden.');

H3($seccion, 'Resolver una evaluación');
P($seccion, '1. Pulse Continuar en la parte actual.');
P($seccion, '2. Revise la sección introductoria.');
P($seccion, '3. Responda todas las preguntas de opción múltiple. Si la parte tiene tiempo límite, se muestra un contador regresivo; al agotarse, la evaluación se envía automáticamente.');
P($seccion, '4. Pulse Enviar evaluación. La parte queda calificada y la siguiente se desbloquea.');
P($seccion, '5. Repita el proceso hasta completar todas las partes.');

H3($seccion, 'Resultado');
P($seccion, 'Al completar todas las partes, la actividad queda finalizada. Se muestra la calificación final (promedio de las partes) y el detalle por parte. Se considera aprobada una calificación igual o superior a 60.');

H2($seccion, '5.2 Mi Progreso');

H3($seccion, 'Indicadores');
P($seccion, 'La parte superior muestra cuatro indicadores: escenarios asignados, entregados, evaluados y promedio de calificaciones de escenarios.');

H3($seccion, 'Mapa de niveles');
P($seccion, 'Resume el avance en cada nivel del Centro de Aprendizaje: unidades completadas sobre el total y porcentaje de progreso.');

H3($seccion, 'Historial');
P($seccion, 'A la derecha se listan los intentos de dinámicas y el historial de escenarios.');

H3($seccion, 'Reporte en PDF');
P($seccion, 'En la parte superior derecha del panel hay un botón Descargar PDF. Genera un documento formal con el resumen completo del desempeño del estudiante.');

H2($seccion, '5.3 Plantillas');
P($seccion, 'Permite descargar cuatro plantillas en formato PDF: ERS Simplificada, Matriz de Stakeholders, Tabla de Backlog y Matriz de Riesgos. Cada plantilla tiene una estructura con campos y tablas para que el estudiante la complete como parte de sus proyectos.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 6. DESBLOQUEO PROGRESIVO
// ----------------------------------------------------------------------------

H1($seccion, '6. Modelo de evaluación por Actividades');

H2($seccion, '6.1 Estructura');
P($seccion, 'Cada actividad pertenece a una unidad de una materia y se compone de una o más partes. Las unidades las crea el administrador; el docente solo puede crear actividades dentro de unidades existentes. Cada parte tiene una sección introductoria (contenido HTML) y una evaluación de opción múltiple con preguntas calificables. Opcionalmente, la evaluación tiene un contador de tiempo límite.');
P($seccion, 'La actividad se asigna a una o varias secciones; solo los estudiantes de esas secciones tienen acceso.');

H2($seccion, '6.2 Avance secuencial');
P($seccion, 'El estudiante resuelve las partes en orden: debe completar la evaluación de una parte para desbloquear la siguiente. Al completar todas las partes activas, la actividad queda finalizada y se calcula la calificación final.');

H2($seccion, '6.3 Calificación');
P($seccion, 'Cada parte obtiene un puntaje de 0 a 100 según las respuestas correctas. La calificación final de la actividad es el promedio de las partes. Se considera aprobada una calificación igual o superior a 60.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 7. REPORTES Y PLANTILLAS PDF
// ----------------------------------------------------------------------------

H1($seccion, '7. Reportes y plantillas en PDF');

P($seccion, 'El sistema utiliza la librería DomPDF para generar documentos PDF reales (no HTML imprimible). Los documentos se abren o descargan directamente desde el navegador.');

H2($seccion, '7.1 Reporte de desempeño del estudiante');
P($seccion, 'Generado desde Mi Progreso. Incluye:');
LI($seccion, 'Encabezado con datos del estudiante.');
LI($seccion, 'Indicadores generales (promedio de dinámicas, promedio de escenarios, total evaluados).');
LI($seccion, 'Tabla de progreso por nivel.');
LI($seccion, 'Detalle de cada escenario asignado con su estado y calificación.');

H2($seccion, '7.2 Reporte de sección del docente');
P($seccion, 'Generado desde Seguimiento. Incluye una tabla con el desempeño de todos los estudiantes de la sección, con promedios calculados.');

H2($seccion, '7.3 Plantillas descargables');
P($seccion, 'Desde el menú Plantillas, tanto el docente como el estudiante pueden descargar:');
LI($seccion, 'ERS Simplificada: estructura para documentar la especificación de requisitos de un sistema.');
LI($seccion, 'Matriz de Stakeholders: identificación, matriz Poder-Interés y estrategias de gestión.');
LI($seccion, 'Tabla de Backlog: historias de usuario con prioridad MoSCoW y story points.');
LI($seccion, 'Matriz de Riesgos: catálogo de riesgos con probabilidad, impacto y plan de mitigación.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 8. SEGURIDAD Y BUENAS PRACTICAS
// ----------------------------------------------------------------------------

H1($seccion, '8. Seguridad y buenas prácticas');

H2($seccion, '8.1 Contraseñas');
LI($seccion, 'Las contraseñas se almacenan con hash bcrypt (algoritmo por defecto de PHP password_hash).');
LI($seccion, 'No se transmiten en texto plano después del login gracias al uso de sesiones del lado del servidor.');
LI($seccion, 'Mínimo 8 caracteres alfanuméricos al crear un usuario.');

H2($seccion, '8.2 Roles y permisos');
LI($seccion, 'Cada controlador verifica el rol del usuario al inicio. Si un usuario sin permisos accede a una URL protegida, es redirigido al Panel Principal con un mensaje de error.');
LI($seccion, 'Los estudiantes nunca pueden ver información de otros estudiantes ni acceder a rutas administrativas.');

H2($seccion, '8.3 Subida de archivos');
LI($seccion, 'Los archivos subidos (videos, PDFs, presentaciones, entregas) se almacenan fuera del directorio público de uploads para archivos de entregas y en /uploads/unidades/{tipo} para contenido.');
LI($seccion, 'El servidor web está configurado (vía .htaccess) para impedir la ejecución de scripts PHP dentro de las carpetas de uploads.');
LI($seccion, 'Los nombres de archivo se renombran a un identificador único (uniqid) para evitar colisiones y ataques de path traversal.');

H2($seccion, '8.4 Inyección SQL');
P($seccion, 'Todas las consultas a la base de datos se realizan mediante sentencias preparadas (PDO con emulación desactivada), por lo que la inyección SQL no es viable.');

H2($seccion, '8.5 XSS');
P($seccion, 'Todo dato dinámico que se muestra en las vistas pasa por htmlspecialchars(), evitando Cross-Site Scripting. El contenido HTML dinámico cargado por el docente en las unidades se sanitiza con strip_tags sobre una lista blanca de etiquetas permitidas.');

H2($seccion, '8.6 Recomendaciones para producción');
LI($seccion, 'Cambiar las contraseñas de las cuentas de prueba.');
LI($seccion, 'Configurar HTTPS y forzar la redirección.');
LI($seccion, 'Desactivar la directiva display_errors de PHP.');
LI($seccion, 'Realizar respaldos periódicos de la base de datos.');
LI($seccion, 'Cambiar las credenciales de la base de datos en config/database.php por valores fuertes.');

$seccion->addPageBreak();

// ----------------------------------------------------------------------------
// 9. SOPORTE
// ----------------------------------------------------------------------------

H1($seccion, '9. Soporte y contacto');

P($seccion, 'Para reportar incidencias, solicitar mejoras o resolver dudas técnicas:');

LI($seccion, 'Departamento de Informática - UPTP-LMR.');
LI($seccion, 'Correo: [correo de soporte institucional].');
LI($seccion, 'Coordinación del PNFI.');

P($seccion, '');
P($seccion, '');
P($seccion, '— Fin del manual —', ['alignment' => Jc::CENTER, 'color' => $colorClaro, 'italic' => true]);

// =============== GUARDAR ===============

$nombreArchivo = isset($argv[1]) && $argv[1] !== '' ? $argv[1] : ('manual_sistema_' . date('Ymd') . '.docx');
$rutaSalida = __DIR__ . '/' . $nombreArchivo;

$writer = IOFactory::createWriter($phpWord, 'Word2007');
$writer->save($rutaSalida);

echo "Manual generado exitosamente en:\n";
echo "  $rutaSalida\n";
echo "\nTamano: " . number_format(filesize($rutaSalida) / 1024, 1) . " KB\n";
