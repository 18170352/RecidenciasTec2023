<?php
/**
 * Este archivo contiene el relleno (polyfill) para permitir que un complemento funcione con Moodle 3.3 en adelante.
 *
 * @package    core_grading
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */

namespace core_grading\privacy;

defined('MOODLE_INTERNAL') || die();  // Se asegura de que este archivo solo sea accesible dentro de Moodle.

/**
 * El trait utilizado para proporcionar compatibilidad con versiones anteriores para complementos de terceros.
 *
 * @package    core_grading
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */
trait gradingform_legacy_polyfill {

    /**
     * Exporta datos de usuario relacionados con un ID de instancia.
     *
     * @param  \context $context Contexto a utilizar con el escritor de exportación.
     * @param  int $instanceid El ID de la instancia para exportar datos.
     * @param  array $subcontext El directorio para exportar estos datos.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
        // Llama al método interno para exportar datos.
        static::_export_gradingform_instance_data($context, $instanceid, $subcontext);
    }

    /**
     * Elimina todos los datos de usuario relacionados con los IDs de instancia proporcionados.
     *
     * @param  array  $instanceids Los IDs de instancia para eliminar información.
     */
    public static function delete_gradingform_for_instances(array $instanceids) {
        // Llama al método interno para eliminar datos.
        static::_delete_gradingform_for_instances($instanceids);
    }
}
