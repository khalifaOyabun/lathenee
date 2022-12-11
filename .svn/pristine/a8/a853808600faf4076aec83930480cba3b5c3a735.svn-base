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
                            <i class="kt-font-brand flaticon-list-3"></i>
                        </span>
                        <h3 class="kt-portlet__head-title">
                            {$assigned.titreAction|upper}
                        </h3>
                    </div>
                    <div>
                        <div class="dropdown dropdown-inline">
                            <button id="aj_is_disabled" type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i id="aj_datatable_filter_text_control" class="{$assigned.datatable.filter.icon}"></i> <span>{$assigned.datatable.filter.text}</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-left">
                                <ul class="kt-nav">
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="all">
                                            <i class="kt-nav__link-icon la la-file-text-o"></i>
                                            <span class="kt-nav__link-text">Tous</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="1">
                                            <i class="kt-nav__link-icon la la-eye"></i>
                                            <span class="kt-nav__link-text">Visible</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a class="kt-nav__link aj-filter-showed-datatable-elements" data-value="-1">
                                            <i class="kt-nav__link-icon la la-eye-slash"></i>
                                            <span class="kt-nav__link-text">Non visible</span>
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
                                    <button type="button" class="btn btn-info btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="flaticon-refresh"></i> Types transaction
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <ul class="kt-nav">
                                            {foreach from=$assigned.elemForm item=transaction key=k}
                                                <li class="kt-nav__item">
                                                    <a class="kt-nav__link" >
                                                        <label class='kt-checkbox kt-checkbox--solid'>
                                                            <input type='checkbox' class='transaction-type-class-checkbox' value="{$k}" {if isset($assigned.datatable.transaction.choosen) && ($k|in_array:$assigned.datatable.transaction.choosen)}checked{/if}><span></span>
                                                            {$transaction.libelle}
                                                        </label>
                                                    </a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                </div>
                                <div class="dropdown dropdown-inline">
                                    <button id="aj_is_disabled" type="button" class="btn btn-danger btn-icon-sm dropdown-toggle {if isset($assigned.datatable.filter.disabled)}{$assigned.datatable.filter.disabled}{/if}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="flaticon2-graphic-1"></i> Actions multiples
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-left">
                                        <ul class="kt-nav">
                                            <li class="kt-nav__section kt-nav__section--first">
                                                <span class="kt-nav__section-text">Choisir une action</span>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="visibleRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon far fa-eye-slash text-success"></i>
                                                    <span class="kt-nav__link-text">Afficher</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="invisibleRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon far fa-eye text-danger"></i>
                                                    <span class="kt-nav__link-text">Masquer</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="activeRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-check-circle"></i>
                                                    <span class="kt-nav__link-text">Activer</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="archiveRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-archive"></i>
                                                    <span class="kt-nav__link-text">Désactiver</span>
                                                </a>
                                            </li>
                                            <li class="kt-nav__item">
                                                <a class="kt-nav__link action-multiple-datatable-elements" data-value="deleteRecordsFromDatatable">
                                                    <i class="kt-nav__link-icon la la-trash"></i>
                                                    <span class="kt-nav__link-text">Supprimer</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <button onclick='popupSwal("select", {$assigned.liste.champs|json_encode}, "{$smarty.const.HTTP_PATH}/{$get.module}/ajout");' class="btn btn-success kt-margin-l-10">
                                    <i class="la la-plus"></i> {$lang.commun_nouvelle_entree}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-portlet kt-portlet--tabs">
                    <div class="kt-portlet__head py-3">
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-brand nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active btn btn-light btn-elevate px-4 aj-filter-showed-datatable-elements-by-others" data-toggle="tab" href="#kt_portlet_base_champ_vues" role="tab" aria-selected="true" data-value="1" data-action="otherToShowDatatable">
                                        <div>Vue <br> <small class="font-weight-bold">(Champs pour les vues)</small> </div> 
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link btn btn-light btn-elevate px-4 aj-filter-showed-datatable-elements-by-others" data-toggle="tab" href="#kt_portlet_base_champ_vues" role="tab" aria-selected="true" data-value="2" data-action="otherToShowDatatable">
                                        <div>Impression <br> <small class="font-weight-bold">(Champs pour les impressions)</small> </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="loader-for-body-crm" style="display: none;">
                            <h1>Exécution de la requête en cours. Veuillez patienter ...</h1>
                        </div>
                        <h2 class="h4 text-center mb-4">Ce titre change en fonction du tableau actif</h2>
                        <!--begin: Datatable -->
                        <table id="datatable-configuration" class="table table-striped table-bordered table-hover table-checkable datatable-config">
                            <thead class="bg-primary">
                                <tr id="headerDatatable">
                                    <th class="" width="5px">
                                        <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid kt-checkbox--bold">
                                            <input type="checkbox" id="toggle-check-all-checkbox">
                                            <span></span>
                                        </label>
                                    </th>
                                    <th class="text-white">Libelle Champ (Nom)</th>
                                    <th class="text-white">Transaction</th>
                                    <th class="text-white">Type form champ</th>
                                    <th class="text-white">Source</th>
                                    <th class="text-white">Donnees</th>
                                    <th class="text-white">Famille</th>
                                    <th class="text-white">Visibilite</th>
                                    <th class="text-white">Statut</th>
                                    <th class="text-white dt-center" width="5px"><i class="flaticon-settings"></th>
                                </tr>
                            </thead>
                        </table>
                        <!--end: Datatable -->
                    </div>
                </div>
                {* <div class="kt-portlet kt-portlet--tabs"> 
                <div class="kt-portlet__body kt-portlet__body--fit">
                <div class="kt-widget30" id="kt_earnings_widget">
                <div class="kt-widget30__head">
                <div class="owl-carousel">
                <div class="carousel aj-filter-showed-datatable-elements-by-others" data-value="1" data-action="otherToShowDatatable"><span>Vue</span><span>Champs pour les vues</span></div>
                <div class="carousel aj-filter-showed-datatable-elements-by-others" data-value="2" data-action="otherToShowDatatable"><span>Impression</span><span>Champs pour les impression</span></div>
                </div>
                </div>
                </div>
                </div>
                <div class="kt-portlet__body mt-lg-5">    
                <div class="loader-for-body-crm" style="display: none;">
                <h1>Exécution de la requête en cours. Veuillez patienter ...</h1>
                </div>
                <!--begin: Datatable -->
                <table id="datatable-configuration" class="table table-striped table-bordered table-hover table-checkable datatable-config">
                <thead class="bg-primary">
                <tr id="headerDatatable">
                <th class="" width="5px">
                <label class="kt-checkbox kt-checkbox--single kt-checkbox--solid kt-checkbox--bold">
                <input type="checkbox" id="toggle-check-all-checkbox">
                <span></span>
                </label>
                </th>
                <th class="text-white">Libelle Champ (Nom)</th>
                <th class="text-white">Transaction</th>
                <th class="text-white">Type form champ</th>
                <th class="text-white">Source</th>
                <th class="text-white">Donnees</th>
                <th class="text-white">Famille</th>
                <th class="text-white">Visibilite</th>
                <th class="text-white">Statut</th>
                <th class="text-white dt-center" width="5px"><i class="flaticon-settings"></th>
                </tr>
                </thead>
                </table>
                <!--end: Datatable -->
                </div>
                </div> *}
            </div>
        </div>
        <!-- end:: Content -->
    </div>
</div>