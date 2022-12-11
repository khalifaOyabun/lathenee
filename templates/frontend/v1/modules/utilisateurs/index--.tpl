<div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
    <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
        <!--==================================
        =            Content Head            =
        ===================================-->
        {include file="{$template}/modules/configuration/inc/_inc_sous_menu_module.tpl"}
        <!--====  Fin Content Head  ====-->
        <!-- begin:: Content -->
        <div class="kt-container  kt-grid__item kt-grid__item--fluid">
            <div class="kt-portlet kt-portlet--mobile">
                <div class="kt-portlet__head kt-portlet__head--lg row d-flex align-items-center">
                    <div class="kt-portlet__head-label">
                        <span class="kt-portlet__head-icon">
                            <i class="kt-font-brand flaticon2-user"></i>
                        </span>
                        <h3 class="kt-portlet__head-title">
                            {$lang.utilisateurs_liste|upper}
                        </h3>
                    </div>
                    <div>
                        <div class="dropdown dropdown-inline">
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i id="aj_datatable_filter_text_control" class="{$assigned.datatable.filter.icon}"></i> <span>{$assigned.datatable.filter.text}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-left">
                                <ul class="kt-nav">
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="all">
                                            <i class="kt-nav__link-icon la la-file-text-o"></i>
                                            <span class="kt-nav__link-text">{$lang.commun_tous}</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="1">
                                            <i class="kt-nav__link-icon la la-check-circle"></i>
                                            <span class="kt-nav__link-text">{$lang.commun_actifs}</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="-1">
                                            <i class="kt-nav__link-icon la la-file-archive-o"></i>
                                            <span class="kt-nav__link-text">{$lang.commun_archives}</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="2">
                                            <i class="kt-nav__link-icon la la-user-plus"></i>
                                            <span class="kt-nav__link-text">{$lang.commun_encreation}</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <div class="kt-portlet__head-actions">
                                <div class="dropdown dropdown-inline">
                                    <button id="aj_is_disabled" type="button" class="btn btn-danger btn-icon-sm dropdown-toggle {if isset($assigned.datatable.filter.disabled)}{$assigned.datatable.filter.disabled}{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="flaticon2-graphic-1"></i> {$lang.commun_actions_multiples}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <ul class="kt-nav">
                                            <li class="kt-nav__section kt-nav__section--first">
                                                <span class="kt-nav__section-text">{$lang.commun_choisir_une_action}</span>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="activeRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-check-circle"></i>
                                                    <span class="kt-nav__link-text">{$lang.commun_activer}</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="archiveRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-archive"></i>
                                                    <span class="kt-nav__link-text">{$lang.commun_archiver}</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="deleteRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-trash"></i>
                                                    <span class="kt-nav__link-text">{$lang.commun_supprimer}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet__body mt-lg-5">
                    <div class="loader-for-body-crm" style="display: none;">
                        <h1>{$lang.commun_msg_attente_execution}</h1>
                    </div>
                    <table id="datatable-configuration" class="table table-striped table-bordered table-hover table-checkable datatable-config">
                        <thead class="bg-primary">
                            <tr id="headerDatatable">
                                <th class="" width="5px">
                                    <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid kt-checkbox--bold">
                                        <input type="checkbox" id="toggle-check-all-checkbox">
                                        <span></span>
                                    </label>
                                </th>
                                <th class="text-white">{$lang.commun_txt_nom_complet}</th>
                                <th class="text-white">{$lang.utilisateurs_type_user}</th>
                                <th class="text-white">{$lang.utilisateurs_adresse_email}</th>
                                <th class="text-white">{$lang.utilisateurs_login}</th>
                                <th class="text-white">{$lang.utilisateurs_agence}</th>
                                <th class="text-white">{$lang.utilisateurs_societe}</th>
                                <th class="text-white" width="auto">{$lang.commun_txt_statut}</th>
                                <th class="text-white dt-center" width="15px"><i class="flaticon-settings"></i></th>
                            </tr>
                        </thead>
                    </table>
                    <!--end: Datatable -->
                </div>
            </div>
        </div>
        <!-- end:: Content -->
    </div>
</div>