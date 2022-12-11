/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$("input").on("change paste", function () {
    sessionStorage.setItem(get["module"] + "_" + get["action"] + "_isFormModified", 1);
});

if (sessionStorage.getItem(get["module"] + "_" + get["action"] + "_isFormModified") == 1) {
    tellMeImleaving(msgTBIML)
} else {
    $(window).off("beforeunload");
}

$(document).ready(function () {
    $('.aj-form-current-tab-save, .aj-form-current-tab-continue, .aj-form-current-tab-terminer, .aj-form-current-tab-terminer-configurer').click(function (event) {
        event.preventDefault();
        var isClicked = $(this);
        var tabForm = $(this).closest("form");
        var tabArray = $(tabForm).serializeArray();
        var countRequired = 0;
        $.each(tabArray, function (i, v) {
            var isRequired = $(tabForm).find("#" + v["name"]);
            if (typeof $(isRequired).attr("required") !== "undefined" && v["value"].length === 0) {
                $(isRequired).addClass('inputError').effect("highlight", 4000);
                countRequired += 1;
                setPopover(isRequired, "Ce champ est obligatoire. Veuillez le renseigner !");
            }
        });
        if (countRequired === 0) {
            var currentTab = parseInt($(tabForm).attr("data-value"));
            var xhr_ajax = -1;
            var _id = $("#id").val();
            if (isClicked.is('.aj-form-current-tab-terminer-configurer, .aj-form-current-tab-terminer') || _id > 0) {
                $(".loader-for-body-crm").toggle();
                var xhr_ajax = 1;
            }
            var _xhrPosted = {app: get["app"], module: get["module"], action: "ajax", actionParent: get["action"], datas: JSON.stringify(tabArray), _id: _id, currentTab: currentTab, xhr_ajax: xhr_ajax, todo: "savingCurrentTabInfo"};
            $.get("/backend/index.php", _xhrPosted).done(function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    var nextTab = currentTab + 1;
                    $('a[href^="#kt_portlet_base_demo_2_' + nextTab + '_tab_content"]').removeClass("disabled");
                    if (isClicked.hasClass("aj-form-current-tab-terminer")) {
                        $(window).off("beforeunload");
                        $(".loader-for-body-crm h1").html("Les données ont été " + ((get["action"] === "modification") ? " mises à jour avec succès" : "définitivement sauvegardées") + ". Redirection en cours !");
                        setTimeout(function () {
                            window.location.replace(listLink);
                        }, 4000);
                        return;
                    } else {
                        if (isClicked.is(".aj-form-current-tab-terminer-configurer")) {
                            window.history.pushState("string", "Title", majLink + window.btoa(unescape(encodeURIComponent(back[2]))));
                            $(".loader-for-body-crm, .action-execution-ajax-form").hide();
                            $(".leave-out-execution-ajax-form").toggleClass('hide');
                            $("#id").val(back[2]);
                        }
                        if (isClicked.is(".aj-form-current-tab-continue, .aj-form-current-tab-terminer-configurer")) {
                            $('#kt_portlet_base_demo_2_' + currentTab + '_tab_content, #kt_portlet_base_demo_2_' + nextTab + '_tab_content').toggleClass("active");
                            $('a[href^="#kt_portlet_base_demo_2_' + currentTab + '_tab_content"], a[href^="#kt_portlet_base_demo_2_' + nextTab + '_tab_content"]').toggleClass("active");
                        }
                        sessionStorage.removeItem(get["module"] + "_" + get["action"] + "_isFormModified");
                        $(tabForm).find('input, select').removeClass('inputError');
                        if (!(currentTab > 1 && isClicked.is(".aj-form-current-tab-continue"))) {
                            notify(back[0], back[1]);
                        }
                    }
                } else {
                    $(".loader-for-body-crm").fadeOut("bounce");
                    if (typeof back[2] !== "undefined") {
                        $("#" + back[2]).addClass('inputError').effect("highlight", 4000);
                        setPopover("#" + back[2], back[1]);
                    } else {
                        notify(back[0], back[1]);
                    }
                }
            });
        }
    });

    $(document).on('click', '.action-execution-ajax-form', function () {
        var actionToDo = $(this).attr("data-value");
        var msgAction = (actionToDo === "annulerSaisie" || actionToDo === "annulerSaisieCourante") ? "annulation de saisie" : ((actionToDo === "deleteRecordsFromDatatable") ? "suppression définitive" : ((actionToDo === "archiveRecordsFromDatatable") ? "désactivation" : ((actionToDo === "reinitialisationMotDePasse") ? "réinitialisation de mot de passe" : "activation")));
        var message = "Vous allez effectuer une " + msgAction + " ! Continuer ?";
        confirmed('Êtes vous-sûr ?', message, function () {
            $(".loader-for-body-crm h1").html("Annulation de la saisie en cours !");
            $(".loader-for-body-crm").toggle();
            var _xhrPosted = {app: get["app"], module: get["module"], element: "", action: "ajax", datas: "", todo: actionToDo};
            $.get("/backend/index.php", _xhrPosted).done(function (returned) {
                $(".loader-for-body-crm").fadeOut("bounce");
                $(".loader-for-body-crm h1").html("La saisie en cours a été annulée !");
                window.location.replace(listLink);
            });
        });
    });

    $('#avatar').change(function () {
        formdata = new FormData();
        if ($(this).prop('files').length > 0) {
            var file = $(this).prop('files')[0];
            formdata.append("image", file);
        }
        var _id = (typeof $("#id").val() === "undefined") ? -1 : $("#id").val();
        $.ajax({
            url: "/backend/index.php?module=" + get["module"] + "&action=ajax&app=" + get["app"] + "&todo=uploadAvatar&_id=" + _id,
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (returned) {
                var back = JSON.parse(returned);
                notify(back[0], back[1]);
            }
        });
    });

    $(document).ready(function () {
        $('.aj-form-back-tab-state').click(function (event) {
            event.preventDefault();
            tabBack(get);
        });
    });

    // Créé une instance de l'observateur lié à la fonction de callback
    var observer = new MutationObserver(function (mutationsList) {
        for (var mutation of mutationsList) {
            if (mutation.type == 'attributes') {
                if (mutation.oldValue.indexOf("kt-avatar--changed") > 0 && mutation.oldValue.indexOf("kt-avatar--changed")) {
                    $(document).on('click', '.kt-avatar__cancel', function () {
                        var _id = (typeof $("#id").val() === "undefined") ? -1 : $("#id").val();
                        $.ajax({
                            url: "/backend/index.php?module=" + get["module"] + "&action=ajax&app=" + get["app"] + "&todo=removeAvatar&_id=" + _id,
                            type: "POST",
                            processData: false,
                            contentType: false,
                            success: function (returned) {
                                var back = JSON.parse(returned);
                                notify(back[0], back[1]);
                            }
                        });
                    });
                }
            }
        }
    });
    observer.observe(document.getElementById('kt_user_avatar_3'), {attributes: true, attributeOldValue: true, attributeFilter: ["class"]});
});