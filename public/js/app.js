jQuery(function ($) {
    $('[data-toggle="tooltip"]').tooltip();

    // Delete
    $(document).on('submit', '.js-delete',  function (e) {
        if (confirm('Ești sigur că vrei să ștergi?')) {
            var route = $(this).attr('action'),
                $parentRow = $(this).parents('tr'),
                token = $(this).children('input[name="_token"]').val();

            $.ajax({
                url: route,
                type: 'DELETE',
                data: {'_token': token},
                success: function (result) {
                    Swal.fire('Success', result.message, 'success');
                    $parentRow.fadeOut();
                },
                error: function () {
                    Swal.fire('Eroare', 'Ne pare rău, dar nu am putut șterge.', 'error');
                }
            });
        }

        e.preventDefault();
    })

    // Show edit form
    $(document).on('click', '.js-edit',  function (e) {
        var route = $(this).attr('href'),
            $parentRow = $(this).parents('tr');

        $.get(route, function(data) {
            $parentRow.hide().after(data);
        });

        e.preventDefault();
    })

    // Save edit/new form
    $(document).on('click', '.js-save',  function (e) {
        var route = $(this).data('href'),
            $parentRow = $(this).parents('tr'),
            $form = $(this).closest('form'),
            mode = $(this).data('mode');

        $.post(route, $form.serialize(), function(html) {
            $form.fadeOut(300, function() {
                if(mode === 'edit') {
                    $parentRow.parent().find('tr:hidden').remove();
                }

                $parentRow.replaceWith(html).fadeIn(400);
            });
        });

        e.preventDefault();
    })

    // Cancel edit form
    $(document).on('click', '.js-close',  function () {
        var $parentRow = $(this).parents('tr'),
            mode = $(this).data('mode');

        if(mode === 'edit') $parentRow.prev().fadeIn(); // revert previous hidden row

        $parentRow.remove();
    })

    // Show add form
    $(document).on('click', '.js-add',  function (e) {
        var route = $(this).attr('href'),
            $lastRow = $(this).parents('.card').find('table tr:last');

        $.get(route, function(data) {
            $lastRow.after(data);
        });

        e.preventDefault();
    })

    var $changeDayPicker = $('#js-change-day-datepicker');

    $changeDayPicker.datepicker({
        format: 'yyyy-mm-dd',
        language: 'ro',
        weekStart: 0,
        todayHighlight: true,
        toggleActive: true,
        autoclose: true
    }).on('changeDate', function(e) {
        var formattedDate = $changeDayPicker.datepicker('getFormattedDate');

        if(formattedDate.length !== 0) {
            $('#js-change-day-date').val($changeDayPicker.datepicker('getFormattedDate'));
            $('#js-change-day-form').submit();
        }
    });
});
