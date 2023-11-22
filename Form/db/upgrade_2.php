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
 * This file keeps track of upgrades to the marking guide grading method.
 *
 * @package   gradingform_guide
 * @category  upgrade
 * @copyright 2016 Jun Pataleta
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 // Asegurarse de que el script no se ejecute directamente desde el navegador.
defined('MOODLE_INTERNAL') || die();

/**
 * Tarea de actualización del método de calificación de la guía de calificación.
 *
 * @param int $oldversion La versión desde la cual estamos actualizando.
 * @return bool Devuelve true en caso de éxito.
 * @throws coding_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_gradingform_guide_upgrade($oldversion) {
    global $DB;

    // Línea de actualización generada automáticamente para la versión de lanzamiento Moodle v3.9.0.
    // Coloca cualquier paso de actualización después de esto.

    // Línea de actualización generada automáticamente para la versión de lanzamiento Moodle v4.0.0.
    // Coloca cualquier paso de actualización después de esto.

    // Línea de actualización generada automáticamente para la versión de lanzamiento Moodle v4.1.0.
    // Coloca cualquier paso de actualización después de esto.

    // Línea de actualización generada automáticamente para la versión de lanzamiento Moodle v4.2.0.
    // Coloca cualquier paso de actualización después de esto.

    return true;
}