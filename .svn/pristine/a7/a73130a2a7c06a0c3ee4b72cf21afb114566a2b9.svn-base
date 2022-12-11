
$(document).ready(function () {

    var elforappend;

    $(document).on("change", "[data-associe-email-input]", function () {
        var el = $(this);
        var elforappendassocie = $(document).find('#is-buyer-here');

        elforappendassocie.html('');
        // elforappendassocie.removeClass("tab-container-founded-buyer-with-accroche");
        $.ajax({
            url: '/backend/index.php',
            type: 'get',
            data: 'app=backend&module=acheteursrecherche&actionParent=' + get['action'] + '&action=ajax&todo=isBuyerHere&email=' + el.val(),
            success: function (returned) { //Succès de la requête
                var back = JSON.parse(returned);
                if (back['state'] == 1) {
                    elforappendassocie.html(back['tpl']);
                    // elforappendassocie.addClass("tab-container-founded-buyer-transition");
                } else {
                    elforappendassocie.html('');
                    // elforappendassocie.removeClass("tab-container-founded-buyer-transition");
                }
            }
        });
    });

    $("[data-acheteur-email-input]").change(function () {
        var el = $(this), old = $("[data-acheteur-old-email-value]").data('acheteur-old-email-value'), _id = null;
        elforappend = $(document).find('[data-container-founded-buyer]');
        if (typeof get['id'] !== 'undefined') {
            _id = get['id'];
        }
        elforappend.html('');
        elforappend.removeClass("tab-container-founded-buyer-with-accroche");
        if (old === '' || old !== el.val()) {
            $.ajax({
                url: '/backend/index.php',
                type: 'get',
                data: 'app=backend&module=acheteursrecherche&actionParent=' + get['action'] + '&action=ajax&todo=isEmailHere&email=' + el.val() + '&_id=' + _id,
                success: function (returned) { //Succès de la requête
                    var back = JSON.parse(returned);
                    if (back['state'] == 1) {
                        elforappend.html(back['tpl']);
                        elforappend.addClass("tab-container-founded-buyer-transition");
                    } else {
                        elforappend.removeClass("tab-container-founded-buyer-transition");
                    }
                }
            });
        } else {
            elforappend.removeClass("tab-container-founded-buyer-transition");
        }
    });

    $(document).on("click", "[data-close-tab-container-founded-buyer-transition]", function () {
        elforappend.removeClass("tab-container-founded-buyer-transition");
        elforappend.addClass("tab-container-founded-buyer-with-accroche");
        elforappend.find('[data-accroche-button-shower]').addClass('icon-accroche-to-board');
        setTimeout(function () {
            elforappend.find('[data-accroche-button-shower]').toggle('slide');
            notify3('Veuillez poursuivre votre saisie !', 'success');
        }, 2000);
    });

    $(document).on("click", "[data-accroche-button-shower]", function () {
        elforappend.hide();
        elforappend.removeClass("tab-container-founded-buyer-with-accroche");
        elforappend.show();
        elforappend.find('[data-accroche-button-shower]').toggle('slide');
        elforappend.addClass("tab-container-founded-buyer-transition");
        elforappend.removeClass("tab-container-founded-buyer-with-accroche");
        elforappend.find('[data-accroche-button-shower]').removeClass('icon-accroche-to-board');
    });

    $(document).on("click", "[data-is-transaction-change-button-acheteur]", function () {
        var el = $(this);
        if (el.is(":checked")) {
            //confirmed('Êtes vous-sûr ?', "Les types d'affaires déjà saisis seront perdues. Continuez ?", function () {
            var formData = {}, brute = $('[data-search-form]').serializeArray();
            $.each(brute, function (key, value) {
                var k = value["name"].replace("[]", "");
                if (typeof formData[k] === "undefined") {
                    // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                    formData[k] = (value["name"] !== k) ? "#" + value["value"] + "#" : value["value"];
                } else {
                    if (value["value"] != "") {
                        formData[k] += "#" + value["value"] + "#";
                    }
                }
            });
            var arrayDatas = JSON.stringify(formData);

            $.get("/backend/index.php", {
                app: get["app"], module: "acheteursrecherche", action: "ajax", datas: arrayDatas, code_type_transaction: el.val(), todo: "transactionOrTypeaffaireSearchChange"
            }).done(function (returned) {
                $("[data-frame-to-load-according-transaction]").html(returned); //Ajoute à la fin
                KTBootstrapSwitch.init();
                KTSelect2.init();
            });
            //});
        }
    });

    $(document).on("change", "[data-current-type-affaire-for-field-to-show]", function () {
        var el = $(this);
        var formData = {}, brute = $('[data-search-form]').serializeArray();
        $.each(brute, function (key, value) {
            var k = value["name"].replace("[]", "");
            if (typeof formData[k] === "undefined") {
                // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                formData[k] = (value["name"] !== k) ? "#" + value["value"] + "#" : value["value"];
            } else {
                if (value["value"] != "") {
                    formData[k] += "#" + value["value"] + "#";
                }
            }
        });
        var arrayDatas = JSON.stringify(formData);
        $.get("/backend/index.php", {
            app: get["app"], module: "acheteursrecherche", action: "ajax", datas: arrayDatas, code_type_transaction: $('[data-is-transaction-change-button-acheteur]:checked').val(), id_type_affaire: el.val(), todo: "transactionOrTypeaffaireSearchChange"
        }).done(function (returned) {
            $("[data-frame-to-load-according-transaction]").html(returned); // Ajoute à la fin
            KTBootstrapSwitch.init();
            KTSelect2.init();
        });
    });

    $(document).on("change", '[data-current-departement-for-ville-to-show]', function () {
        var dept = $(this).val();
        $.get("/backend/_ajax.php", {
            app: get["app"], module: "acheteursrecherche", departements: dept, action: "loadVillesFromDeptForResearchFromBD"
        }).done(function (returned) {
            $("[data-current-ville-to-show]").html(returned); //Ajoute à la fin
            KTSelect2.init();
        });
    });

    $(document).on("click", "[data-rapprochement-launcher]", function () {
        var id = $(this).data('rapprochement-id'), isopened = $("[data-show-rapprochement-windows]").attr("data-rapprochement-opened");
        if (isopened === id) {
            $("[data-show-rapprochement-windows]").attr("data-rapprochement-opened", "");
            $("[data-show-rapprochement-windows]").hide();
        } else {
            if (isopened === "") {
                $("[data-show-rapprochement-windows]").toggle();
            }
            $("[data-show-rapprochement-windows]").attr("data-rapprochement-opened", id);

            $.get("/backend/index.php", {
                app: get["app"], module: "acheteursrecherche", action: "ajax", _id: id, todo: "loadRapprochement"
            }).done(function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {

                    $('[data-show-rapprochement-windows]').html(back[1]);
                    if (isopened == "") {
                        // $("[data-show-rapprochement-windows]").toggle();
                    }
                    $("[data-show-rapprochement-windows]").attr("data-rapprochement-opened", id);
                }
            });
        }
    });

    $(document).on("click", "[data-associe-launcher]", function () {
        var id = $(this).data('associe-id'), isopened = $("[data-show-associe-windows]").attr("data-associe-opened");
        if (isopened === id) {
            $("[data-show-associe-windows]").attr("data-associe-opened", "");
            $("[data-show-associe-windows]").hide();
        } else {
            if (isopened === "") {
                $("[data-show-associe-windows]").toggle();
            }
            $("[data-show-associe-windows]").attr("data-associe-opened", id);

            $.get("/backend/index.php", {
                app: get["app"], module: "acheteursrecherche", action: "ajax", _id: id, todo: "loadAssocies"
            }).done(function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {

                    $('[data-show-associe-windows]').html(back[1]);
                    if (isopened == "") {
                        // $("[data-show-associe-windows]").toggle();
                    }
                    $("[data-show-associe-windows]").attr("data-associe-opened", id);
                }
            });
        }
    });

    $(document).on("click", "[data-tc]", function () {
        var tc = $(this).data("tc");
        $(document).find("[data-tc-" + tc + "]").toggle('blind');
        $(this).find('i').toggleClass('fa-plus fa-minus');
    });

    $(document).on("click", "[data-action-rapprochement]", function () {
        var el = $(this);
        var todo = el.data("action-rapprochement");
        var message = ((todo === "propositionRapprochement") ? "Vous allez proposer cette affaire ?" : ((todo === "annulerRapprochement") ? "Vous allez remettre cette affaire aux rapprochements ?" : "Vous allez rejeter cette affaire ?"));
        confirmed("Êtes vous-sûr ? ", message, function () {
            $.get("/backend/index.php", {
                app: get["app"], module: "acheteursrecherche", action: "ajax", id_acheteur_recherche: el.data('id-recherche-acheteur'), id_affaire: el.data('id-affaire'), todo: todo
            }).done(function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    if (back[2] == 2) {
                        var cloned = el.parents(".kt-widget5").clone();
                        cloned.find('[data-action-rapprochement="propositionRapprochement"]').remove();
                        if ($("#affaire_aproposee_fenetre_id").find(".kt-widget5").length > 0) {
                            cloned.insertAfter($("#affaire_aproposee_fenetre_id").find(".kt-widget5").last());
                        } else {
                            $("#affaire_aproposee_fenetre_id").append(cloned);
                        }
                    }
                    el.parents(".kt-widget5").remove();
                }
                notify3(back[1], 'success');
            });
        });
    });

    //  Actions sur document
    $(document).on('click', '[data-accroche-aside-docs-multi-actions]', function () {
        var action = $(this).data("accroche-aside-docs-multi-actions");
        var checked = $(document).find('[data-toggle-checkbox-doc-list]').find('input:checkbox:checked');
        var title = $(this).parent().data('title');
        var message = "Vous souhaitez <b>" + title.toLowerCase() + "</b> ! Continuer ?";
        if (checked.length > 0) {
            confirmed('Êtes vous-sûr ?', message, function () {
                var docs = [];
                $.each(checked, function () {
                    docs.push($(this).val());
                });
                if (action === 'removeUploadedFileUppy') {
                    $.get("/backend/_ajax.php?app=backend&module=acheteursrecherche&action=" + action + "&from=acheteursrecherche&filename=" + JSON.stringify(docs) + "&id=" + $('[data-part-id-node]').data('part-id')).done(function (returned) {
                        var back = secureparse(returned);
                        if (back[0] === 'success') {
                            $.each(back[2], function (i) {
                                $(document).find('[data-filename="' + back[2][i] + '"]').remove();
                            });
                            $(document).find('[data-accroche-aside-actions]').toggle("blind");
                            $(document).find('[data-toggle-checkbox-doc-list]').toggle("slide");
                            $(document).find('[data-dropdown-menu-manage]').toggle("drop");
                            if ($(document).find('[data-filename]').length === 0) {
                                $('[data-no-file-found-message]').show('clip');
                            }
                        }
                        notify3(back[1], back[0]);
                    });
                } else {
                    var clicked;
                    if (action === 'copyDocs') {
                        clicked = '[data-modal-copy-button]';
                        $("[ data-modal-move-button]").hide();
                        $("[ data-modal-copy-button]").show();
                    } else {
                        clicked = '[data-modal-move-button]';
                        $("[ data-modal-copy-button]").hide();
                        $("[ data-modal-move-button]").show();
                    }

                    $("[data-quick-panel-tab-documents]").toggleClass("kt-cp");
                    $("[data-detail-show-affaires-list]").toggle("drop");
                    $(document).on('click', clicked, function () {
                        var achecked = $('.affaire_selected:checked');
                        var checked = [];
                        if (achecked.length > 0) {
                            $.each(achecked, function () {
                                checked.push($(this).val());
                            });
                            $.get("/backend/_ajax.php?app=backend&module=acheteursrecherche&action=" + action + "&from=acheteursrecherche&filename=" + JSON.stringify(docs) + "&items=" + JSON.stringify(checked) + "&id=" + $('[data-part-id-node]').data('part-id')).done(function (returned) {
                                var back = secureparse(returned);
                                if (back[0] === 'success') {
                                    if (action === 'moveDocs') {
                                        $.each(back[2], function (i) {
                                            $(document).find('[data-filename="' + back[2][i] + '"]').remove();
                                        });
                                    }
                                    $.each(achecked, function () {
                                        $(this).prop('checked', false);
                                    });
                                    $("[data-quick-panel-tab-documents]").removeClass("kt-cp");
                                    $("[data-detail-show-affaires-list]").hide("drop");
                                    $(document).find('[data-accroche-aside-actions]').toggle("blind");
                                    $(document).find('[data-toggle-checkbox-doc-list]').toggle("slide");
                                    $(document).find('[data-dropdown-menu-manage]').toggle("drop");
                                    if ($(document).find('[data-filename]').length === 0) {
                                        $('[data-no-file-found-message]').show('clip');
                                    }
                                }
                                notify3(back[1], back[0]);
                            });
                        } else {
                            notify3("Cocher d'abord une affaire dans la liste.", 'error');
                        }
                        log(achecked);
                    });
                }
            });
        } else {
            notify3("Cocher d'abord un élément dans la liste.", 'error');
        }
    });
    //  Actions sur document
    $(document).on('click', '[data-toggle-checkbox-doc-list-select-multiple]', function () {
        $(document).find('[data-accroche-aside-actions]').toggle("blind");
        $(document).find('[data-toggle-checkbox-doc-list]').toggle("slide");
        $(document).find('[data-dropdown-menu-manage]').toggle("drop");
        $(document).find('[data-toggle-checkbox-doc-list]').find('input:checkbox').prop('checked', false);
        $(this).parents('[data-toggle-doc-target]').find('input:checkbox').prop('checked', true);
    });
    //  Annulation action sur document
    $(document).on('click', '[data-toggle-checkbox-doc-checkboxes]', function () {
        var checked = $(document).find('[data-toggle-checkbox-doc-list]').find('input:checkbox:checked').length;
        if (checked === 0) {
            $(document).find('[data-accroche-aside-actions]').toggle("blind");
            $(document).find('[data-toggle-checkbox-doc-list]').toggle("slide");
            $(document).find('[data-dropdown-menu-manage]').toggle("drop");
        }
    });
});

