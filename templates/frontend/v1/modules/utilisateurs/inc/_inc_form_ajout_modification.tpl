<div class="kt-portlet__head">
    <div class="kt-portlet__head-toolbar">
        <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
            <li class="nav-item">
                <a class="nav-link {if !isset($assigned.tab.actived) || ($assigned.tab.actived eq "1")}active{/if}" onclick='isCurrent({$get|json_encode}, 1);
                        $(".leave-out-execution-ajax-form").addClass("hide");' data-toggle="tab" href="#kt_portlet_base_demo_2_1_tab_content" role="tab" aria-selected="true">
                    <i class="flaticon-user" aria-hidden="true"></i>{$lang.commun_txt_filiation_coordonnees}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {if isset($assigned.tab.actived) && $assigned.tab.actived eq "2"}active{else}{if !isset($assigned.tab.enabled) || ($assigned.tab.enabled lt 1)}disabled{/if}{/if}" onclick='isCurrent({$get|json_encode}, 2);
                        $(".leave-out-execution-ajax-form").addClass("hide");' data-toggle="tab" href="#kt_portlet_base_demo_2_2_tab_content" role="tab" aria-selected="false">
                    <i class="flaticon-lock" aria-hidden="true"></i>{$lang.commun_txt_infos_connexion}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {if isset($assigned.tab.actived) && $assigned.tab.actived eq "3"}active{else}{if !isset($assigned.tab.enabled) || ($assigned.tab.enabled lt 2)}disabled{/if}{/if}" onclick='isCurrent({$get|json_encode}, 3);
                        $(".leave-out-execution-ajax-form").addClass("hide");' data-toggle="tab" href="#kt_portlet_base_demo_2_3_tab_content" role="tab" aria-selected="false">
                    <i class="flaticon-avatar" aria-hidden="true"></i>{$lang.commun_txt_photo_profil}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {if isset($assigned.tab.actived) && $assigned.tab.actived eq "4"}active{else}{if !isset($assigned.tab.enabled) || ($assigned.tab.enabled lt 3)}disabled{/if}{/if}" onclick='isCurrent({$get|json_encode}, 4);
                        $(window).off("beforeunload");
                        $(".leave-out-execution-ajax-form").removeClass("hide");' data-toggle="tab" href="#kt_portlet_base_demo_2_4_tab_content" role="tab" aria-selected="false">
                    <i class="flaticon-map-location" aria-hidden="true"></i>{$lang.commun_txt_dept_autorises}
                </a>
            </li>
        </ul>
    </div>
    <div class="kt-portlet__head--right">
        <button onclick='$(window).off("beforeunload");
                redirection("{$smarty.const.HTTP_PATH}/{$get.module}/liste");' class="btn btn-outline-primary btn-sm btn-icon btn-pill btn-icon-md mt-3 leave-out-execution-ajax-form cursor-pointer {if !isset($assigned.tab.actived) || $assigned.tab.actived neq "4"} hide {/if}" title="Quitter">
            <i class="kt-nav__link-icon la la-close"></i>
        </button>
        {if isset($get.action) && $get.action eq "ajout"}
            <button class="btn btn-sm btn-icon btn-outline-danger btn-pill btn-icon-md mt-3 action-execution-ajax-form cursor-pointer" data-value="annulerSaisieCourante" title="{$lang.commun_annuler_saisie}">
                <i class="kt-nav__link-icon flaticon2-trash"></i>
            </button>
        {/if}
    </div>
</div>
<div class="kt-portlet__body">                   
    <div class="tab-content">
        <div class="tab-pane {if !isset($assigned.tab.actived) || ($assigned.tab.actived eq "1")}active{/if}" id="kt_portlet_base_demo_2_1_tab_content" role="tabpanel">
            <br />
            <form enctype="multipart/form-data" action="" method="POST" class='aj-form-section-tab' data-value="1">
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label for="code_civilite">{$lang.commun_txt_civilite}</label>
                        <select class="form-control" id="code_civilite" name="code_civilite">
                            {foreach from=$assigned.elemForm.civilite item=civilite}
                                <option value="{$civilite->code}" {if isset($assigned.donnees.code_civilite) && $assigned.donnees.code_civilite eq $civilite->code}selected{/if}>{$civilite->libelle}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="nom">{$lang.commun_txt_nom}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input {if isset($assigned.donnees.nom)} value="{$assigned.donnees.nom}" {/if} type="text" class="form-control" id="nom" name="nom" placeholder="{$lang.commun_txt_nom} {$lang.utilisateurs_txt_delutilisateur}" required="required" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-user"></i></span>
                            </span>
                        </div>
                        <em class="text-danger">{$lang.commun_txt_requis}</em>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="formControlSelect1">{$lang.commun_txt_prenom}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="text" class="form-control" name="prenom" id="prenom" placeholder="{$lang.commun_txt_prenom} {$lang.utilisateurs_txt_delutilisateur}" {if isset($assigned.donnees.prenom)} value="{$assigned.donnees.prenom}"{/if} />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-user"></i></span>
                            </span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label for="adresse">{$lang.commun_txt_adresse}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input {if isset($assigned.donnees.adresse)} value="{$assigned.donnees.adresse}"{/if} type="text" class="form-control" name="adresse" id="adresse" placeholder="{$lang.commun_txt_adresse} {$lang.utilisateurs_txt_delutilisateur}" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-street-view"></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="cp">{$lang.commun_txt_cp}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input class="form-control" type="text" name="cp" {if isset($assigned.donnees.cp)} value="{$assigned.donnees.cp}" {/if} id="cp" onkeyup='loadDocument("{$smarty.const.HTTP_PATH}/backend/_ajax.php?module=liste-des-agences&source=loadVilleParcpordept&cp=" + this.value, "id_ville", "", "");' placeholder="{$lang.commun_txt_cp} {$lang.utilisateurs_txt_delutilisateur}" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-list-ol"></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="id_ville">{$lang.commun_txt_ville}</label>
                        <select name="id_ville" class="form-control" id="id_ville">
                            {if isset($assigned.listes.villes)}
                                {foreach from=$assigned.listes.villes item=villes}
                                    <option value="{$villes->id}#{$villes->code_postal}" {if isset($assigned.donnees.id_ville) && $assigned.donnees.id_ville eq $villes->id}selected{/if}>{$villes->nom} ({$villes->code_postal})</option>
                                {/foreach}
                            {else}
                                <option value="">{$lang.commun_txt_ville} {$lang.utilisateurs_txt_delutilisateur}</option>
                            {/if}
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="telephone">{$lang.commun_txt_telephone}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input {if isset($assigned.donnees.telephone)} value="{$assigned.donnees.telephone}"{/if} type="tel" class="form-control" name="telephone" id="telephone" onkeypress="verifIntPointSpace();" placeholder="{$lang.commun_txt_telephone} {$lang.utilisateurs_txt_delutilisateur}" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-phone"></i></span>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label for="portable">{$lang.commun_txt_portable}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="tel" onkeypress="verifIntPointSpace();" {*literal}pattern="0[1-68]([-. ]?[0-9]{2}){4}"{/literal*} class="form-control phone" name="portable" id="portable" placeholder="{$lang.commun_txt_portable} {$lang.utilisateurs_txt_delutilisateur}" {if isset($assigned.donnees.portable)} value="{$assigned.donnees.portable}" {/if} />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-phone"></i></span>
                            </span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                        <button class="btn btn-xs btn-block col-sm-5 {if isset($get.action) && $get.action eq "ajout"} btn-primary aj-form-current-tab-save {else} btn-dark aj-form-current-tab-terminer {/if}">{if isset($get.action) && $get.action eq "ajout"}<span class="flaticon2-download"></span>&nbsp;&nbsp;{$lang.commun_txt_sauvegarder}{else}<span class="flaticon2-check-mark"></span>&nbsp;&nbsp;{$lang.commun_txt_terminer}{/if}</button>
                    </div>
                    <div class="col-sm-8">
                        <button class="btn btn-outline-info btn-xs btn-block aj-form-current-tab-continue col-sm-1 pull-right" title="{$lang.commun_txt_suivant}"><span class="flaticon2-next"></span>{*$lang.commun_txt_suivant*}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane {if isset($assigned.tab.actived) && $assigned.tab.actived eq "2"}active{/if}" id="kt_portlet_base_demo_2_2_tab_content" role="tabpanel">
            <br />
            <form enctype="multipart/form-data" action="" method="POST" class='aj-form-section-tab' data-value="2">
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label for="email">{$lang.utilisateurs_adresse_email}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input {if isset($assigned.donnees.email)} value="{$assigned.donnees.email}" {/if} type="email" class="form-control" id="email" name="email" placeholder="{$lang.utilisateurs_adresse_email} {$lang.utilisateurs_txt_delutilisateur}" required="required" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-envelope"></i></span>
                            </span>
                        </div>
                        <em class="text-danger">{$lang.commun_txt_requis}</em>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="login">{$lang.utilisateurs_login}</label>
                        <div class="kt-input-icon kt-input-icon--left">
                            <input type="text" class="form-control" name="login" id="login" placeholder="{$lang.utilisateurs_login} {$lang.utilisateurs_txt_delutilisateur}" {if isset($assigned.donnees.login)} value="{$assigned.donnees.login}"{/if} required="required" />
                            <span class="kt-input-icon__icon kt-input-icon__icon--left">
                                <span><i class="la la-user"></i></span>
                            </span>
                        </div>
                        <em class="text-danger">{$lang.commun_txt_requis}</em>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col form-group">
                        <label for="type_utilisateur">{$lang.utilisateurs_type_user}</label>
                        <select class="form-control" id="code_type_utilisateur" name="code_type_utilisateur" required='required'>
                            <option value=''>{$lang.utilisateurs_type_user}</option>
                            {foreach from=$assigned.elemForm.type_utilisateur item=type}
                                {if !($get.isAdmin && $type->code eq "SUPERADMINISTRATEUR")}
                                    <option value="{$type->code}" {if isset($assigned.donnees.code_type_utilisateur) && $assigned.donnees.code_type_utilisateur eq $type->code}selected{/if}>{$type->libelle}</option>
                                {/if}
                            {/foreach}
                        </select>
                        <em class="text-danger">{$lang.commun_txt_requis}</em>
                    </div>
                    {if isset($get.isSuper) && $get.isSuper}
                        <div class="col form-group">
                            <label for="id_agence">{$lang.utilisateurs_agence}</label>
                            <select class="form-control" id="id_agence" name="id_agence" required='required'>
                                <option value=''>{$lang.utilisateurs_agence} {$lang.utilisateurs_txt_delutilisateur}</option>
                                {foreach from=$assigned.listes.agences item=agences}
                                    <option value="{$agences->id}" {if isset($assigned.donnees.id_agence) && $assigned.donnees.id_agence eq $agences->id}selected{/if}>{$agences->raison_sociale}</option>
                                {/foreach}
                            </select>
                            <em class="text-danger">{$lang.commun_txt_requis}</em>
                        </div>
                    {else}
                        <input type="hidden" name="id_agence" id="id_agence" {if isset($smarty.session.{$smarty.const._USER_}->id_agence)} value="{$smarty.session.{$smarty.const._USER_}->id_agence}"{/if}>
                    {/if}
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-2">
                        <button class="btn btn-xs btn-block {if isset($get.action) && $get.action eq "ajout"} btn-primary aj-form-current-tab-save {else} btn-dark aj-form-current-tab-terminer {/if}">{if isset($get.action) && $get.action eq "ajout"}<span class="flaticon2-download"></span>&nbsp;&nbsp;{$lang.commun_txt_sauvegarder}{else}<span class="flaticon2-check-mark"></span>&nbsp;&nbsp;{$lang.commun_txt_terminer}{/if}</button>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-outline-info btn-xs btn-block col-sm-4 aj-form-back-tab-state " title="{$lang.commun_txt_precedent}"><span class="flaticon2-back"></span>{*$lang.commun_txt_suivant*}</button>
                    </div>
                    <div class="col-sm-8">
                        <button class="btn btn-outline-info btn-xs btn-block aj-form-current-tab-continue col-sm-1 pull-right" title="{$lang.commun_txt_suivant}"><span class="flaticon2-next"></span>{*$lang.commun_txt_suivant*}</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane {if isset($assigned.tab.actived) && $assigned.tab.actived eq "3"}active{/if}" id="kt_portlet_base_demo_2_3_tab_content" role="tabpanel">
            <br />
            <form class="kt-form kt-form--label-right aj-form-section-tab" enctype="multipart/form-data" action="" method="POST" data-value="3" id='test'>
                <div class="kt-portlet__body">
                    <div class="form-group row">
                        <label class="col-xl-5 col-lg-5 col-form-label">&nbsp;{$lang.commun_txt_charger_avatar}</label>
                        <div class="col-lg-4 col-xl-4">
                            <div class="kt-avatar kt-avatar--outline kt-avatar--circle {if isset($assigned.donnees.avatar) && !empty($assigned.donnees.avatar)}kt-avatar--changed{/if}" id="kt_user_avatar_3">
                                <div class="kt-avatar__holder" style="background-image: url(&quot;{if isset($assigned.donnees.avatar) && !empty($assigned.donnees.avatar)}{$assigned.donnees.avatar}{else}{$smarty.const.MED_PATH_BE}/users/avatar-default-icon.png{/if}&quot;);"></div>
                                <span class="kt-avatar__holder_path-to-default-avatar hide" style="background-image: url(&quot;{$smarty.const.MED_PATH_BE}/users/avatar-default-icon.png&quot;);"></span>
                                <label class="kt-avatar__upload" data-toggle="kt-tooltip" title="" data-original-title="Change avatar">
                                    <i class="flaticon-edit"></i>
                                    <input type="file" id="avatar" name="avatar" accept=".{$assigned.images.accept|implode:', .'}">
                                </label>
                                <span class="kt-avatar__cancel" data-toggle="kt-tooltip" title="" data-original-title="Cancel avatar">
                                    <i class="flaticon-circle"></i>
                                </span>
                            </div>
                        </div>
                    </div> 
                    <center>
                        <b class="form-text text-danger"><em class="flaticon-warning"> {$lang.commun_msg_formats_autorises}</em></b>
                    </center>
                </div>
                <div class="row">
                    <div class="col-sm-2">
                        <button class="btn btn-dark btn-xs btn-block aj-form-current-tab-terminer"><span class="flaticon2-check-mark"></span>&nbsp;&nbsp;{$lang.commun_txt_terminer}</button>
                    </div>
                    <div class="col-sm-2">
                        <button class="btn btn-outline-info btn-xs btn-block col-sm-4 aj-form-back-tab-state " title="{$lang.commun_txt_precedent}"><span class="flaticon2-back"></span>{*$lang.commun_txt_suivant*}</button>
                    </div>
                    {if isset($get.action) && $get.action eq "ajout"}
                        <div class="col-sm-8">
                            <button class="btn btn-outline-primary btn-xs btn-block aj-form-current-tab-terminer-configurer col-sm-2 pull-right"><span class="flaticon-settings-1"></span>&nbsp;&nbsp;{$lang.commun_txt_configurer}</button>
                        </div>
                    {/if}
                </div>
            </form>
        </div>
        <div class="tab-pane {if isset($assigned.tab.actived) && $assigned.tab.actived eq "4"}active{/if}" id="kt_portlet_base_demo_2_4_tab_content" role="tabpanel">
            <br />
            <form class="aj-form-section-tab" method="POST" data-value="4">
                <div class="row">
                    {foreach from=$assigned.listes.departements item=departement}
                        <div class="form-group form-group-xs row col-sm-3">
                            <div class="col-2">
                                <span class="kt-switch kt-switch--brand kt-switch--sm kt-switch--icon">
                                    <label>
                                        <input onclick='loadDocument("{$smarty.const.HTTP_PATH}/backend/index.php?module={$get.module}&action=ajax&app={$get.app}&todo=upatingUserDepartments&_id=" + document.getElementById("id").value + "&_dept=" + this.value, "", "", "", "", "", false);' type="checkbox" value="{$departement->num}" {if isset($assigned.donnees.departements_autorises)}{if in_array($departement->num, $assigned.donnees.departements_autorises)}checked="checked"{/if}{/if}>
                                        <span></span>
                                        <input type="hidden" name="formType" value="departement">
                                    </label>
                                </span>
                            </div>
                            <label class="col-10 col-form-label">({$departement->num}) {$departement->nom}</label>
                        </div>
                    {/foreach}
                    <input type="hidden" name="id" id="id" {if isset($assigned.donnees.id)} value="{$assigned.donnees.id}"{/if}>
                </div>
            </form>
        </div>
    </div>
</div>
