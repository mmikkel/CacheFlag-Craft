$(function () {

    var $form = $('.cacheFlag-form'),
        $submitRequest = null;

    $form.on('submit', onFormSubmit);

    $form.on('click', '[data-emptycache]', clearCaches);

    function onFormSubmit(e) {
        e.preventDefault();
        submitForm();
    }

    function submitForm() {

        if ($submitRequest) {
            $submitRequest.abort();
        }

        $form.addClass('js-submitting');
        $form.find('.spinner').removeClass('hidden');

        $form.find('input[type="submit"]').prop('disabled', true).addClass('disabled');

        $submitRequest = $.ajax($form.attr('action'), {
            data: $form.serialize(),
            type: 'POST',
            success: function (response) {
                if (response.success) {
                    Craft.cp.displayNotice(response.message);
                } else {
                    Craft.cp.displayError(response.message);
                }
                if (response.flags) {
                    for (var key in response.flags) {
                        $form.find('input[data-id="' + key + '"]').val(response.flags[key].flags);
                        $form.find('input[name="cacheflags[' + key + '][flagId]"]').val(response.flags[key].id);
                    }
                }
            },
            error: function (response) {
                if (response.statusText !== 'abort') {
                    Craft.cp.displayError(response.statusText);
                }
            },
            complete: function () {
                delete $submitRequest;
                $form.removeClass('js-submitting');
                $form.find('.spinner').addClass('hidden');
                $form.find('input[type="submit"]').prop('disabled', false).removeClass('disabled');
            }
        });

    }

    function clearCaches(e)
    {
        e.preventDefault();

        var actionUrl = Craft.getActionUrl('cacheFlag/clearCachesByFlags'),
            $target = $(e.currentTarget),
            flags = $target.data('emptycache');

        if ($target.hasClass('disabled') || !flags || flags == '') return false;
        
        var data = {
            flags : flags
        };
        
        data[$form.data('csrf-name')] = $form.data('csrf-token'); 

        $.ajax(actionUrl, {
            type : 'POST',
            data : data,
            success: function (response) {
                if (response.success) {
                    Craft.cp.displayNotice(response.message);
                } else {
                    Craft.cp.displayError(response.message);
                }
            },
            error: function (response) {
                if (response.statusText !== 'abort') {
                    Craft.cp.displayError(response.statusText);
                }
            },
            complete: function () {
                $target.addClass('disabled');
            }
        });

    }

});
