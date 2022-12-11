<div class="container-scroller">

    <div class="kt-body kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor kt-grid--stretch" id="kt_body">
        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor mb-5" id="kt_content">

            <!--==================================
            =            Content Head            =
            ===================================-->
            {include file="{$template}/modules/configuration/inc/_inc_sous_menu_module.tpl"}
            <!--====  Fin Content Head  ====-->

            <!-- begin:: Content -->
            <div class="kt-container kt-grid__item kt-grid__item--fluid">
                <div class="mb-4">
                    <h1 class="h4 text-center"><i class="fa fa-user-plus text-primary"></i>&nbsp; {$assigned.titreForm}</h1>
                    <div class="kt-divider">
                        <span></span>
                        <span><span class=" flaticon2-arrow-down "></span></span>
                        <span></span>
                    </div>
                </div>
                <div class="kt-portlet kt-portlet--tabs">  
                    <div class="loader-for-body-crm" style="display: none;">
                        <h1>{$lang.commun_msg_save_definitive_encours}</h1>
                    </div>
                    <div class="tab-content">
                        {include file="{$template}/modules/{$get.module}/inc/_inc_form_ajout_modification.tpl"}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

