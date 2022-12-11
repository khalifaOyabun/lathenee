$(document).ready(function () {
    var thisExportOptions = {
        exportOptions: {
            rows: function (idx, data, node) {
                var checkedB = sontCoches("dt-class-checkbox", "entireRow");
                var dt = new $.fn.dataTable.Api('#datatable-configuration');
                $(checkedB).each(function (i, v) {
                    dt.row(this).select();
                });
                var selected = dt.rows({selected: true}).indexes().toArray();
                if (selected.length === 0 || $.inArray(idx, selected) !== -1)
                    return true;
                return false;
            },
            columns: ':visible'
        }
    };

    var table = $('#datatable-configuration').DataTable({
        "stateSave": true,
        "scrollY": '150vh',
        "scrollCollapse": true,
        "deferRender": true,
        "paging": true,
        "processing": true,
        "serverSide": true,
        "info": true,
        //"responsive": true,
        "pagingType": 'full_numbers',
        "order": [columnOrderdefault],
        "lengthMenu": lengthMenu,
        "dom": "<'row'<'col-sm-2'l><'col-sm-6 text-center'B><'col-sm-4'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "searchable": true,
        "orderable": true,
        "rowReorder": rowReorder,
        "colReorder": colReorder,
        "columnDefs": columnsDefs,
        "ajax": {
            "url": "/backend/index.php",
            "dataType": "json",
            "type": "GET",
            "data": {
                "app": get["app"],
                "module": get["module"],
                "element": cElement,
                "action": "serverside",
                "actionParent": get["action"],
                "get": get,
                "list": ""
            }
        },
        "columns": columnsDT,
        "drawCallback": function (settings) {
            if (!$(document).find(".dt-class-checkbox").is(":checked")) {
                $("#toggle-check-all-checkbox").prop("checked", false);
            } else {
                if ($('.dt-class-checkbox:checked').length === $('.dt-class-checkbox').length) {
                    $("#toggle-check-all-checkbox").prop('checked', true);
                }
            }
        },
        "buttons": [
            $.extend(true, {}, thisExportOptions, {text: 'Imprimer', extend: 'print'}),
            $.extend(true, {}, thisExportOptions, {text: 'Copier', extend: 'copyHtml5'}),
            $.extend(true, {}, thisExportOptions, {text: 'Excel', extend: 'excelHtml5'}),
            $.extend(true, {}, thisExportOptions, {text: 'CSV', extend: 'csvHtml5'}),
            $.extend(true, {}, thisExportOptions, {text: 'PDF', extend: 'pdfHtml5'}),
            {extend: 'colvis', text: 'Export colonnes', className: 'btn-primary', columns: ":not(.notConcernedByColvis)"}
        ],
        "fnStateSave": function (oSettings, oData) {
            try {
                localStorage.setItem("dataTableStore", JSON.stringify(oData)); //saves to the database, "key", "value"
            } catch (e) {
                if (e == QUOTA_EXCEEDED_ERR) {
                    localStorage.removeItem("dataTableStore");
                    localStorage.setItem("dataTableStore", JSON.stringify(oData)); //saves to the database, "key", "value"
                }
            }
        },
        "fnStateLoad": function (oSettings) {
            return JSON.parse(localStorage.getItem('dataTableStore'));
        },
        "stateSaveParams": function (settings, data) {
            data.columns.forEach(function (column) {
                delete column.visible;
            });
        }/*,
         "initComplete": function (settings, json) {
         $('body').find('.dataTables_scrollBody').addClass("kt-scroll");
         },*/
        //select: true
    });
    if (delButton == 1) {
        table.buttons().destroy();
    }
    /* Buttons export actions */
    /*$('#export_print').on('click', function (e) {
     e.preventDefault();
     table.button(0).trigger();
     });
     
     $('#export_copy').on('click', function (e) {
     e.preventDefault();
     table.button(1).trigger();
     });
     
     $('#export_excel').on('click', function (e) {
     e.preventDefault();
     table.button(2).trigger();
     });
     
     $('#export_csv').on('click', function (e) {
     e.preventDefault();
     table.button(3).trigger();
     });
     
     $('#export_pdf').on('click', function (e) {
     e.preventDefault();
     table.button(4).trigger();
     });
     
     $('#column_visible').on('click', function (e) {
     e.preventDefault();
     table.button(5).trigger();
     });*/

    // Rangement des lignes dans un datatable
    table.on('row-reordered', function (e, details, edit) {
        var arrayDatas = new Array(), columnData = new Array();
        var ids = table.columns(0).data()[0];
        var line = table.rows().data();
        $.each(columnsDT, function (key, value) {
            if (typeof value["className"] !== "undefined" && value["className"].indexOf("row-reference") > -1) {
                $.each(line, function (skey, svalue) {
                    columnData.push(svalue[value["data"]]);
                });
                return;
            }
        });
        $.each(ids, function (key, value) {
            arrayDatas[key] = JSON.stringify({"referenceDelUpd": columnData[key], "rang": value});
        });
        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", "datas[]": arrayDatas, todo: "updatingRange"});
    });
    table.on('mousedown.rowReorder', 'tbody tr td', function () {
        $(this).find('i.fa-hand-paper').toggleClass("fa-hand-paper fa-hand-rock");
        /*
         var tr = $(this).closest('tr');
         console.log($(this).find('i.fa'));
         
         $(document).on('mousemove.rowReorder touchmove.rowReorder', function () {
         console.log('Dragging row', tr);
         });*/
    });

    // Pour le bouton "Plus de détails"
    $(document).on('click', '.detailsControl', function () {
        var oThis = $(this);
        var id = oThis.closest(".id-datatable-line-for-reference").attr("data-reference-row");
        var tr = $("#datarow-option-id-" + id).closest('tr');
        var rowClicked = table.row(tr);
        if (oThis.find("i").hasClass("fa-eye") || oThis.find("i").hasClass("fa-eye-slash")) {
            oThis.find("i").toggleClass("fa-eye fa-eye-slash");
        } else if (oThis.find("i").hasClass("flaticon-plus") || oThis.find("i").hasClass("flaticon2-line")) {
            oThis.find("i").toggleClass("flaticon-plus flaticon2-line");
        } else {
            oThis.find("i").toggleClass("flaticon2-resize flaticon2-shrink");
        }
        if (rowClicked.child.isShown()) {
            $('div.slider', rowClicked.child()).slideUp(function () {
                rowClicked.child.remove();
                tr.removeClass('shown');
            });
        } else {
            const p = new Promise((resolve, reject) => {
                var resolved = false;
                if (table.row('.shown').length) {
                    $('.detailsControl', table.row('.shown').node()).click();
                    resolved = true;
                } else {
                    resolved = true;
                }
                if (resolved === true) {
                    resolve("Well done !");
                } else {
                    reject(":-(");
                }
            });

            p.then((message) => {
                table.rows().every(function () {
                    if (this.child.isShown()) {
                        this.child.remove();
                        tr.removeClass('shown');
                    }
                });
                $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", idChildRow: id, todo: "childRow"}).done(function (returned) {
                    rowClicked.child(returned, "no-padding noteditable").show(2000);
                    tr.addClass('shown');
                    $('div.slider', rowClicked.child()).slideDown();
                    KTBootstrapSwitch.init();
                });
            }).catch((message) => {
                console.log("None done" + message);
            });
        }
    });
    // Suppression ligne dans datatable
    $('#datatable-configuration tbody').on('click', '.deleteDatatableRow', function () {
        var message = "Vous allez supprimer cette ligne ! Voulez-vous continuer ?";
        var oThis = this;
        confirmed('Êtes vous-sûr ?', message, function () {
            var myreference;
            $.each($(oThis).closest('tr').find('td.row-reference'), function (key, value) {
                myreference = $(this).text();
            });
            $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", referenceDelUpd: myreference, todo: "deleteRow"}).done(function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    table.page(table.page.info().page).draw('page')
                }
                notify(back[0], back[1]);
            });
        });

    });
    // Edition d'une ligne avec un modal
    $(document).on('click', '.editDatatableRow', function () {
        var line = table.row($("#datarow-option-id-" + $(this).closest("ul.id-datatable-line-for-reference").attr("data-reference-row")).closest('tr')).data();
        var referenceDelUpd, counter = 1;
        $.each(columnsDT, function (key, value) {
            if (typeof value["className"] !== "undefined" && value["className"].indexOf("row-reference") > -1) {
                referenceDelUpd = value["data"];
            }
            if (value["className"].indexOf("modaledit") !== -1 && value["className"].indexOf("noteditable") === -1) {
                $("#data-modal-form-datatable-" + (counter)).val(line[value["data"]]);
                counter++;
            }
        });
        $("#hiddenReferenceDelUpd").remove();
        $("#modalNewLineDatatable .modal-footer").prepend("<input type='hidden' name='referenceDelUpd' value='" + line[referenceDelUpd] + "' id='hiddenReferenceDelUpd' />");
        // Show dialog
        $("#modalNewLineDatatable").modal('show');
    });

    // Edition d'une ligne avec un modal ajax
    $(document).on('click', '.editDatatableRowAjax, .editDatatableChildRowAjax', function () {
        var _id, oThis = $(this);
        $("#modal-title span").text("Modification");
        if (oThis.is(".editDatatableRowAjax")) {
            var line = table.row($("#datarow-option-id-" + oThis.closest("ul.id-datatable-line-for-reference").attr("data-reference-row")).closest('tr')).data();
            var referenceDelUpd;

            $.each(columnsDT, function (key, value) {
                if (typeof value["className"] !== "undefined" && value["className"].indexOf("row-reference") > -1) {
                    referenceDelUpd = value["data"];
                    return;
                }
            });
            _id = line[referenceDelUpd];
        } else {
            _id = oThis.data('id');
        }
        $.get("/backend/index.php", {app: get["app"], module: get["module"], aparent: get["action"], element: cElement, action: "ajax", _id: _id, todo: "loadUpdatedRow"}).done(function (returned) {
            var data = secureparse(returned);
            if (data !== false && (parseInt(data[0]) === -1 || parseInt(data[0]) === 1)) {
                notify(data[0], data[1]);
                return;
            } else {
                $("#modalNewLineDatatable .modal-body").html(returned);
                $("#hiddenReferenceDelUpd").remove();
                $("#modalNewLineDatatable .modal-footer").prepend("<input type='hidden' name='referenceDelUpd' value='" + _id + "' id='hiddenReferenceDelUpd' />");
                if (typeof $('.summernote') !== "undefined") {
                    $(".summernote").summernote({
                        height: 150
                    });
                }
                if (typeof $('.custom-file-input') !== "undefined") {
                    $('.custom-file-input').on('change', function () {
                        var fileName = $(this).val();
                        $(this).next('.custom-file-label').addClass("selected").html(fileName);
                    });
                }
                $("#modalNewLineDatatable").modal('show');
            }
        });
    });

    /*$(document).on('click', '.editDatatableRowAjaxs', function () {
     var oThis = $(this);
     var _ajaxId = (oThis.is(".action-execution-ajax-in-childrow")) ? oThis.attr("data-id") : oThis.closest(".id-datatable-line-for-reference").attr("data-reference-row");
     $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: JSON.stringify({"id": _ajaxId}), todo: "loadUpdatedRow"}).done(function (returned) {
     var data = secureparse(returned);
     if (data !== false && (parseInt(data[0]) === -1 || parseInt(data[0]) === 1)) {
     notify(data[0], data[1]);
     return;
     } else {
     $("#modalNewLineDatatable .modal-body").html(returned);
     $("#modalNewLineDatatable").modal('show');
     }
     });
     // $("#modalNewLineDatatable").modal('show');
     });*/

    // Ajout nouveau par ajout de ligne dans le datatable. 
    $('#addNewDatatableLine').click(function () {
        if (typeof $("#newDatatableLine").attr("data-action") == "undefined") {
            var html = '<tr id="newDatatableLine" data-action="edition">';
            $.each(columnsDT, function (key, value) {
                var currentId = key + 1;
                if (typeof value["visible"] === "undefined" || value["visible"] !== false) {
                    var rowContent = '<td id="data-line-datatable-' + value["data"] + '" ';
                    if (typeof value["className"] !== "undefined" && value["className"].indexOf("noteditable") > -1) {
                        rowContent += 'class="noteditable">';
                    } else {
                        if (typeof value["defaultContent"] === "undefined")
                            rowContent += 'contenteditable="true" placeholder="' + value["data"] + '..." style="background-color: #EEE;">';
                        else
                            rowContent += 'contenteditable="true" placeholder="' + value["data"] + '..." style="background-color: #EEE;">' + value["defaultContent"];
                    }
                }
                //  || currentId === columnsDT.length
                if (value["data"] === "options") {
                    rowContent += '<button id="newDatatableRow" class="btn btn-primary btn-elevate btn-circle btn-icon mr-2" title="Enregistrer"><i class="flaticon-interface-5"></i></button><button id="newDatatableRowDestroyed" class="btn btn-danger btn-elevate btn-circle btn-icon" title="Annuler la saisie"><i class="flaticon-close"></i></button>';
                }
                rowContent += '</td>';
                html += rowContent;
            });
            $('#datatable-configuration tbody').append(html);
        } else {
            notify(-1, "Attention ! <br />Vous avez déjà une ligne en cours d'édition.");
        }

        scrollEffect("#newDatatableLine", "highlight");
    });
    // Enregistrement de la nouvelle ligne ajoutée. 
    $(document).on('click', '#newDatatableRow', function () {
        var getEditableField = {};
        $.each(columnsDT, function (key, value) {
            if (typeof value["className"] === "undefined" || (value["className"].indexOf("noteditable") === -1)) {
                getEditableField[value["data"]] = $('#data-line-datatable-' + value["data"]).text();
            }
        });
        var arrayDatas = JSON.stringify(getEditableField);
        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, todo: "addNewRow"}).done(function (returned) {
            var back = JSON.parse(returned);
            if (back[0] == 1) {
                if (typeof back[2] !== "undefined" && back[2] === "loadOther") {
                    notify(back[0], back[1]);
                    $(location).attr("href", back[3]);
                    return;
                } else {
                    table.page(table.page.info().page).draw('page');
                }
            }
            notify(back[0], back[1]);
        });
    });
    // Annulation saisie en cours de l'ajout inline
    $(document).on('click', '#newDatatableRowDestroyed', function () {
        var message = "Vous souhaitez annuler la saisie en cours ! Continuer ?";
        confirmed('Êtes vous-sûr ?', message, function () {
            $("#newDatatableLine").fadeOut(2000, function () {
                table.page(table.page.info().page).draw('page');
                notify(1, "La saisie a été annulée !");
            });
        });
    });

    // Ajout avec formulaire d'elements dans un datatable
    $("#modalNewLineDatatableForm").submit(function (e) {
        e.preventDefault();
        var formData = {}, brute = $(this).serializeArray();
        $.each(brute, function (key, value) {
            var k = value["name"].replace("[]", "");
            if (typeof formData[k] === "undefined") {
                // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                formData[k] = value["value"];

            } else
                formData[k] += "##" + value["value"];
        });
        var arrayDatas = JSON.stringify(formData);
        var uploaded = blobimg(".blobImageDt");
        $.ajax({
            url: "/backend/index.php?app=" + get["app"] + "&module=" + get["module"] + "&action=ajax&element=" + cElement + "&datas=" + encodeURIComponent(arrayDatas) + "&todo=addNewRow",
            type: "POST",
            data: uploaded,
            processData: false,
            contentType: false,
            success: function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    table.page(table.page.info().page).draw('page');
                    resetForm("#modalNewLineDatatableForm");
                    $("#hiddenReferenceDelUpd").remove();
                    $('#modalNewLineDatatable').modal('toggle');
                }
                notify(back[0], back[1]);
            }
        });
        /*$.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, data: uploaded, todo: "addNewRow"}).done(function (returned) {
         var back = JSON.parse(returned);
         if (back[0] == 1) {
         //table.draw();
         table.page(table.page.info().page).draw('page');
         resetForm("#modalNewLineDatatableForm");
         $("#hiddenReferenceDelUpd").remove();
         $('#modalNewLineDatatable').modal('toggle');
         }
         notify(back[0], back[1]);
         });*/
    });

    $("#modalNewLineDatatableForms").submit(function (e) {
        e.preventDefault();

        var formData = {}, brute = $(this).serializeArray();
        $.each(brute, function (key, value) {
            var k = value["name"].replace("[]", "");
            if (typeof formData[k] === "undefined") // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                formData[k] = value["value"];
            else
                formData[k] += "##" + value["value"];
        });
        var arrayDatas = JSON.stringify(formData);

        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, todo: "addNewRow"}).done(function (returned) {
            var back = JSON.parse(returned);
            if (back[0] == 1) {

                table.page(table.page.info().page).draw('page');
                resetForm("#modalNewLineDatatableForms");
                $("#hiddenReferenceDelUpd").remove();
                $('#modalNewLineDatatable').modal('toggle');
            }
            notify(back[0], back[1]);
        });
    });

    // Enregistrement Signature
    $(document).on("submit", "#modalSignatureARForm", function (e) {
        e.preventDefault();
        var formData = {}, brute = $(this).serializeArray();
        $.each(brute, function (key, value) {
            var k = value["name"].replace("[]", "");
            if (typeof formData[k] === "undefined") {
                // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                formData[k] = value["value"];

            } else
                formData[k] += "##" + value["value"];
        });
        var arrayDatas = JSON.stringify(formData);
        $.ajax({
            url: "/backend/_ajax.php?app=backend&action=recordSignature&datas=" + encodeURIComponent(arrayDatas),
            type: "POST",
            processData: true,
            contentType: false,
            success: function (returned) {
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    table.page(table.page.info().page).draw('page');
                    $('#modalSignatureAR').modal('toggle');
                    notify3(back[1]);
                } else {
                    notify3(back[1], 'error');
                }
            }
        });
    });

    // Cas particulier
    $(document).on("click", "[data-add-by-id]", function () {
        var arrayDatas = JSON.stringify({id: get['id'], idbis: $(this).data('id')});
        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, todo: "addById"}).done(function (returned) {
            var back = JSON.parse(returned);
            if (back[0] == 1) {
                notify3(back[1], 'success');
            } else {
                notify3(back[1], 'error');
            }
            $("#modalNewLineDatatable").modal('toggle');
            table.page(table.page.info().page).draw('page');
        });
    });
    // Making TD editable exept td with action button and classes noteditable modaledit
    $('body').on('dblclick', 'td:not(:has(button),.noteditable,.modaledit)', function () {
        // The cell that has been clicked will be editable
        var el = $(this);
        el.attr('contenteditable', 'true');
        el.focus();
        $(this).blur(function () {
            var lineData = {}, lineDataIndex = {}, counter = 0, tdValue = $(this).closest('tr').children('td');
            $.each(tdValue, function (key, value) {
                lineData[key] = $(this).text();
            });
            var line = table.row($(this).closest('tr')).data();
            $.each(columnsDT, function (key, value) {
                if (typeof value["visible"] !== "undefined") {
                    if (typeof value["className"] !== "undefined" && value["className"].indexOf("row-reference") > -1) {
                        lineDataIndex['referenceDelUpd'] = line[value["data"]];
                    } else
                        lineDataIndex[value["data"]] = line[value["data"]];
                } else {
                    if (typeof value["className"] !== "undefined" && value["className"].indexOf("row-reference") > -1) {
                        lineDataIndex['referenceDelUpd'] = lineData[counter];
                    } else {
                        if (typeof value["className"] === "undefined" || value["className"].indexOf("noteditable") === -1) {
                            lineDataIndex[value["data"]] = lineData[counter];
                        }
                    }
                    counter++;
                }
            });
            if (typeof lineDataIndex['referenceDelUpd'] !== "undefined") {
                var arrayDatas = JSON.stringify(lineDataIndex);
                $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, todo: "updateRow"}).done(function (returned) {
                    var back = JSON.parse(returned);
                    if (back[0] == 1) {
                        if (typeof back[2] !== "undefined" && back[2] === "loadOther") {
                            $(location).attr("href", back[3]);
                        } else {
                            table.page(table.page.info().page).draw('page');
                        }
                    } else {
                        if (typeof back[2] !== "undefined" && back[2] === "focus") {
                            el.css("background-color", "#FFFF99");
                            el.attr('contenteditable', 'true');
                        }
                    }
                    notify(back[0], back[1]);
                });
            }
        });
    });
    // Css for datatable loaded
    $('.datatable-config').each(function () {
        var datatable = $(this);
        // SEARCH - Add the placeholder for Search and Turn this into in-line form control
        var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
        search_input.attr('placeholder', 'Faire une recherche ...');
        //search_input.removeClass('form-control-sm');
        search_input.css("width", "99%");
        // LENGTH - Inline-Form control
        var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
        length_sel.removeClass('form-control-sm');
    });
    // Check all checkbox
    $(document).on('click', '#toggle-check-all-checkbox', function () {
        if (!$(this).is(':checked')) {
            $(document).find(".dt-class-checkbox").prop('checked', false);
        } else {
            $(document).find(".dt-class-checkbox").prop('checked', true);
        }
    });
    // Unchecked "selectionner tout" if on less checkbox unchecked
    $(document).on('click', '.dt-class-checkbox', function () {
        if (!$(this).is(':checked')) {
            $("#toggle-check-all-checkbox").prop('checked', false);
        }
        if ($('.dt-class-checkbox:checked').length === $('.dt-class-checkbox').length) {
            $("#toggle-check-all-checkbox").prop('checked', true);
        }
    });
    // Filter by status
    $(document).on('click', '.aj-filter-showed-datatable-elements, .aj-filter-showed-datatable-elements-by-others', function () {
        var oThis = $(this);
        (oThis.data("value") == "2") ? $("#aj_is_disabled").addClass("disabled") : $("#aj_is_disabled").removeClass("disabled");
        var target = (oThis.is(".aj-filter-showed-datatable-elements-by-others")) ? oThis.data("action") : "statusToShowDatatable";

        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", _ajaxActif: oThis.data("value"), todo: target}).done(function (returned) {
            if (oThis.is(".aj-filter-showed-datatable-elements")) {
                var back = JSON.parse(returned);
                if (typeof back["text"] !== "undefined") {
                    $("#aj_datatable_filter_text_control").removeClass().addClass(back["icon"], function () {
                        $(this).next("span").text(back["text"]);
                    });
                }
            }
            table.page(table.page.info().page).draw('page');
        });
    });
    // Filter by transaction
    $(document).on('click', '.transaction-type-class-checkbox', function () {
        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: {"cVal": $(this).val(), "act": $(this).is(":checked")}, todo: "fieldValueToShowDatatable"}).done(function (returned) {
            table.page(table.page.info().page).draw('page');
        });
    });
    // Actions multiples - Activer, desactiver, suppression multiple
    $(document).on('click', '.action-multiple-datatable-elements', function () {
        var actionToDo = $(this).attr("data-value");
        var msgAction = (actionToDo === "deleteRecordsFromDatatable") ? "suppression définitive" : ((actionToDo === "archiveRecordsFromDatatable") ? "désactivation" : "activation");
        msgAction = (actionToDo === "annulationSignature") ? "<b class='text text-danger'>annulation de signature</b>" : msgAction;
        var message = "Vous allez effectuer une " + msgAction + " ! Continuer ?";
        confirmed('Êtes vous-sûr ?', message, function () {
            var checked = sontCoches("dt-class-checkbox");
            if (checked.length) {
                var jsonChecked = JSON.stringify(checked);
                var _xhrPosted = {app: get["app"], module: get["module"], aparent: get["action"], element: cElement, action: "ajax", datas: jsonChecked, todo: actionToDo};
                saveState(_xhrPosted, "offline-cron");
                $(".loader-for-body-crm").toggle();
                $.get("/backend/index.php", _xhrPosted).done(function (returned) {
                    $(".loader-for-body-crm").fadeOut("bounce");
                    var back = JSON.parse(returned);
                    if (back[0] == 1) {
                        table.page(table.page.info().page).draw('page');
                    }
                    notify(back[0], back[1]);
                });
            } else {
                notify(-1, "Veuillez cocher au moins un élément !");
            }
        });
    });
    // Activer, desactiver, suppression par ligne
    $(document).on('click', '.action-execution-ajax, .action-execution-ajax-in-childrow', function () {
        var oThis = $(this);
        var actionToDo = oThis.attr("data-value");
        var msgAction = (actionToDo === "annulerSaisie") ? "annulation de saisie" : ((actionToDo === "deleteRecordsFromDatatable") ? "suppression définitive" : ((actionToDo === "archiveRecordsFromDatatable") ? "désactivation" : ((actionToDo === "reinitialisationMotDePasse") ? "réinitialisation de mot de passe" : "activation")));
        msgAction = (actionToDo === "annulationSignature") ? "<b class='text text-danger'>annulation de signature</b>" : msgAction;
        var message = "Vous allez effectuer une " + msgAction + " ! Continuer ?";
        confirmed('Êtes vous-sûr ?', message, function () {
            var _ajaxId = (oThis.is(".action-execution-ajax-in-childrow")) ? oThis.attr("data-id") : oThis.closest(".id-datatable-line-for-reference").attr("data-reference-row");
            var _xhrPosted = {app: get["app"], module: get["module"], aparent: get["action"], element: cElement, action: "ajax", datas: JSON.stringify({"id": _ajaxId}), todo: actionToDo};
            saveState(_xhrPosted, "offline-cron");
            $(".loader-for-body-crm").toggle();
            $.get("/backend/index.php", _xhrPosted).done(function (returned) {
                $(".loader-for-body-crm").fadeOut("bounce");
                var back = JSON.parse(returned);
                if (back[0] == 1) {
                    table.page(table.page.info().page).draw('page');
                }
                notify(back[0], back[1]);
            });
        });
    });

    $("#modalNewLineDatatableFormPAsse").submit(function (e) {
        e.preventDefault();
        var formData = {}, brute = $(this).serializeArray();
        $.each(brute, function (key, value) {
            var k = value["name"].replace("[]", "");
            if (typeof formData[k] === "undefined") // Pour controler les checkbox afin de mieux les recuperer sur une chaine dont les donnees sont separees par ## 
                formData[k] = value["value"];
            else
                formData[k] += "##" + value["value"];
        });
        var arrayDatas = JSON.stringify(formData);

        $.get("/backend/index.php", {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: arrayDatas, todo: "addNewPasserelleRow"}).done(function (returned) {
            var back = JSON.parse(returned);
            if (back[0] == 1) {
                table.page(table.page.info().page).draw('page');
                resetForm("#modalNewLineDatatableForms");
                $("#hiddenReferenceDelUpd").remove();
                $('#modalNewLineDatatablePass').modal('toggle');
            }
            notify(back[0], back[1]);
            window.location.reload(true);
        });
    });
    /*/ Activer, desactiver, suppression par ligne avec childRow
     $(document).on('click', '.action-execution-ajax-in-childrow', function () {
     var oThis = $(this);
     var actionToDo = oThis.attr("data-value");
     var msgAction = (actionToDo === "annulerSaisie") ? "annulation de saisie" : ((actionToDo === "deleteRecordsFromDatatable") ? "suppression définitive" : ((actionToDo === "archiveRecordsFromDatatable") ? "désactivation" : ((actionToDo === "reinitialisationMotDePasse") ? "réinitialisation de mot de passe" : "activation")));
     var message = "Vous allez effectuer une " + msgAction + " ! Continuer ?";
     confirmed('Êtes vous-sûr ?', message, function () {
     var _ajaxId = oThis.attr("data-id");
     var _xhrPosted = {app: get["app"], module: get["module"], element: cElement, action: "ajax", datas: JSON.stringify({"id": _ajaxId}), todo: actionToDo};
     saveState(_xhrPosted, "offline-cron");
     $(".loader-for-body-crm").toggle();
     $.get("/backend/index.php", _xhrPosted).done(function (returned) {
     $(".loader-for-body-crm").fadeOut("bounce");
     var back = JSON.parse(returned);
     if (back[0] == 1) {
     table.page(table.page.info().page).draw('page');
     }
     notify(back[0], back[1]);
     });
     });
     });*/
});