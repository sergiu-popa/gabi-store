jQuery(function ($) {
    $('[data-toggle="tooltip"]').tooltip();

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

    $(document).on('click', '.js-edit',  function (e) {
        var route = $(this).attr('href'),
            $parentRow = $(this).parents('tr');

        $.get(route, function(data) {
            $parentRow.html(data).fadeIn();
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
