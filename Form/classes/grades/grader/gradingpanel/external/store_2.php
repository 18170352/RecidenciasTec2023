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

// Importar las clases y funciones necesarias.
global $CFG;
use coding_exception;
use context;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
use core_user;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use moodle_exception;

// Incluir el archivo necesario.
require_once($CFG->dirroot.'/grade/grading/form/guide/lib.php');

/**
 * Web services relating to storing of a marking guide for the grading panel.
 *
 * @package    gradingform_guide
 * @copyright  2019 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
 * Clase que maneja el almacenamiento a través de la API externa.
 */
class store extends external_api {

    /**
     * Describe los parámetros para almacenar el panel de calificación para una calificación simple.
     *
     * @return external_function_parameters Los parámetros para la ejecución.
     * @since Moodle 3.8
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
                'El ID del contexto en el que se está calificando',
                VALUE_REQUIRED
            ),
            'itemname' => new external_value(
                PARAM_ALPHANUM,
                'El nombre del ítem de calificación que se está calificando',
                VALUE_REQUIRED
            ),
            'gradeduserid' => new external_value(
                PARAM_INT,
                'El ID del usuario que se está calificando',
                VALUE_REQUIRED
            ),
            'notifyuser' => new external_value(
                PARAM_BOOL,
                'Si notificar al usuario o no',
                VALUE_DEFAULT,
                false
            ),
            'formdata' => new external_value(
                PARAM_RAW,
                'Los datos del formulario serializados que representan la calificación',
                VALUE_REQUIRED
            ),
        ]);
    }
}

/**
 * Clase que maneja el almacenamiento a través de la API externa.
 */
class store extends external_api {

    /**
     * Obtiene los datos necesarios para construir un panel de calificación para una calificación simple.
     *
     * @param string $component El nombre del componente.
     * @param int $contextid El ID del contexto.
     * @param string $itemname El nombre del ítem de calificación.
     * @param int $gradeduserid El ID del usuario calificado.
     * @param bool $notifyuser Indica si se notificará al usuario.
     * @param string $formdata Los datos del formulario serializados que representan la calificación.
     *
     * @return array Los datos necesarios para construir el panel de calificación.
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     * @throws coding_exception
     * @throws moodle_exception
     * @since Moodle 3.8
     */
    public static function execute(
        string $component,
        int $contextid,
        string $itemname,
        int $gradeduserid,
        bool $notifyuser,
        string $formdata
    ): array {
        global $USER;

        // Validar los parámetros utilizando la función validate_parameters.
        [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
            'notifyuser' => $notifyuser,
            'formdata' => $formdata,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
            'notifyuser' => $notifyuser,
            'formdata' => $formdata,
        ]);

        // Resto del código...
    }
}

/**
 * Clase que maneja el almacenamiento a través de la API externa.
 */
class store extends external_api {

    /**
     * Obtiene los datos necesarios para construir un panel de calificación para una calificación simple.
     *
     * @param string $component El nombre del componente.
     * @param int $contextid El ID del contexto.
     * @param string $itemname El nombre del ítem de calificación.
     * @param int $gradeduserid El ID del usuario calificado.
     * @param bool $notifyuser Indica si se notificará al usuario.
     * @param string $formdata Los datos del formulario serializados que representan la calificación.
     *
     * @return array Los datos necesarios para construir el panel de calificación.
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \restricted_context_exception
     * @throws coding_exception
     * @throws moodle_exception
     * @since Moodle 3.8
     */
    public static function execute(
        string $component,
        int $contextid,
        string $itemname,
        int $gradeduserid,
        bool $notifyuser,
        string $formdata
    ): array {
        global $USER;

        // Validar el contexto.
        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // Validar que el ítem de nombre suministrado es un ítem calificable.
        if (!component_gradeitems::is_valid_itemname($component, $itemname)) {
            throw new coding_exception("El ítem '{$itemname}' no es válido para el componente '{$component}'");
        }

        // Obtener la instancia del ítem de calificación.
        $gradeitem = gradeitem::instance($component, $context, $itemname);

        // Validar que este ítem de calificación esté realmente habilitado.
        if (!$gradeitem->is_grading_enabled()) {
            throw new moodle_exception("La calificación no está habilitada para {$itemname} en este contexto");
        }

        // Obtener el registro del usuario calificado.
        $gradeduser = core_user::get_user($gradeduserid);

        // Requerir que este usuario pueda guardar calificaciones.
        $gradeitem->require_user_can_grade($gradeduser, $USER);

        // Validar que este ítem de calificación esté configurado para calificación avanzada con una guía de calificación.
        if (MARKING_GUIDE !== $gradeitem->get_advanced_grading_method()) {
            throw new moodle_exception(
                "El ítem {$itemname} en {$component}/{$contextid} no está configurado para la calificación avanzada con una guía de calificación"
            );
        }

        // Analizar la cadena serializada en un objeto.
        $data = [];
        parse_str($formdata, $data);

        // Calificar.
        $gradeitem->store_grade_from_formdata($gradeduser, $USER, (object) $data);

        // Notificar.
        if ($notifyuser) {
            // Enviar notificación al estudiante.
            $gradeitem->send_student_notification($gradeduser, $USER);
        }

        // Obtener y devolver los datos obtenidos.
        return fetch::get_fetch_data($gradeitem, $gradeduser);
    }

    /**
     * Describe los datos devueltos desde la función externa.
     *
     * @return external_single_structure
     * @since Moodle 3.8
     */
    public static function execute_returns(): external_single_structure {
        return fetch::execute_returns();
    }
}

?>