(() => {
    $('input[name=username]').focus();

    if ($('#lblNote').text()) {
        $('#lblNote').parent().removeClass('d-none');

        setTimeout(() => {
            $('#lblNote').text('').parent().addClass('d-none');
        }, 5000);
    }

    $('form').submit(function (e) {
        const username = $('input[name=username]').val().trim();
        const pass = $('input[name=password]').val();

        if (username === '' || pass === '') {
            e.preventDefault();

            $('#lblNote').text('Chưa nhập tài khoản hoặc mật khẩu.').parent().removeClass('d-none');

            setTimeout(() => {
                $('#lblNote').text('').parent().addClass('d-none');
            }, 3000);
        }
    });
})();