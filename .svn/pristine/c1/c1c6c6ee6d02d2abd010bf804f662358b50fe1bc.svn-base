<div class="kt-portlet slider">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <span class="kt-portlet__head-icon">
                <i class="flaticon-information"></i>
            </span>
            <h3 class="kt-portlet__head-title">
                {$lang.commun_txt_plus_dinfos}
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar mt-3">
            <div class="kt-portlet__head-actions">
                <a href="{$smarty.const.HTTP_PATH}/utilisateurs/details/{$childAssigned->id|base64_encode}" data-toggle="kt-tooltip" data-placement="right" title="Voir tous les détails de l'utilisateur" class="btn btn-sm btn-outline-brand btn-elevate-hover btn-circle btn-icon mr-1">
                    <i class="kt-nav__link-icon flaticon2-expand"></i>
                </a>
                {if $childAssigned->actif eq 1}
                    {if (isset($isSuper) && $isSuper) || ($smarty.session.{$smarty.const._USER_}->id eq $childAssigned->id) || ($childAssigned->code_type_utilisateur eq "NEGOCIATEUR")}
                        <a href="{$smarty.const.HTTP_PATH}/utilisateurs/modification/{$childAssigned->id|base64_encode}" data-toggle="kt-tooltip" data-placement="right" title="Modifier l'utilisateur" class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1">
                            <i class="flaticon-edit"></i>
                        </a>
                    {/if}
                    <a href="#" data-toggle="kt-tooltip" data-placement="right" title="Créer une alerte pour l'utilisateur" class="btn btn-sm btn-outline-brand btn-elevate-hover btn-circle btn-icon mr-1">
                        <i class="kt-nav__link-icon flaticon-bell"></i>
                    </a>
                    <a href="#" data-toggle="kt-tooltip" data-placement="right" title="Envoyer un courriel à l'utilisateur" class="btn btn-sm btn-outline-brand btn-elevate-hover btn-circle btn-icon mr-1">
                        <i class="kt-nav__link-icon flaticon2-send"></i>
                    </a>
                    <span onclick='resetForm("#add-note-form");$("#id_concerne").val($(this).data("id"));
                            loadNote("{$childAssigned->id}", "{$childAssigned->id_agence}");' data-toggle="kt-tooltip" data-placement="right" title="Ajouter une note" class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1 cursor-pointer aj-load-note kt_quick_panel_toggler_btn_note_class" data-id="{$childAssigned->id}">
                        <i class="kt-nav__link-icon flaticon2-talk"></i>
                    </span>
                    {if (isset($isSuper) && $isSuper) || ($smarty.session.{$smarty.const._USER_}->id eq $childAssigned->id) || ($childAssigned->code_type_utilisateur eq "NEGOCIATEUR")}
                        <button  data-toggle="kt-tooltip" data-placement="right" title='{$lang.commun_archiver}' class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1 action-execution-ajax-in-childrow" data-value='archiveRecordsFromDatatable' data-id="{$childAssigned->id}">
                            <i class="kt-nav__link-icon flaticon2-box-1"></i>
                        </button>
                        <button  data-toggle="kt-tooltip" data-placement="right" title="Réinitialiser le mot de passe" class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1 action-execution-ajax-in-childrow" data-value='reinitialisationMotDePasse' data-id="{$childAssigned->id}">
                            <i class="kt-nav__link-icon flaticon-refresh"></i>
                        </button>
                    {/if}
                {else}
                    {if (isset($isSuper) && $isSuper) || ($smarty.session.{$smarty.const._USER_}->id eq $childAssigned->id) || ($childAssigned->code_type_utilisateur eq "NEGOCIATEUR")}
                        <button  data-toggle="kt-tooltip" data-placement="right" title='{$lang.commun_activer}' class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1 action-execution-ajax-in-childrow" data-value='activeRecordsFromDatatable' data-id="{$childAssigned->id}">
                            <i class="kt-nav__link-icon flaticon2-open-box"></i>
                        </button>
                        <button  data-toggle="kt-tooltip" data-placement="right" title='{$lang.commun_supprimer}' class="btn btn-sm btn-outline-danger btn-elevate-hover btn-circle btn-icon mr-1 action-execution-ajax-in-childrow" data-value='deleteRecordsFromDatatable' data-id="{$childAssigned->id}">
                            <i class="kt-nav__link-icon flaticon2-rubbish-bin-delete-button"></i>
                        </button>
                    {/if}
                {/if}
            </div>
        </div>
    </div>
    <div class="kt-portlet__body">
        <div class="kt-section">
            <div class="kt-section__content kt-section__content--solid">
                <div class="row">
                    <div class="col-lg-6">
                        <ul style="list-style-type:none;">
                            <li class="text text-dark mt-2 mb-2"><b><span class="fa fa-caret-right"></span>&nbsp;&nbsp;<u>{$lang.commun_txt_date_creation}</u> </b> <span class="pull-right">{$childAssigned->date_creation}</span></li>
                            <li class="text text-dark mb-2"><b><span class="fa fa-caret-right"></span>&nbsp;&nbsp;<u>{$lang.commun_txt_date_modification}</u> </b> <span class="pull-right">{$childAssigned->date_modification}</span></li>
                            <li class="text text-dark mb-2"><b><span class="fa fa-caret-right"></span>&nbsp;&nbsp;<u>{$lang.commun_txt_adresse}</u> </b> <span class="pull-right">{$childAssigned->adresse} {$childAssigned->cp} {$childAssigned->ville}</span></li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <ul style="list-style-type:none;">
                            <li class="text text-dark mt-2 mb-2"><b><span class="fa fa-caret-right"></span>&nbsp;&nbsp;<u>{$lang.commun_txt_telephone}</u> </b> <span class="pull-right">{$childAssigned->telephone}</span></li>
                            <li class="text text-dark mb-2"><b><span class="fa fa-caret-right"></span>&nbsp;&nbsp;<u>{$lang.commun_txt_portable}</u> </b> <span class="pull-right">{$childAssigned->portable}</span></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        {if $childAssigned->departements_autorises|count gt 0}
            <div class="row">
                <div class="col-lg-12">
                    <div class="kt-section">
                        <div class="kt-section__content kt-section__content--solid">
                            <ul style="list-style-type:none;">
                                <li class="text text-dark"><span class="flaticon-map-location"></span><b>&nbsp;&nbsp;<u>{$lang.commun_txt_dept_autorises}</u></b><br /> 
                                            {foreach from=$childAssigned->departements_autorises item=departement}
                                        <span class="mt-2 btn btn-bold btn-sm btn-font-sm  btn-label-primary">{$departement->nom}</span>
                                    {/foreach}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>