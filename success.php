<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
/**
 * Page to display success message after importing questions.
 *
 * @package    local_questionconverter
 * @copyright  2025 Tu Nombre
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Obtener parámetros
$courseid = required_param('courseid', PARAM_INT);
$total = required_param('total', PARAM_INT);
$categories = optional_param('categories', 1, PARAM_INT);
$categoryid = required_param('categoryid', PARAM_INT);

// Verificar acceso
$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);
require_capability('moodle/question:add', $context);

// Configurar la página
$PAGE->set_url(new moodle_url('/local/questionconverter/success.php', [
    'courseid' => $courseid,
    'total' => $total,
    'categories' => $categories,
    'categoryid' => $categoryid
]));
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(get_string('success', 'local_questionconverter'));
$PAGE->set_heading($course->fullname);

// URL para ir al banco de preguntas
$question_bank_url = new moodle_url('/question/edit.php', [
    'courseid' => $courseid,
    'cat' => $categoryid . ',' . $context->id
]);

echo $OUTPUT->header();

// Mostrar mensaje de éxito con diseño mejorado
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <!-- Card de éxito -->
            <div class="card shadow-lg border-0 mb-4">
                <!-- Header verde de éxito -->
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="mb-3">
                        <i class="fa fa-check-circle fa-4x"></i>
                    </div>
                    <h2 class="mb-0"><?php echo get_string('importsuccess', 'local_questionconverter'); ?></h2>
                </div>
                
                <!-- Cuerpo del card -->
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <p class="lead">
                            <?php echo get_string('importsuccessdesc', 'local_questionconverter'); ?>
                        </p>
                    </div>
                    
                    <!-- Estadísticas -->
                    <div class="row text-center mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h1 class="display-4 text-primary mb-0"><?php echo $total; ?></h1>
                                    <p class="text-muted mb-0">
                                        <?php echo get_string('questionsimported', 'local_questionconverter'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h1 class="display-4 text-success mb-0"><?php echo $categories; ?></h1>
                                    <p class="text-muted mb-0">
                                        <?php echo get_string('categoriescreated', 'local_questionconverter'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Spinner y contador -->
                    <div class="text-center mb-4">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="text-muted">
                            <?php echo get_string('redirecting', 'local_questionconverter'); ?>
                            <strong><span id="countdown">5</span></strong> 
                            <?php echo get_string('seconds', 'local_questionconverter'); ?>
                        </p>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="text-center">
                        <a href="<?php echo $question_bank_url; ?>" class="btn btn-primary btn-lg mr-2">
                            <i class="fa fa-question-circle"></i>
                            <?php echo get_string('gotoquestionbank', 'local_questionconverter'); ?>
                        </a>
                        <a href="<?php echo new moodle_url('/course/view.php', ['id' => $courseid]); ?>" 
                           class="btn btn-outline-secondary btn-lg">
                            <i class="fa fa-home"></i>
                            <?php echo get_string('backtocourse', 'local_questionconverter'); ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="alert alert-info">
                <h5 class="alert-heading">
                    <i class="fa fa-info-circle"></i>
                    <?php echo get_string('nextsteps', 'local_questionconverter'); ?>
                </h5>
                <ol class="mb-0">
                    <li><?php echo get_string('nextstep1', 'local_questionconverter'); ?></li>
                    <li><?php echo get_string('nextstep2', 'local_questionconverter'); ?></li>
                    <li><?php echo get_string('nextstep3', 'local_questionconverter'); ?></li>
                </ol>
            </div>
            
        </div>
    </div>
</div>

<script>
// Contador regresivo y redirección automática
let seconds = 5;
const countdownEl = document.getElementById('countdown');
const redirectUrl = '<?php echo $question_bank_url; ?>';

const interval = setInterval(() => {
    seconds--;
    if (countdownEl) {
        countdownEl.textContent = seconds;
    }
    
    if (seconds <= 0) {
        clearInterval(interval);
        window.location.href = redirectUrl;
    }
}, 1000);
</script>

<?php
echo $OUTPUT->footer();