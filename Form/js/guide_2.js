M.gradingform_guide = {};

/**
 * Esta función se llama para cada guía en la página.
 */
M.gradingform_guide.init = function(Y, options) {
    // Elemento actualmente enfocado.
    var currentfocus = Y.one('.markingguideremark');

    // Manejadores de eventos para los elementos .markingguideremark.
    Y.all('.markingguideremark').on('blur', function(e) {
        currentfocus = e.currentTarget;
    });

    // Manejadores de eventos para los elementos .markingguidecomment.
    Y.all('.markingguidecomment').on('click', function(e) {
        currentfocus.set('value', currentfocus.get('value') + '\n' + e.currentTarget.get('text'));
        currentfocus.focus();
    });

    // Manejadores de eventos para los elementos de radio en .showmarkerdesc.
    Y.all('.showmarkerdesc input[type=radio]').on('click', function(e) {
        if (e.currentTarget.get('value') == 'false') {
            Y.all('.criteriondescriptionmarkers').addClass('hide');
        } else {
            Y.all('.criteriondescriptionmarkers').removeClass('hide');
        }
    });

    // Manejadores de eventos para los elementos de radio en .showstudentdesc.
    Y.all('.showstudentdesc input[type=radio]').on('click', function(e) {
        if (e.currentTarget.get('value') == 'false') {
            Y.all('.criteriondescription').addClass('hide');
        } else {
            Y.all('.criteriondescription').removeClass('hide');
        }
    });
};
