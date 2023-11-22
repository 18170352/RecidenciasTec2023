// Importa los módulos necesarios
define("gradingform_guide/grades/grader/gradingpanel/comments", ["exports", "./comments/selectors"], function (_exports, _selectors) {
    var obj;
  
    /**
     * Grading panel frequently used comments selector.
     *
     * @module gradingform_guide/grades/grader/gradingpanel/comments
     * @copyright 2019 Andrew Nicols <andrew@nicols.co.uk>
     * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    Object.defineProperty(_exports, "__esModule", { value: true });
  
    // Importa el módulo de selectores
    _selectors = (obj = _selectors) && obj.__esModule ? obj : { default: obj };
  
    // Exporta la función de inicialización
    _exports.init = function (rootId) {
      // Agrega un evento de escucha al elemento con el ID proporcionado
      document.querySelector("#" + rootId).addEventListener("click", function (e) {
        if (!e.target.matches(_selectors.default.frequentComment)) return;
        e.preventDefault();
  
        // Encuentra el elemento más cercano correspondiente al comentario frecuente
        const clicked = e.target.closest(_selectors.default.frequentComment);
        const remark = clicked.closest(_selectors.default.criterion).querySelector(_selectors.default.remark);
  
        if (remark) {
          // Si existe el elemento de observación, agrega el comentario al valor del elemento
          remark.value = remark.value.trim() ? remark.value + "\n" + clicked.innerHTML : remark.value + clicked.innerHTML;
        }
      });
    };
  });
  