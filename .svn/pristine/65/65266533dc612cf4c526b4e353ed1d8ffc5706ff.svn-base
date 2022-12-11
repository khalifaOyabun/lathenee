/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {
    //  Annulation action sur alertes
    $(document).on('click', '.close-window-event, .close-window-event-saved', function () {
        $("#show-det-edit-windows").removeClass("pin-window-left");
        $("#show-det-edit-windows").toggle("drop");
    });
    // Actions sur une affaire. QPanel action
    $(document).on('click', '[data-affaire-actions="alertes"]', function () {
        var infos = {};
        moment.locale('fr');
        infos = {
            "libelle": "Sans titre",
            "jour_debut": moment().format('ll'),
            "heure_debut": "09:00",
            "jour_fin": moment().format('ll'),
            "heure_fin": "10:00",
            "id_concerne": $('[data-part-id-node]').data('part-id')
        };
        var _xhrPosted = "&app=backend&caller=" + get["module"] + "&module=agenda&action=ajax&_id=RAPPEL&actionParent=detail&todo=loadFormAddTache&more=" + JSON.stringify(infos);
        $.ajax({
            url: "/backend/index.php?" + _xhrPosted
        }).done(function (returned) {
            $('.load-det-edit-windows').html(returned);
            $("#show-det-edit-windows").toggle("drop");
            _alert_reload_plug();
        });
    });
    // Actions sur une affaire. QPanel action - details
    $(document).on('click', '[data-alerte-actions="detail"]', function () {
        var infos = {}, idalerte = $(this).data("id-alerte");
        var _xhrPosted = "&app=backend&caller=" + get["module"] + "&module=agenda&action=ajax&_id=" + idalerte + "&actionParent=" + get["action"] + "&todo=loadTache&more=" + JSON.stringify(infos)
        $.ajax({
            url: "/backend/index.php?" + _xhrPosted
        }).done(function (returned) {
            $('.load-det-edit-windows').html(returned);
            if (document.getElementById("show-det-edit-windows").style.display === "none" || idalerte === sessionStorage.getItem('idEventOutterAgenda')) {
                $("#show-det-edit-windows").toggle("drop");
            }
            sessionStorage.setItem('idEventOutterAgenda', idalerte);
            if (risNaN(idalerte)) {
                _alert_reload_plug();
            }

        });
    });
    // Actions sur une affaire. ajouter nouvelle tache
    $(document).on('submit', '#kt_form_agenda_tache, #kt_form_agenda_tache_edition', function (e) {

        e.preventDefault();
        $(document).find("[data-loader-window]").find(".loader-for-body-crm").toggle();
        var form = $(this);
        var formData = _alert_build_form_var(form.serializeArray());

        $.get("/backend/index.php", {app: "backend", caller: get['module'], module: "agenda", actionParent: "index", action: "ajax", datas: JSON.stringify(formData), tpl_to_load: "_alert/_inc_alert_list_model.tpl", todo: "saveTask"}).done(function (returned) {

            $(document).find("[data-loader-window]").find(".loader-for-body-crm").toggle();
            var back = JSON.parse(returned);
            if (back[0] == 1) {

                if (typeof back[2] === "undefined") {
                    $("#show-det-edit-windows").hide("drop");
                    $(document).find('[data-alert-list]').append(back[1]);
                    $(document).find('[data-no-alerte-found-message]').hide();
                } else {
                    $("[data-alerte-" + back[2] + "]").replaceWith(back[1]);
                    var _xhrPosted2 = "&app=" + get["app"] + "&caller=" + get["module"] + "&module=agenda&action=ajax&_id=" + back[2] + "&actionParent=" + get["action"] + "&todo=loadTache";
                    $.ajax({
                        url: "/backend/index.php?" + _xhrPosted2
                    }).done(function (returned) {
                        var data = secureparse(returned);
                        if (data !== false && (parseInt(data[0]) === -1 || parseInt(data[0]) === 1)) {
                            notify3(data[1], data[0] == 1 ? 'success' : 'error');
                        } else {
                            $('.load-det-edit-windows').html(returned);
                        }
                    });
                }
                notify3("L'alerte a été enregistrée avec succès", 'success');
            } else {
                notify3(back[1], 'error');
            }
        });
    });

    $(document).on('click', '[data-edit-event]', function (e) {
        var id = parseInt(sessionStorage.getItem('idEventOutterAgenda'));
        if (risNaN(id)) {
            notify(-1, "Cette tâche semble ne pas exister.");
        } else {
            _xhr_form(id);
        }
    });

    $(document).on('click', '.archive-event', function (e) {
        var id = parseInt(sessionStorage.getItem('idEventOutterAgenda'));
        _alert_ad(id, 'archiveTache');
    });

    $(document).on('click', '.delete-event', function (e) {
        var id = parseInt(sessionStorage.getItem('idEventOutterAgenda'));
        _alert_ad(id, 'deleteTache');
    });

    $(document).on('click', '.send-ics-to-concerned', function (e) {
        var id = parseInt(sessionStorage.getItem('idEventOutterAgenda'));
        _alert_ics(id);
    });
});

function _xhr_form(id, todo = 'loadTache') {
    var _xhrPosted = "&app=" + get["app"] + "&caller=" + get["module"] + "&module=agenda&action=ajax&_id=" + id + "&actionParent=" + get["action"] + "&_ismaj&todo=" + todo;
    $.ajax({
        url: "/backend/index.php?" + _xhrPosted
    }).done(function (returned) {
        $('#is-edit-or-details').html(returned);
        _alert_reload_plug();
    });
}