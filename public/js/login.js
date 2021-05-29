(() => {
    $('input[name=username]').focus();

    if ($('#lblNote').text()) {
        $('#lblNote').removeClass('invisible');

        setTimeout(() => {
            $('#lblNote').text('&nbsp;').addClass('invisible');
        }, 5000);
    }

    $('form').submit(function (e) {
        const username = $('input[name=username]').val().trim();
        const pass = $('input[name=password]').val();

        if (username === '' || pass === '') {
            e.preventDefault();

            $('#lblNote').text('Chưa nhập tài khoản hoặc mật khẩu.').removeClass('invisible');

            setTimeout(() => {
                $('#lblNote').text('&nbsp;').addClass('invisible');
            }, 3000);
        }
    });
})();