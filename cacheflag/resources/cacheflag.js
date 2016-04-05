$(function () {

    var $form = $('.cacheFlag-form'),
        $request = null;

    $form.on('submit', onFormSubmit);

    function onFormSubmit(e) {
        e.preventDefault();
        submitForm();
    }

    function submitForm()
    {

        if ($request) {
            $request.abort();
        }

        $form.addClass('js-submitting');
        $form.find('.spinner').removeClass('hidden');

        $form.find('input[type="submit"]').prop('disabled', true).addClass('disabled');

        $request = $.ajax($form.attr('action'), {
            data : $form.serialize(),
            type : 'POST',
            success : function (response) {
                if (response.success) {
                    Craft.cp.displayNotice(response.message);
                } else {
                    Craft.cp.displayError(response.message);
                }
                if (response.flags) {
                    for (var key in response.flags) {
                        $form.find('input[data-id="'+key+'"]').val(response.flags[key].flags);
                        $form.find('input[name="cacheflags['+key+'][flagId]"]').val(response.flags[key].id);
                    }
                }
            },
            error : function (response) {
                if (response.statusText !== 'abort') {
                    Craft.cp.displayError(response.statusText);
                }
            },
            complete : function () {
                delete $request;
                $form.removeClass('js-submitting');
                $form.find('.spinner').addClass('hidden');
                $form.find('input[type="submit"]').prop('disabled', false).removeClass('disabled');
            }
        });

    }

});