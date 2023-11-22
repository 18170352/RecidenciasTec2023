var pseudotablink = '<span class="pseudotablink" tabindex="0"></span>',
taplain = ta.next('.plainvalue'),
tbplain = null,
tb = el.one('.score input[type=text]');

// add 'plainvalue' next to textarea for description/definition and next to input text field for score (if applicable)
if (!taplain && ta.get('name') != '') {
ta.insert('<div class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></div>', 'after');
taplain = ta.next('.plainvalue');
taplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere);
if (tb) {
    tb.get('parentNode').append('<span class="plainvalue">'+pseudotablink+'<span class="textvalue">&nbsp;</span></span>');
    tbplain = tb.get('parentNode').one('.plainvalue');
    tbplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere);
}
}

if (tb && !tbplain) {
tbplain = tb.get('parentNode').one('.plainvalue');
}

if (!editmode) {
// if we need to hide the input fields, copy their contents to plainvalue(s). If description/definition
// is empty, display the default text ('Click to edit ...') and add/remove 'empty' CSS class to element
var value = Y.Lang.trim(ta.get('value'));
if (value.length) {
    taplain.removeClass('empty');
} else if (ta.get('name').indexOf('[shortname]') > 1){
    value = M.util.get_string('clicktoeditname', 'gradingform_guide');
    taplain.addClass('editname');
} else {
    value = M.util.get_string('clicktoedit', 'gradingform_guide');
    taplain.addClass('empty');
}

// Replace newlines with <br> tags when displaying in the page.
taplain.one('.textvalue').set('innerHTML', Y.Escape.html(value).replace(/(?:\r\n|\r|\n)/g, '<br>'));

if (tb) {
    tbplain.one('.textvalue').set('innerHTML', Y.Escape.html(tb.get('value')));
}

// hide/display textarea, textbox, and plaintexts
taplain.removeClass('hiddenelement');
ta.addClass('hiddenelement');
if (tb) {
    tbplain.removeClass('hiddenelement');
    tb.addClass('hiddenelement');
}
} else {
// if we need to show the input fields, set the width/height for textarea so it fills the cell
try {
    if (ta.get('name').indexOf('[maxscore]') > 1) {
        ta.setStyle('width', '25px');
    } else {
        var width = parseFloat(ta.get('parentNode').getComputedStyle('width')) - 10,
            height = parseFloat(ta.get('parentNode').getComputedStyle('height'));
        ta.setStyle('width', Math.max(width, 50) + 'px');
        ta.setStyle('height', Math.max(height, 30) + 'px');
    }
} catch (e) {
    // IE doesn't like reading styles in this way. Do nothing in case of an error.
}

// ...continuación del bloque anterior

// set focus on the textarea or textbox
if (ta.get('tagName') == 'TEXTAREA') {
    ta.focus();
} else if (tb) {
    tb.focus();
}
}


var pseudotablink = '<span class="pseudotablink" tabindex="0"></span>',
    taplain = ta.next('.plainvalue'),
    tbplain = null,
    tb = el.one('.score input[type=text]');

// Agrega elementos "plainvalue" al textarea y al campo de entrada de texto.
if (!taplain && ta.get('name') !== '') {
    ta.insert('<div class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></div>', 'after');
    taplain = ta.next('.plainvalue');
    taplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere);

    if (tb) {
        tb.get('parentNode').append('<span class="plainvalue">' + pseudotablink + '<span class="textvalue">&nbsp;</span></span>');
        tbplain = tb.get('parentNode').one('.plainvalue');
        tbplain.one('.pseudotablink').on('focus', M.gradingform_guideeditor.clickanywhere);
    }
}

if (tb && !tbplain) {
    tbplain = tb.get('parentNode').one('.plainvalue');
}

// Copia el contenido a los elementos "plainvalue".
if (!editmode) {
    var value = Y.Lang.trim(ta.get('value'));

    if (value.length) {
        taplain.removeClass('empty');
    } else if (ta.get('name').indexOf('[shortname]') > 1) {
        value = M.util.get_string('clicktoeditname', 'gradingform_guide');
        taplain.addClass('editname');
    } else {
        value = M.util.get_string('clicktoedit', 'gradingform_guide');
        taplain.addClass('empty');
    }

    taplain.one('.textvalue').set('innerHTML', Y.Escape.html(value).replace(/(?:\r\n|\r|\n)/g, '<br>'));

    if (tb) {
        tbplain.one('.textvalue').set('innerHTML', Y.Escape.html(tb.get('value')));
    }

    taplain.removeClass('hiddenelement');
    ta.addClass('hiddenelement');

    if (tb) {
        tbplain.removeClass('hiddenelement');
        tb.addClass('hiddenelement');
    }
} else {
    try {
        if (ta.get('name').indexOf('[maxscore]') > 1) {
            ta.setStyle('width', '25px');
        } else {
            var width = parseFloat(ta.get('parentNode').getComputedStyle('width')) - 10,
                height = parseFloat(ta.get('parentNode').getComputedStyle('height'));

            ta.setStyle('width', Math.max(width, 50) + 'px');
            ta.setStyle('height', Math.max(height, 30) + 'px');
        }
    } catch (err) {
        // El navegador no admite 'computedStyle', deja el tamaño predeterminado del cuadro de texto.
    }

    taplain.addClass('hiddenelement');
    ta.removeClass('hiddenelement');

    if (tb) {
        tbplain.addClass('hiddenelement');
        tb.removeClass('hiddenelement');
    }

    // Enfoca el textarea en modo de edición.
    if (editmode) {
        ta.focus();
    }
}

// Manejador para hacer clic en los botones de envío dentro del elemento guideeditor.
// Agrega/elimina/reordena criterios/comentarios en el lado del cliente.
M.gradingform_guideeditor.buttonclick = function(e, confirmed) {
    var Y = M.gradingform_guideeditor.Y;
    var name = M.gradingform_guideeditor.name;

    // Verifica si el evento proviene de un botón de envío.
    if (e.target.get('type') !== 'submit') {
        return;
    }

    // Deshabilita todos los editores.
    M.gradingform_guideeditor.disablealleditors();

    // Divide el ID del botón de envío para obtener información sobre la acción.
    var chunks = e.target.get('id').split('-');
    var section = chunks[1];
    var action = chunks[chunks.length - 1];

    // Verifica si el ID del botón es válido para el elemento guideeditor.
    if (chunks[0] !== name || (section !== 'criteria' && section !== 'comments')) {
        return;
    }

    // Prepara el ID del nuevo criterio insertado.
    var elements_str;
    if (section === 'criteria') {
        elements_str = '#guide-' + name + ' .criteria .criterion';
    } else if (section === 'comments') {
        elements_str = '#guide-' + name + ' .comments .criterion';
    }

    var newid = 0;

    // Calcula el nuevo ID para el nuevo criterio o comentario.
    if (action === 'addcriterion' || action === 'addcomment') {
        newid = M.gradingform_guideeditor.calculatenewid(elements_str);
    }

    if (chunks.length === 3 && (action === 'addcriterion' || action === 'addcomment')) {
        // AGREGAR NUEVO CRITERIO O COMENTARIO
        var parentel = Y.one('#' + name + '-' + section);

        if (parentel.one('>tbody')) {
            parentel = parentel.one('>tbody');
        }

        if (section === 'criteria') {
            var newcriterion = M.gradingform_guideeditor.templates[name]['criterion'];
            parentel.append(newcriterion.replace(/\{CRITERION-id\}/g, 'NEWID' + newid).replace(/\{.+?\}/g, ''));
        } else if (section === 'comments') {
            var newcomment = M.gradingform_guideeditor.templates[name]['comment'];
            parentel.append(newcomment.replace(/\{COMMENT-id\}/g, 'NEWID' + newid).replace(/\{.+?\}/g, ''));
        }

        // Agrega manejadores a los nuevos elementos.
        M.gradingform_guideeditor.addhandlers();
        M.gradingform_guideeditor.disablealleditors();
        M.gradingform_guideeditor.assignclasses(elements_str);

        // Habilita el modo de edición para la entrada recién agregada.
        var inputTarget = 'shortname';
        if (action === 'addcomment') {
            inputTarget = 'description';
        }
        var inputTargetId = '#guide-' + name + ' #' + name + '-' + section + '-NEWID' + newid + '-' + inputTarget;
        M.gradingform_guideeditor.editmode(Y.one(inputTargetId), true);
    } else if (chunks.length === 4 && action === 'moveup') {
        // MOVER ARRIBA
        el = Y.one('#' + name + '-' + section + '-' + chunks[2]);

        if (el.previous()) {
            el.get('parentNode').insertBefore(el, el.previous());
        }

        M.gradingform_guideeditor.assignclasses(elements_str);
    } else if (chunks.length === 4 && action === 'movedown') {
        // MOVER ABAJO
        el = Y.one('#' + name + '-' + section + '-' + chunks[2]);

        if (el.next()) {
            el.get('parentNode').insertBefore(el.next(), el);
        }

        M.gradingform_guideeditor.assignclasses(elements_str);
    } else if (chunks.length === 4 && action === 'delete') {
        // ELIMINAR
        if (confirmed) {
            Y.one('#' + name + '-' + section + '-' + chunks[2]).remove();
            M.gradingform_guideeditor.assignclasses(elements_str);
        } else {
            // Muestra un mensaje de confirmación antes de eliminar.
            require(['core/notification', 'core/str'], function (Notification, Str) {
                Notification.saveCancelPromise(
                    Str.get_string('confirmation', 'admin'),
                    Str.get_string('confirmdeletecriterion', 'gradingform_guide'),
                    Str.get_string('yes', 'moodle')
                ).then(function () {
                    M.gradingform_guideeditor.buttonclick.apply(this, [e, true]);
                    return;
                }.bind(this)).catch(function () {
                    // El usuario canceló.
                });
            }.bind(this));
        }
    } else {
        // Acción desconocida
        return;
    }

    // Previene el comportamiento predeterminado del evento.
    e.preventDefault();
};

// Establece adecuadamente las clases (first/last/odd/even) y/o el orden de clasificación del criterio para los elementos Y.all(elements_str).
M.gradingform_guideeditor.assignclasses = function (elements_str) {
    var elements = M.gradingform_guideeditor.Y.all(elements_str);

    for (var i = 0; i < elements.size(); i++) {
        elements.item(i).removeClass('first').removeClass('last').removeClass('even').removeClass('odd').
            addClass(((i % 2) ? 'odd' : 'even') + ((i == 0) ? ' first' : '') + ((i == elements.size() - 1) ? ' last' : ''));

        // Establece el valor del orden de clasificación del criterio en los elementos ocultos.
        elements.item(i).all('input[type=hidden]').each(
            function (node) {
                if (node.get('name').match(/sortorder/)) {
                    node.set('value', i);
                }
            }
        );
    }
};

// Devuelve un ID único para el próximo elemento agregado.
// Este ID no debe ser igual a ninguno de los IDs en Y.all(elements_str).
M.gradingform_guideeditor.calculatenewid = function (elements_str) {
    var newid = 1;

    M.gradingform_guideeditor.Y.all(elements_str).each(function (node) {
        var idchunks = node.get('id').split('-'), id = idchunks.pop();
        
        // Verifica si el ID coincide con el patrón NEWID(\d+).
        if (id.match(/^NEWID(\d+)$/)) {
            newid = Math.max(newid, parseInt(id.substring(5)) + 1);
        }
    });

    return newid;
};
