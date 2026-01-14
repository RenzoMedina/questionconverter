<?php
/**
 * Debug info - Archivo temporal para verificar configuración
 * ELIMINAR en producción
 *
 * @package    local_questionconverter
 */

require_once(__DIR__ . '/../../config.php');

// Requiere login
require_login();

// Solo para administradores
require_capability('moodle/site:config', context_system::instance());

// Configurar la página
$PAGE->set_url(new moodle_url('/local/questionconverter/debug.php'));
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Question Converter - Debug Info');
$PAGE->set_heading('Question Converter - Información de Depuración');

echo $OUTPUT->header();

echo '<h2>Información de Depuración - Question Converter</h2>';

// 1. Verificar vendor
echo '<h3>1. Verificar Composer/Vendor</h3>';
$vendor_path = __DIR__ . '/vendor/autoload.php';
if (file_exists($vendor_path)) {
    echo '<p style="color: green;">✓ vendor/autoload.php encontrado</p>';
    
    require_once($vendor_path);
    
    if (class_exists('Smalot\PdfParser\Parser')) {
        echo '<p style="color: green;">✓ Clase Smalot\PdfParser\Parser cargada correctamente</p>';
        
        try {
            $parser = new \Smalot\PdfParser\Parser();
            echo '<p style="color: green;">✓ Parser instanciado correctamente</p>';
        } catch (Exception $e) {
            echo '<p style="color: red;">✗ Error al instanciar Parser: ' . $e->getMessage() . '</p>';
        }
    } else {
        echo '<p style="color: red;">✗ Clase Smalot\PdfParser\Parser no encontrada</p>';
    }
} else {
    echo '<p style="color: red;">✗ vendor/autoload.php NO encontrado</p>';
    echo '<p>Ejecuta: <code>cd ' . __DIR__ . ' && composer install</code></p>';
}

// 2. Verificar clases del plugin
echo '<h3>2. Verificar Clases del Plugin</h3>';

$classes = [
    'pdf_parser' => __DIR__ . '/classes/converter/pdf_parser.php',
    'question_importer' => __DIR__ . '/classes/converter/question_importer.php',
];

foreach ($classes as $name => $path) {
    if (file_exists($path)) {
        echo '<p style="color: green;">✓ ' . $name . '.php encontrado</p>';
    } else {
        echo '<p style="color: red;">✗ ' . $name . '.php NO encontrado</p>';
    }
}

// 3. Verificar templates
echo '<h3>3. Verificar Templates</h3>';

$templates = [
    'index' => __DIR__ . '/templates/main.mustache',
];

foreach ($templates as $name => $path) {
    if (file_exists($path)) {
        echo '<p style="color: green;">✓ Template ' . $name . '.mustache encontrado</p>';
    } else {
        echo '<p style="color: red;">✗ Template ' . $name . '.mustache NO encontrado</p>';
    }
}

// 4. Verificar archivos de idioma
echo '<h3>4. Verificar Archivos de Idioma</h3>';

$lang_path = __DIR__ . '/lang/es/local_questionconverter.php';
if (file_exists($lang_path)) {
    echo '<p style="color: green;">✓ Archivo de idioma español encontrado</p>';
} else {
    echo '<p style="color: red;">✗ Archivo de idioma español NO encontrado</p>';
}

// 5. Verificar capabilities
echo '<h3>5. Verificar Capabilities</h3>';

$context = context_system::instance();
$capabilities = [
    'moodle/question:add' => 'Añadir preguntas',
    'local/questionconverter:use' => 'Usar convertidor',
];

foreach ($capabilities as $cap => $desc) {
    if (has_capability($cap, $context)) {
        echo '<p style="color: green;">✓ Capability ' . $cap . ' (' . $desc . ')</p>';
    } else {
        echo '<p style="color: orange;">⚠ Capability ' . $cap . ' no disponible (puede ser normal)</p>';
    }
}

// 6. Verificar cursos disponibles
echo '<h3>6. Verificar Cursos Disponibles</h3>';

$courses = $DB->get_records_sql(
    "SELECT id, fullname, shortname 
     FROM {course} 
     WHERE id > 1 
     ORDER BY id ASC 
     LIMIT 5"
);

if ($courses) {
    echo '<p>Cursos de ejemplo (primeros 5):</p>';
    echo '<ul>';
    foreach ($courses as $course) {
        $url = new moodle_url('/local/questionconverter/index.php', ['courseid' => $course->id]);
        echo '<li><a href="' . $url . '">' . $course->fullname . ' (ID: ' . $course->id . ')</a></li>';
    }
    echo '</ul>';
} else {
    echo '<p style="color: orange;">No hay cursos creados (además del sitio)</p>';
}

// 7. Información del sistema
echo '<h3>7. Información del Sistema</h3>';
echo '<ul>';
echo '<li><strong>Moodle versión:</strong> ' . $CFG->version . ' (' . $CFG->release . ')</li>';
echo '<li><strong>PHP versión:</strong> ' . phpversion() . '</li>';
echo '<li><strong>Plugin path:</strong> ' . __DIR__ . '</li>';
echo '<li><strong>Vendor path:</strong> ' . ($vendor_path ?? 'N/A') . '</li>';
echo '</ul>';

// 8. Instrucciones
echo '<h3>8. Próximos Pasos</h3>';
echo '<div style="background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3;">';
echo '<p><strong>Si todo está en verde (✓):</strong></p>';
echo '<ol>';
echo '<li>Selecciona un curso de la lista anterior</li>';
echo '<li>Haz clic en el enlace del curso</li>';
echo '<li>Prueba subiendo un PDF</li>';
echo '</ol>';
echo '<p><strong>Si hay errores en rojo (✗):</strong></p>';
echo '<ol>';
echo '<li>Verifica que hayas instalado las dependencias: <code>composer install</code></li>';
echo '<li>Verifica que todos los archivos del plugin estén en su lugar</li>';
echo '<li>Purga las cachés de Moodle</li>';
echo '</ol>';
echo '</div>';

echo '<p style="color: red; margin-top: 20px;"><strong>IMPORTANTE:</strong> Elimina este archivo (debug.php) en producción por seguridad.</p>';

echo $OUTPUT->footer();