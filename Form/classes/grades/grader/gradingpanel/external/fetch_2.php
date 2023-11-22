<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Web services relating to fetching of a marking guide for the grading panel.
 *
 * @package    gradingform_guide
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);

namespace gradingform_guide\grades\grader\gradingpanel\external;

// Importación de clases y namespaces necesarios.
use coding_exception;
use context;
use core_user;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_exception;
use stdClass;

// Se requiere el archivo lib.php del directorio de grading/form/guide.
require_once($CFG->dirroot . '/grade/grading/form/guide/lib.php');

// Declaración estricta de tipos.
declare(strict_types=1);

namespace gradingform_guide\grades\grader\gradingpanel\external;

// Importación de clases y namespaces necesarios.
use coding_exception;
use context;
use core_user;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use moodle_exception;
use stdClass;

// Se requiere el archivo lib.php del directorio de grading/form/guide.
require_once($CFG->dirroot . '/grade/grading/form/guide/lib.php');

/**
 * Web services relating to fetching of a marking guide for the grading panel.
 *
 * @package    gradingform_guide
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch extends external_api {

    /**
     * Describes the parameters for fetching the grading panel for a simple grade.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */

/**
 * Define y devuelve los parámetros de ejecución para la función externa.
 *
 * @return external_function_parameters Parámetros de la función externa.
 */
public static function execute_parameters(): external_function_parameters {
    return new external_function_parameters([
        'component' => new external_value(
            PARAM_ALPHANUMEXT,
            'El nombre del componente',
            VALUE_REQUIRED
        ),
        'contextid' => new external_value(
            PARAM_INT,
            'El ID del contexto que se está calificando',
            VALUE_REQUIRED
        ),
        'itemname' => new external_value(
            PARAM_ALPHANUM,
            'El nombre del elemento de calificación que se está calificando',
            VALUE_REQUIRED
        ),
        'gradeduserid' => new external_value(
            PARAM_INT,
            'El ID del usuario mostrado',
            VALUE_REQUIRED
        ),
    ]);
}
/**
 * Recupera los datos necesarios para construir un panel de calificación para una calificación simple.
 *
 * @param string $component El nombre del componente.
 * @param int $contextid El ID del contexto.
 * @param string $itemname El nombre del elemento de calificación.
 * @param int $gradeduserid El ID del usuario calificado.
 * @return array Los datos necesarios para construir el panel de calificación.
 * @throws \dml_exception
 * @throws \invalid_parameter_exception
 * @throws \restricted_context_exception
 * @throws coding_exception
 * @throws moodle_exception
 * @since Moodle 3.8
 */
public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
    global $CFG, $USER;

    // Se requiere gradelib.php del directorio lib.
    require_once("{$CFG->libdir}/gradelib.php");

    // Validación de parámetros utilizando la función validate_parameters.
    [
        'component' => $component,
        'contextid' => $contextid,
        'itemname' => $itemname,
        'gradeduserid' => $gradeduserid,
    ] = self::validate_parameters(self::execute_parameters(), [
        'component' => $component,
        'contextid' => $contextid,
        'itemname' => $itemname,
        'gradeduserid' => $gradeduserid,
    ]);

    // Resto del código aquí...

    // El resto del código debería ir aquí, ya que no se proporciona en la muestra.
}

// Validar el contexto.
$context = context::instance_by_id($contextid);
self::validate_context($context);

// Validar que el itemname suministrado sea un elemento gradable.
if (!component_gradeitems::is_valid_itemname($component, $itemname)) {
    throw new coding_exception("El elemento '{$itemname}' no es válido para el componente '{$component}'");
}

// Obtener la instancia del elemento de calificación.
$gradeitem = gradeitem::instance($component, $context, $itemname);

// Verificar que el método de calificación avanzada sea el de un marking guide.
if (MARKING_GUIDE !== $gradeitem->get_advanced_grading_method()) {
    throw new moodle_exception(
        "El elemento {$itemname} en {$component}/{$contextid} no está configurado para calificación avanzada con un marking guide"
    );
}

// Obtener los datos reales.
$gradeduser = core_user::get_user($gradeduserid, '*', MUST_EXIST);

// Uno puede acceder a sus propias calificaciones. Otros solo si son calificadores.
if ($gradeduserid != $USER->id) {
    $gradeitem->require_user_can_grade($gradeduser, $USER);
}

// Devolver los datos obtenidos.
return self::get_fetch_data($gradeitem, $gradeduser);

/**
 * Obtener los datos que se van a recuperar.
 *
 * @param gradeitem $gradeitem La instancia del elemento de calificación.
 * @param stdClass $gradeduser El usuario calificado.
 * @return array Los datos obtenidos.
 */
public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser): array {
    global $USER;

    // Verificar si el usuario tiene una calificación.
    $hasgrade = $gradeitem->user_has_grade($gradeduser);

    // Obtener la calificación formateada para el usuario actual.
    $grade = $gradeitem->get_formatted_grade_for_user($gradeduser, $USER);

    // Obtener la instancia de calificación avanzada.
    $instance = $gradeitem->get_advanced_grading_instance($USER, $grade);

    // Lanzar una excepción si la instancia no está disponible.
    if (!$instance) {
        throw new moodle_exception('error:gradingunavailable', 'grading');
    }

    // Obtener el controlador de la instancia de calificación.
    $controller = $instance->get_controller();

    // Obtener la definición del controlador.
    $definition = $controller->get_definition();

    // Obtener el llenado de la guía.
    $fillings = $instance->get_guide_filling();

    // Obtener el contexto del controlador.
    $context = $controller->get_context();

    // Obtener el ID de definición como entero.
    $definitionid = (int) $definition->id;

    // Devolver los datos obtenidos.
    return [
        'hasgrade' => $hasgrade,
        'grade' => $grade,
        'fillings' => $fillings,
        'context' => $context,
        'definitionid' => $definitionid,
    ];
}

 // Set up some items we need to return on other interfaces.
 $gradegrade = \grade_grade::fetch(['itemid' => $gradeitem->get_grade_item()->id, 'userid' => $gradeduser->id]);
 $gradername = $gradegrade ? fullname(\core_user::get_user($gradegrade->usermodified)) : null;
 $maxgrade = max(array_keys($controller->get_grade_range()));

 $criterion = [];
 if ($definition->guide_criteria) {
     $criterion = array_map(function($criterion) use ($definitionid, $fillings, $context) {
         $result = [
             'id' => $criterion['id'],
             'name' => $criterion['shortname'],
             'maxscore' => $criterion['maxscore'],
             'description' => self::get_formatted_text(
                 $context,
                 $definitionid,
                 'description',
                 $criterion['description'],
                 (int) $criterion['descriptionformat']
             ),
             'descriptionmarkers' => self::get_formatted_text(
                 $context,
                 $definitionid,
                 'descriptionmarkers',
                 $criterion['descriptionmarkers'],
                 (int) $criterion['descriptionmarkersformat']
             ),
             'score' => null,
             'remark' => null,
         ];

        // Verificar si existen criterios en la guía.
if ($definition->guide_criteria) {
    // Mapear cada criterio y obtener la información necesaria.
    $criterion = array_map(function ($criterion) use ($definitionid, $fillings, $context) {
        $result = [
            'id' => $criterion['id'],
            'name' => $criterion['shortname'],
            'maxscore' => $criterion['maxscore'],
            'description' => self::get_formatted_text(
                $context,
                $definitionid,
                'description',
                $criterion['description'],
                (int)$criterion['descriptionformat']
            ),
            'descriptionmarkers' => self::get_formatted_text(
                $context,
                $definitionid,
                'descriptionmarkers',
                $criterion['descriptionmarkers'],
                (int)$criterion['descriptionmarkersformat']
            ),
            'score' => null,
            'remark' => null,
        ];

        // Verificar si el criterio está presente en los llenados.
        if (array_key_exists($criterion['id'], $fillings['criteria'])) {
            $filling = $fillings['criteria'][$criterion['id']];

            // Actualizar los datos con la información del llenado.
            $result['score'] = $filling['score'];
            $result['remark'] = self::get_formatted_text(
                $context,
                $definitionid,
                'remark',
                $filling['remark'],
                (int)$filling['remarkformat']
            );
        }

        return $result;
    }, $definition->guide_criteria);
}

// Inicializar el arreglo de comentarios.
$comments = [];

// Verificar si existen comentarios en la guía.
if ($definition->guide_comments) {
    // Mapear cada comentario y obtener la información necesaria.
    $comments = array_map(function ($comment) use ($definitionid, $context) {
        return [
            'id' => $comment['id'],
            'sortorder' => $comment['sortorder'],
            'description' => self::get_formatted_text(
                $context,
                $definitionid,
                'description',
                $comment['description'],
                (int)$comment['descriptionformat']
            ),
        ];
    }, $definition->guide_comments);
}

// Devolver los datos obtenidos.
return [
    'templatename' => 'gradingform_guide/grades/grader/gradingpanel',
    'hasgrade' => $hasgrade,
    'grade' => [
        'instanceid' => $instance->get_id(),
        'criterion' => $criterion,
        'hascomments' => !empty($comments),
        'comments' => $comments,
        'usergrade' => $grade->usergrade,
        'maxgrade' => $maxgrade,
        'gradedby' => $gradername,
        'timecreated' => $grade->timecreated,
        'timemodified' => $grade->timemodified,
    ],
    'warnings' => [],
];

/**
 * Describe los datos devueltos por la función externa.
 *
 * @return external_single_structure Estructura de datos externa para la función.
 * @since Moodle 3.8
 */
public static function execute_returns(): external_single_structure {
    // Devolver una nueva estructura de datos externa que describe los datos devueltos por la función.
    return new external_single_structure([
        'templatename' => new external_value(PARAM_SAFEPATH, 'La plantilla a utilizar al renderizar estos datos'),
        'hasgrade' => new external_value(PARAM_BOOL, '¿Tiene el usuario una calificación?'),
        'grade' => new external_single_structure([
            'instanceid' => new external_value(PARAM_INT, 'El ID de la instancia actual de calificación'),
            'criterion' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'El ID del criterio'),
                    'name' => new external_value(PARAM_RAW, 'El nombre del criterio'),
                    'maxscore' => new external_value(PARAM_FLOAT, 'La puntuación máxima para este criterio'),
                    'description' => new external_value(PARAM_RAW, 'La descripción del criterio'),
                    'descriptionmarkers' => new external_value(PARAM_RAW, 'La descripción del criterio para los evaluadores'),
                    'score' => new external_value(PARAM_FLOAT, 'La puntuación actual para el usuario evaluado', VALUE_OPTIONAL),
                    'remark' => new external_value(PARAM_RAW, 'Comentarios para este criterio para el usuario evaluado', VALUE_OPTIONAL),
                ]),
                'El criterio por el cual se calificará este elemento'
            ),
            'hascomments' => new external_value(PARAM_BOOL, '¿Hay comentarios frecuentemente utilizados?'),
            'comments' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'ID del comentario'),
                    'sortorder' => new external_value(PARAM_INT, 'El orden de clasificación de este comentario'),
                    'description' => new external_value(PARAM_RAW, 'El valor del comentario'),
                ]),
                'Comentarios frecuentemente utilizados'
            ),
            'usergrade' => new external_value(PARAM_RAW, 'Calificación actual del usuario'),
            'maxgrade' => new external_value(PARAM_RAW, 'Calificación máxima posible'),
            'gradedby' => new external_value(PARAM_RAW, 'El evaluador asumido de esta instancia de calificación'),
            'timecreated' => new external_value(PARAM_INT, 'La hora en que se creó la calificación'),
            'timemodified' => new external_value(PARAM_INT, 'La hora en que se actualizó por última vez la calificación'),
        ]),
        'warnings' => new external_warnings(),
    ]);
}

/**
 * Obtiene una versión formateada de la observación/descripción/etc.
 *
 * @param context $context El contexto de Moodle.
 * @param int $definitionid El ID de la definición.
 * @param string $filearea El área de archivo del campo.
 * @param string $text El texto a formatear.
 * @param int $format El formato de entrada de la cadena.
 * @return string La cadena formateada.
 */
protected static function get_formatted_text(context $context, int $definitionid, string $filearea, string $text, int $format): string {
    // Opciones de formato para el texto.
    $formatoptions = [
        'noclean' => false,
        'trusted' => false,
        'filter' => true,
    ];

    // Formatear el texto utilizando la utilidad de formato de texto externo de Moodle.
    [$newtext] = \core_external\util::format_text(
        $text,
        $format,
        $context,
        'grading',
        $filearea,
        $definitionid,
        $formatoptions
    );

    // Devolver la cadena formateada.
    return $newtext;
}
