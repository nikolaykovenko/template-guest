function ajaxDefaultResponse(data) {
    var result = $.parseJSON(data);
    alert(result.message);

    if (result.status == 'ok') {
    }
}

function registerResponse(result, form) {
    form.fadeTo(200, 1);

    alert(result.message);

    if (result.status == 'ok') {
        location.href = 'index.php';
    }
}

function ajaxQuery(data, response) {
    if (!response) {
        response = ajaxDefaultResponse;
    }

    return $.get('index.php', data, response);
}


$(function () {
    $('body').on('submit', 'form[data-ajax-form]', function (e) {
        e.preventDefault();
        var form = $(this);

        form.fadeTo(200, .6);
        $.post(form.attr('action'), {'form': form.serialize()}, function (data) {
            var result = $.parseJSON(data),
                 callback = form.attr('data-ajax-form-response'),
                 fn = window[callback];

            if (typeof fn === 'function') {
                fn(result, form);
            } else {
                form.fadeTo(200, 1);
                ajaxDefaultResponse(data);
            }
        });

        return false;
    });
});
