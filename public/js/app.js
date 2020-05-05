jQuery(function ($) {
    $('[data-toggle="tooltip"]').tooltip();

    // Delete
    $(document).on('submit', '.js-delete',  function (e) {
        var route = $(this).attr('action'),
            $parentRow = $(this).parents('tr'),
            token = $(this).children('input[name="_token"]').val();

        $.ajax({
            url: route,
            type: 'DELETE',
            data: {'_token': token},
            success: function(result) {
                Swal.fire('Success', result.message, 'success');
                $parentRow.fadeOut();
            },
            error: function() {
                Swal.fire('Eroare', 'Ne pare rău, dar nu am putut șterge.', 'error');
            }
        });

        e.preventDefault();
    })

    // Show form on edit
    $(document).on('click', '.js-edit',  function (e) {
        var route = $(this).attr('href'),
            $parentRow = $(this).parents('tr');

        $.get(route, function(data) {
            $parentRow.replaceWith(data).fadeIn();
        });

        e.preventDefault();
    })

    // Submit edit form
    $(document).on('click', '.js-save',  function (e) {
        var route = $(this).data('href'),
            $parentRow = $(this).parents('tr'),
            $form = $(this).closest('form');

        $.post(route, $form.serialize(), function(html) {
            $form.fadeOut(300, function() {
                $parentRow.hide().replaceWith(html).fadeIn(400);
            });
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
        $('#js-change-day-date').val($changeDayPicker.datepicker('getFormattedDate'));
        $('#js-change-day-form').submit();
    });
});
