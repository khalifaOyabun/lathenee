/*

For all scripts that are used once !

**/

var extra = $(document);

extra.ready(function () {

    /**
     * IDLE - Connexion
     */

    extra.on('submit', '[data-athenee-contact-form]', function (e) {
        e.preventDefault();

        let el = $(this);
        let post = el.serializeArray();
        let __xhr = { datas: post };

        scrollOn('[data-index-form-register-for]');
        $.post(`/frontend/index.php?app=${get["app"]}&module=${get["module"]}&action=ajax&todo=writeus&__to=` + el.data('index-form-register-for'), __xhr).done(function (response) {
            let back = JSON.parse(response)
            if (back['status'] === 'success') {
                el.find('input').val(null);
                el.find('textarea').val(null);
            }

            $('body').removeClass('overlay loader loader-bouncing is-active');
            toastshower(back["titre"], back["notification"], "toast-top-right", back["status"]);
        });
    });
});