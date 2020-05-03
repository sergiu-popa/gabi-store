jQuery(function ($) {
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
