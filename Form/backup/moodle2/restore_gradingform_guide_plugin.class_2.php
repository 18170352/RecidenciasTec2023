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
 * Support for restore API
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Restores the marking guide specific data from grading.xml file
 *
 * @package    gradingform_guide
 * @copyright  2012 Dan Marsden <dan@danmarsden.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 /**
 * Clase que extiende la funcionalidad de restauración de un plugin de guía de calificación.
 */
class restore_gradingform_guide_plugin extends restore_gradingform_plugin {

    /**
     * Declara las rutas XML de la guía de marcado adjuntas al elemento de definición del formulario.
     *
     * @return array de {@link restore_path_element}
     */
    protected function define_definition_plugin_structure() {

        // Arreglo para almacenar las rutas.
        $paths = array();

        // Ruta para los criterios de la guía de calificación.
        $paths[] = new restore_path_element('gradingform_guide_criterion',
            $this->get_pathfor('/guidecriteria/guidecriterion'));

        // Ruta para los comentarios de la guía de calificación.
        $paths[] = new restore_path_element('gradingform_guide_comment',
            $this->get_pathfor('/guidecomments/guidecomment'));

        // MDL-37714: Localiza correctamente los comentarios frecuentemente utilizados en ambos
        // formatos, el actual y el antiguo incorrecto.
        $paths[] = new restore_path_element('gradingform_guide_comment_legacy',
            $this->get_pathfor('/guidecriteria/guidecomments/guidecomment'));

        // Devuelve el arreglo de rutas.
        return $paths;
    }
}
/**
 * Declara las rutas XML de la guía de marcado adjuntas al elemento de instancia del formulario.
 *
 * @return array de {@link restore_path_element}
 */
protected function define_instance_plugin_structure() {

    // Arreglo para almacenar las rutas.
    $paths = array();

    // Ruta para los elementos de llenado de la guía de calificación.
    $paths[] = new restore_path_element('gradinform_guide_filling',
        $this->get_pathfor('/fillings/filling'));

    // Devuelve el arreglo de rutas.
    return $paths;
}

/**
 * Procesa los datos del elemento de criterio.
 *
 * Establece el mapeo 'gradingform_guide_criterion' para ser utilizado más tarde por
 * {@link self::process_gradinform_guide_filling()}
 *
 * @param array|stdClass $data
 */
public function process_gradingform_guide_criterion($data) {
    global $DB;

    // Convierte $data en un objeto.
    $data = (object)$data;
    $oldid = $data->id;
    $data->definitionid = $this->get_new_parentid('grading_definition');

    // Inserta el registro en la tabla 'gradingform_guide_criteria'.
    $newid = $DB->insert_record('gradingform_guide_criteria', $data);

    // Establece el mapeo entre el antiguo y el nuevo ID.
    $this->set_mapping('gradingform_guide_criterion', $oldid, $newid);
}

/**
 * Procesa los datos del elemento de comentarios.
 *
 * @param array|stdClass $data Los datos a insertar como comentario.
 */
public function process_gradingform_guide_comment($data) {
    global $DB;

    // Convierte $data en un objeto.
    $data = (object)$data;
    $data->definitionid = $this->get_new_parentid('grading_definition');

    // Inserta el registro en la tabla 'gradingform_guide_comments'.
    $DB->insert_record('gradingform_guide_comments', $data);
}
/**
 * Procesa los datos del elemento de comentarios en el formato antiguo.
 *
 * @param array|stdClass $data Los datos a insertar como comentario.
 */
public function process_gradingform_guide_comment_legacy($data) {
    global $DB;

    // Convierte $data en un objeto.
    $data = (object)$data;
    $data->definitionid = $this->get_new_parentid('grading_definition');

    // Inserta el registro en la tabla 'gradingform_guide_comments'.
    $DB->insert_record('gradingform_guide_comments', $data);
}

/**
 * Procesa los datos del elemento de llenado.
 *
 * @param array|stdClass $data Los datos a insertar como llenado.
 */
public function process_gradinform_guide_filling($data) {
    global $DB;

    // Convierte $data en un objeto.
    $data = (object)$data;

    // Asigna el nuevo ID de instancia y el ID de criterio utilizando el mapeo.
    $data->instanceid = $this->get_new_parentid('grading_instance');
    $data->criterionid = $this->get_mappingid('gradingform_guide_criterion', $data->criterionid);

    // Inserta el registro en la tabla 'gradingform_guide_fillings'.
    $DB->insert_record('gradingform_guide_fillings', $data);
}
?>