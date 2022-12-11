<div class="kt-portlet__body kt-portlet__body--fit">
    <div class="kt-widget30" id="kt_earnings_widget">
        <div class="kt-widget30__head">
            <div class="owl-carousel">
                <div class="carousel aj-filter-showed-datatable-elements" data-value="1"><span>Actif</span><span>Utilisateurs actifs</span></div>
                <div class="carousel aj-filter-showed-datatable-elements" data-value="-1"><span>Archivé</span><span>Utilisateurs archivés</span></div>
                <div class="carousel aj-filter-showed-datatable-elements" data-value="2"><span>En création</span><span>Utilisateurs en cours de création</span></div>
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
                <th class="text-white" width="auto">{$lang.commun_txt_statut}</th>
                <th class="text-white dt-center" width="15px"><i class="flaticon-settings"></i></th>
            </tr>
        </thead>
    </table>
</div>
