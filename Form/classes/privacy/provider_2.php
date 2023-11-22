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
 * Privacy class for requesting user data.
 *
 * @package    gradingform_guide
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 <?php
 namespace gradingform_guide\privacy;
 
 // Asegurarse de que el script no se ejecute directamente desde el navegador.
 defined('MOODLE_INTERNAL') || die();
 
 // Importar las clases necesarias para la gestión de la privacidad.
 use \core_privacy\local\metadata\collection;
 use \core_privacy\local\request\transform;
 use \core_privacy\local\request\writer;
 
 /**
 * Privacy class for requesting user data.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
\core_privacy\local\metadata\provider,
\core_grading\privacy\gradingform_provider_v2,
\core_privacy\local\request\user_preference_provider {

/**
* Return the fields which contain personal data.
*
* @param   collection $collection The initialised collection to add items to.
* @return  collection A listing of user data stored through this system.
*/

/**
 * Clase que gestiona la privacidad de los datos relacionados con el plugin.
 */
class privacy implements \core_privacy\local\metadata\provider {

    /**
     * Obtiene los metadatos de privacidad para el plugin.
     *
     * @param collection $collection La colección de metadatos de privacidad.
     * @return collection La colección de metadatos de privacidad actualizada.
     */
    public static function get_metadata(collection $collection): collection {
        // Agregar metadatos relacionados con la tabla de la base de datos 'gradingform_guide_fillings'.
        $collection->add_database_table('gradingform_guide_fillings', [
            'instanceid' => 'privacy:metadata:instanceid',
            'criterionid' => 'privacy:metadata:criterionid',
            'remark' => 'privacy:metadata:remark',
            'score' => 'privacy:metadata:score'
        ], 'privacy:metadata:fillingssummary');

        // Agregar preferencias del usuario relacionadas con el plugin.
        $collection->add_user_preference(
            'gradingform_guide-showmarkerdesc',
            'privacy:metadata:preference:showmarkerdesc'
        );
        $collection->add_user_preference(
            'gradingform_guide-showstudentdesc',
            'privacy:metadata:preference:showstudentdesc'
        );

        // Devolver la colección actualizada.
        return $collection;
    }
}
/**
 * Clase que gestiona la exportación y eliminación de datos relacionados con el plugin.
 */
class privacy implements \core_privacy\local\legacy_polyfill {

    /**
     * Exporta datos de usuario relacionados con un ID de instancia.
     *
     * @param \context $context El contexto a utilizar con el escritor de exportación.
     * @param int $instanceid El ID de instancia para exportar datos.
     * @param array $subcontext El directorio para exportar estos datos.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
        global $DB;

        // Obtener registros de los parámetros proporcionados.
        $params = ['instanceid' => $instanceid];
        $sql = "SELECT gc.shortname, gc.description, gc.maxscore, gf.score, gf.remark
                  FROM {gradingform_guide_fillings} gf
                  JOIN {gradingform_guide_criteria} gc ON gc.id = gf.criterionid
                 WHERE gf.instanceid = :instanceid";
        $records = $DB->get_records_sql($sql, $params);

        if ($records) {
            $subcontext = array_merge($subcontext, [get_string('guide', 'gradingform_guide'), $instanceid]);
            writer::with_context($context)->export_data($subcontext, (object) $records);
        }
    }

    /**
     * Elimina todos los datos de usuario relacionados con los ID de instancia proporcionados.
     *
     * @param array $instanceids Los ID de instancia para eliminar información.
     */
    public static function delete_gradingform_for_instances(array $instanceids) {
        global $DB;
        $DB->delete_records_list('gradingform_guide_fillings', 'instanceid', $instanceids);
    }

    /**
     * Almacena todas las preferencias del usuario para el plugin.
     *
     * @param int $userid El ID de usuario cuyos datos se exportarán.
     */
    public static function export_user_preferences(int $userid) {
        // Exportar preferencia 'gradingform_guide-showmarkerdesc'.
        $prefvalue = get_user_preferences('gradingform_guide-showmarkerdesc', null, $userid);
        if ($prefvalue !== null) {
            $transformedvalue = transform::yesno($prefvalue);
            writer::export_user_preference(
                'gradingform_guide',
                'gradingform_guide-showmarkerdesc',
                $transformedvalue,
                get_string('privacy:metadata:preference:showmarkerdesc', 'gradingform_guide')
            );
        }

        // Exportar preferencia 'gradingform_guide-showstudentdesc'.
        $prefvalue = get_user_preferences('gradingform_guide-showstudentdesc', null, $userid);
        if ($prefvalue !== null) {
            $transformedvalue = transform::yesno($prefvalue);
            writer::export_user_preference(
                'gradingform_guide',
                'gradingform_guide-showstudentdesc',
                $transformedvalue,
                get_string('privacy:metadata:preference:showstudentdesc', 'gradingform_guide')
            );
        }
    }
}