// Importa los módulos necesarios
define("gradingform_guide/grades/grader/gradingpanel", [
    "exports",
    "core/ajax",
    "core_grades/grades/grader/gradingpanel/normalise",
    "core_grades/grades/grader/gradingpanel/comparison",
    "jquery"
  ], function (_exports, _ajax, _normalise, _comparison, _jquery) {
    var obj;
  
    /**
     * Grading panel for gradingform_guide.
     *
     * @module gradingform_guide/grades/grader/gradingpanel
     * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    Object.defineProperty(_exports, "__esModule", { value: true });
  
    // Importa el módulo jQuery
    _jquery = (obj = _jquery) && obj.__esModule ? obj : { default: obj };
  
    // Función para obtener la calificación actual
    _exports.fetchCurrentGrade = (component, contextid, itemname, gradeduserid) => {
      return (0, _ajax.call)([
        {
          methodname: "gradingform_guide_grader_gradingpanel_fetch",
          args: {
            component: component,
            contextid: contextid,
            itemname: itemname,
            gradeduserid: gradeduserid
          }
        }
      ])[0];
    };
  
    // Función para almacenar la calificación actual
    _exports.storeCurrentGrade = async (component, contextid, itemname, gradeduserid, notifyUser, rootNode) => {
      const form = rootNode.querySelector("form");
  
      // Compara los datos del formulario
      if (!(0, _comparison.compareData)(form)) {
        return (0, _normalise.normaliseResult)(await (0, _ajax.call)([
          {
            methodname: "gradingform_guide_grader_gradingpanel_store",
            args: {
              component: component,
              contextid: contextid,
              itemname: itemname,
              gradeduserid: gradeduserid,
              notifyuser: notifyUser,
              formdata: _jquery.default(form).serialize()
            }
          }
        ])[0]);
      } else {
        return "";
      }
    };
  });
  