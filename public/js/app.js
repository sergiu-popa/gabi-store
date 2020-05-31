jQuery(function ($) {
    $('[data-toggle="tooltip"]').tooltip();

    // Inline form: Delete
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

    // Inline form: Show edit form
    $(document).on('click', '.js-edit',  function (e) {
        var route = $(this).attr('href'),
            $parentRow = $(this).parents('tr'),
            bgColor = $parentRow.css('backgroundColor');

        $.get(route, function(data) {
            $parentRow.hide().after(data);
            $parentRow.next()
                .css('backgroundColor', bgColor)
                .find('.js-selectize').selectize();
        });

        e.preventDefault();
    })

    // Inline form: send edit/new form
    $(document).on('click', '.js-save',  function (e) {
        var route = $(this).data('href'),
            $parentRow = $(this).parents('tr'),
            $form = $(this).closest('form'),
            mode = $(this).data('mode'),
            refresh = $(this).data('refresh'),
            formIsValid = false;

        $.post(route, $form.serialize(), function(html) {
            $form.fadeOut(300, function() {
                if(mode === 'edit') {
                    $parentRow.parent().find('tr:hidden').remove();
                }

                $parentRow.replaceWith(html).fadeIn(400).find('.js-selectize').selectize();
            });

            $parentRow.siblings('.js-empty').remove();
            formIsValid = (html.indexOf('invalid-feedback') === -1);
        }).done(function() {
            if (refresh && formIsValid) {
                Swal.fire('Success', 'Intrarea a fost adăugată. Apasă OK să reîncarci pagina.', 'success')
                    .then(function() {
                        location.reload();
                    });
            } // merchandise global from refresh after send
        });

        e.preventDefault();
    })

    // Inline form: Cancel edit form
    $(document).on('click', '.js-close',  function () {
        var $parentRow = $(this).parents('tr'),
            mode = $(this).data('mode');

        if(mode === 'edit') $parentRow.prev().fadeIn(); // revert previous hidden row

        $parentRow.remove();
    })

    // Inline form: Show add form
    $(document).on('click', '.js-add',  function (e) {
        var route = $(this).attr('href'),
            $lastRow = $(this).parents('.card').find('table.js-main > tbody > tr:last');

        if($lastRow.hasClass('total')) $lastRow = $lastRow.prev();

        $.get(route, function(data) {
            $lastRow.after(data).next().find('.js-selectize').selectize();
        });

        e.preventDefault();
    })

    // Merchandise global form with provider
    $(document).on('click', '.js-add-merchandise',  function (e) {
        var $wrapper = $('#merchandiseForm');
        $.get($(this).data('route'), function(data) {
            $wrapper.find('.card-body').html(data).find('.js-selectize').selectize();
            $wrapper.slideDown();
        });
    });

    // Merchandise price calculation with VAT
    $(document).on('change', '.js-merchandise-vat', function() {
        var $selectInput = $(this),
            $enterPriceInput = $(this).parents('tr').prev().find('.js-merchandise-enter-price');

        if($enterPriceInput.val().length > 0) {
            var enterPrice = parseFloat($enterPriceInput.val()),
                priceWithVAT = enterPrice * ($(this).val() / 100) + enterPrice,
                recommendedPrice = priceWithVAT * 1.30;

            $enterPriceInput.val(priceWithVAT.toFixed(2));
            $selectInput.parents('td').next().find('.js-recommended-price').text(recommendedPrice.toFixed(2));
        }
    })

    // Merchandise exit price calculation with profit
    $(document).on('change', '.js-merchandise-enter-price', function() {
        var enterPrice = parseFloat($(this).val()),
            recommendedPrice = enterPrice * 1.30,
            $nextRow = $(this).parents('tr').next(),
            $recommendedPriceWrapper = $nextRow.find('.js-recommended-price'),
            vat = $nextRow.find('.js-merchandise-vat').val();

        if (vat.length > 0) {
            var priceWithVAT = enterPrice * ($(this).val() / 100) + enterPrice;
            recommendedPrice = priceWithVAT * 1.30;
        }

        $recommendedPriceWrapper.text(recommendedPrice.toFixed(2));
    })

    // Merchandise paidWith sweet alert
    $(document).on('click', '#merchandise_paidWith input', function(e) {
        var paidWith = $(this).val(),
            message = 'Se va genera/actualiza <strong>datoria</strong>.'

        switch (paidWith) {
            case 'bill':
                message = 'Se va genera o plată nouă de tip <strong>bon</strong>.';
                break;
            case 'invoice':
                message = 'Se va genera o plată nouă de tip <strong>factură</strong>.';
                break;
        }

        Swal.fire({
            title: 'Atenție',
            html: message,
            timer: 3000,
            icon: 'warning'
        });
    })

    // Review button
    $(document).on('click', '.js-review',  function (e) {
        var $card = $(this).parents('.card');

        if (confirm('Ești sigur că este totul corect?')) {
            $card.find('table').remove();
            $card.find('.card-footer').remove();
            $card.addClass('text-white bg-success');
            $card.removeClass('js-unverified');

            if($('.card.js-unverified').length === 0) {
                $('.js-day-action').removeAttr('disabled');
            }
        }

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
