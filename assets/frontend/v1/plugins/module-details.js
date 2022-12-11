/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function secureparse(param) {
    try {
        return JSON.parse(param);
    } catch (e) {
        return false;
    }
}

$('.aj_details_per_link').click(function () { // gestion des liens du gauche
    var clicked = $(this);
    var todo = clicked.data("link") ? clicked.data("link") : null;
    var old = $('.aj_details_per_link.kt-widget__item--active');
    if (old.data("link") !== todo) {
        $.get("/backend/index.php", {app: get["app"], module: get["module"], action: "ajax", todo: todo}).done(function (returned) {
            var data = secureparse(returned);
            if (data !== false && (parseInt(data[0]) === -1 || parseInt(data[0]) === 1)) {
                notify(data[0], data[1]);
                return;
            } else {
                $('#aj_user_details_right').html(returned);
                old.removeClass('kt-widget__item--active'); // On supprime le lien qui etait actif
                clicked.toggleClass('kt-widget__item--active');
                scrollEffect("#kt_subheader", null);
                return;
            }
        });
    }
});
