<?php
/**
 * Este archivo es parte de Moodle - http://moodle.org/
 *
 * Moodle es un software libre: puedes redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General de GNU, según lo publicado por
 * la Fundación de Software Libre, ya sea la versión 3 de la licencia o, a su elección,
 * cualquier versión posterior.
 *
 * Moodle se distribuye con la esperanza de que sea útil,
 * pero SIN GARANTÍA ALGUNA; incluso sin la garantía implícita de
 * COMERCIABILIDAD o IDONEIDAD PARA UN PROPÓSITO PARTICULAR. Consulta la
 * Licencia Pública General de GNU para obtener más detalles.
 *
 * Deberías haber recibido una copia de la Licencia Pública General de GNU
 * junto con Moodle. Si no la has recibido, consulta <http://www.gnu.org/licenses/>.
 */

/**
 * Clase de privacidad para solicitar datos de usuario.
 *
 * @package    core_grading
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 o posterior
 */
namespace core_grading\privacy;

defined('MOODLE_INTERNAL') || die();  // Asegura que este archivo solo sea accesible dentro de Moodle.

interface gradingform_provider_v2 extends
    \core_privacy\local\request\plugin\subsystem_provider,  // Interfaz para proporcionar datos del subsistema.
    \core_privacy\local\request\shared_userlist_provider  // Interfaz para proporcionar datos compartidos del usuario.
{

    /**
     * Exportar datos de usuario relacionados con un ID de instancia.
     *
     * @param  \context $context Contexto a utilizar con el escritor de exportación.
     * @param  int $instanceid El ID de instancia para exportar datos.
     * @param  array $subcontext El directorio para exportar estos datos.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext);

    /**
     * Eliminar todos los datos de usuario relacionados con los IDs de instancia proporcionados.
     *
     * @param  array  $instanceids Los IDs de instancia para eliminar información.
     */
    public static function delete_gradingform_for_instances(array $instanceids);
}
