$(function () {
    $('input[name=username]').focus();

    if ($('#lblNote').text()) {
        $('#lblNote').closest('.control-group').addClass('d-block').removeClass('d-none');

        setTimeout(() => {
            $('#lblNote').text('').closest('.control-group').addClass('d-none').removeClass('d-block');
        }, 5000);
    }

    $('#loginform').submit(function (e) {
        const username = $('input[name=username]').val().trim();
        const pass = $('input[name=password]').val();

        if (username === '' || pass === '') {
            e.preventDefault();

            $('#lblNote').text('Chưa nhập tài khoản hoặc mật khẩu.').closest('.control-group').addClass('d-block').removeClass('d-none');

            setTimeout(() => {
                $('#lblNote').text('').closest('.control-group').addClass('d-none').removeClass('d-block');
            }, 3000);
        }
    });
});