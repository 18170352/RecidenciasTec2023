define(['jquery', 'core/templates', 'core/notification'], function($, templates, notification) {

    // Función para mostrar el diálogo del selector de comentarios.
    function displayChooserDialog(compiledSource, comments, criterionId, remarkId) {
        // Crea una etiqueta de título para el diálogo.
        const titleLabel = '<label>' + M.util.get_string('insertcomment', 'gradingform_guide') + '</label>';

        // Crea un botón de cancelar.
        const cancelButtonId = 'comment-chooser-' + criterionId + '-cancel';
        const cancelButton = '<button id="' + cancelButtonId + '">' + M.util.get_string('cancel', 'moodle') + '</button>';

        // Crea una instancia del diálogo de Moodle.
        const chooserDialog = new M.core.dialogue({
            modal: true,
            headerContent: titleLabel,
            bodyContent: compiledSource,
            footerContent: cancelButton,
            focusAfterHide: '#' + remarkId,
            id: "comments-chooser-dialog-" + criterionId
        });

        // Asocia un evento de clic al botón de cancelar para cerrar el diálogo.
        $("#" + cancelButtonId).click(function() {
            chooserDialog.hide();
        });

        // Recorre cada comentario y asocia eventos de clic y teclado a las opciones del comentario.
        $.each(comments, function(index, comment) {
            const commentOptionId = '#comment-option-' + criterionId + '-' + comment.id;

            // Asocia un evento de clic a la opción de comentario.
            $(commentOptionId).click(function() {
                const remarkTextArea = $('#' + remarkId);
                const remarkText = remarkTextArea.val();

                if (remarkText.trim() !== '') {
                    remarkText += '\n';
                }
                remarkText += comment.description;

                remarkTextArea.val(remarkText);

                chooserDialog.hide();
            });

            // Asocia un evento de teclado a las opciones de comentario.
            $(document).off('keypress', commentOptionId).on('keypress', commentOptionId, function(event) {
                const keyCode = event.which || event.keyCode;

                if (keyCode === 13 || keyCode === 32) {
                    $(commentOptionId).click();
                }
            });
        });

        // Destruye el diálogo cuando se oculta para permitir su recarga.
        chooserDialog.after('visibleChange', function(e) {
            if (e.prevVal && !e.newVal) {
                this.destroy();
            }
        }, chooserDialog);

        // Muestra el diálogo.
        chooserDialog.show();
    }

    // Función para generar el diálogo del selector de comentarios a partir de una plantilla.
    function generateCommentsChooser(criterionId, commentOptions, remarkId) {
        const context = {
            criterionId: criterionId,
            comments: commentOptions
        };

        // Renderiza la plantilla y muestra el diálogo del selector de comentarios.
        templates.render('gradingform_guide/comment_chooser', context)
            .done(function(compiledSource) {
                displayChooserDialog(compiledSource, commentOptions, criterionId, remarkId);
            })
            .fail(function(error) {
                notification.exception(error);
            });
    }

    // Devuelve un objeto que contiene la función 'initialise' para inicializar el módulo.
    return {
        initialise: function(criterionId, insertCommentButtonId, remarkId, commentOptions) {
            // Asocia un evento de clic al botón de inserción de comentarios.
            $("#" + insertCommentButtonId).click(function(e) {
                e.preventDefault();
                generateCommentsChooser(criterionId, commentOptions, remarkId);
            });
        }
    };
});
