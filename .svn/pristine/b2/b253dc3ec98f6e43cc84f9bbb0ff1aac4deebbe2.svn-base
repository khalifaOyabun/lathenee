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
                    <div class="kt-portlet__head-toolbar">
                        <div class="kt-portlet__head-wrapper">
                            <div class="kt-portlet__head-actions">
                                <div class="dropdown dropdown-inline">
                                    <button id="aj_is_disabled" type="button" class="btn btn-danger btn-icon-sm dropdown-toggle {if isset($assigned.datatable.filterdisabled)}{$assigned.datatable.filterdisabled}{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                <div class="kt-portlet kt-portlet--tabs">                
                    {include file="{$template}/modules/{$get.module}/inc/_inc_utilisateurs_liste.tpl"}
                </div>
            </div>
        </div>
        <!-- end:: Content -->
    </div>
</div>