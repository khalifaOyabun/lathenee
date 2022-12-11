

<!--begin:: Widgets/Applications/User/Profile1-->
<div class="kt-portlet kt-portlet--height-fluid-">
    <div class="kt-portlet__head  kt-portlet__head--noborder">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
            </h3>
        </div>
        <div class="kt-portlet__head-toolbar">
            <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown">
                <i class="flaticon-more-1"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

                <!--begin::Nav-->
                <ul class="kt-nav">
                    <li class="kt-nav__head">
                        Actions sur l'utilisateur
                        <!--span data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon kt-svg-icon--brand kt-svg-icon--md1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <circle fill="#000000" opacity="0.3" cx="12" cy="12" r="10" />
                                    <rect fill="#000000" x="11" y="10" width="2" height="7" rx="1" />
                                    <rect fill="#000000" x="11" y="7" width="2" height="2" rx="1" />
                                </g>
                            </svg> </span-->
                    </li>
                    <li class="kt-nav__separator"></li>
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-drop"></i>
                            <span class="kt-nav__link-text">Modifier</span>
                        </a>
                    </li>
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-calendar-8"></i>
                            <span class="kt-nav__link-text">Archiver</span>
                        </a>
                    </li>
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
                            <span class="kt-nav__link-text">Activer</span>
                        </a>
                    </li>
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-new-email"></i>
                            <span class="kt-nav__link-text">Supprimer</span>
                            <!--span class="kt-nav__link-badge">
                                <span class="kt-badge kt-badge--success kt-badge--rounded">5</span>
                            </span-->
                        </a>
                    </li>
                    <li class="kt-nav__item">
                        <a href="#" class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-telegram-logo"></i>
                            <span class="kt-nav__link-text">Réinitialiser mot de passe</span>
                        </a>
                    </li>
                    <!--li class="kt-nav__separator"></li>
                    <li class="kt-nav__foot">
                        <a class="btn btn-label-danger btn-bold btn-sm" href="#">Upgrade plan</a>
                        <a class="btn btn-clean btn-bold btn-sm" href="#" data-toggle="kt-tooltip" data-placement="right" title="Click to learn more...">Learn more</a>
                    </li-->
                </ul>

                <!--end::Nav-->
            </div>
        </div>
    </div>
    <div class="kt-portlet__body kt-portlet__body--fit-y">

        <!--begin::Widget -->
        <div class="kt-widget kt-widget--user-profile-1">
            <div class="kt-widget__head">
                <div class="kt-widget__media">
                    <img src="{$assigned.details.user->photo}" alt="{$assigned.details.user->utilisateur}">
                </div>
                <div class="kt-widget__content">
                    <div class="kt-widget__section">
                        <a href="#" class="kt-widget__username" data-toggle="kt-tooltip" data-placement="right" title="{$assigned.details.user->utilisateur}">
                            {$assigned.details.user->utilisateur}
                            <!--i class="flaticon2-check-mark kt-font-success"></i-->
                        </a>
                        <span class="kt-widget__subtitle">
                            {$assigned.details.user->type_utilisateur}
                        </span>
                    </div>
                    <div class="kt-widget__action">
                        <button data-toggle="kt-tooltip" data-placement="right" title="Créer une alerte pour l'utilisateur" type="button" class="btn btn-label-danger btn-sm"><i class="kt-nav__link-icon flaticon-bell"></i></button>
                        <button data-toggle="kt-tooltip" data-placement="right" title="Envoyer un courriel à l'utilisateur" type="button" class="btn btn-label-danger btn-sm"><i class="kt-nav__link-icon flaticon2-send"></i></button>
                        <button data-toggle="kt-tooltip" data-placement="right" title="Ajouter une note" type="button" class="btn btn-label-danger btn-sm"><i class="kt-nav__link-icon flaticon2-talk"></i></button>
                    </div>
                </div>
            </div>
            <div class="kt-widget__body">
                <div class="kt-widget__content">
                    <div class="kt-portlet kt-portlet--tabs">
                        <div class="kt-portlet__head">
                            <div class="kt-portlet__head-toolbar">
                                <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-danger nav-tabs-line-2x nav-tabs-line-right nav-tabs-bold" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#kt_portlet_base_demo_2_3_tab_content" role="tab">
                                            <i class="kt-nav__link-icon flaticon-information"></i> Infos
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#kt_portlet_base_demo_2_2_tab_content" role="tab">
                                            <i class="kt-nav__link-icon flaticon-placeholder-3"></i> Adres.
                                        </a>
                                    </li>
                                    {if $assigned.details.user->departements_autorises|count gt 0}
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kt_portlet_base_demo_2_4_tab_content" role="tab">
                                                <i class="kt-nav__link-icon flaticon2-map"></i> Dept. auto.
                                            </a>
                                        </li>
                                    {/if}
                                </ul>
                            </div>
                        </div>
                        <div class="kt-portlet__body">                   
                            <div class="tab-content">
                                <div class="tab-pane active" id="kt_portlet_base_demo_2_3_tab_content" role="tabpanel">
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Créé le :</span>
                                        <span class="kt-widget__data">{$assigned.details.user->date_creation}</span>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Modifié le :</span>
                                        <span class="kt-widget__data">{$assigned.details.user->date_modification}</span>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Email :</span>
                                        <span class="kt-widget__data" data-toggle="kt-tooltip" data-placement="right" title="Envoyer un courriel à {$assigned.details.user->utilisateur}">{$assigned.details.user->email}</span>
                                    </div>
                                    {if !empty($assigned.details.user->telephone)}
                                        <div class="kt-widget__info">
                                            <span class="kt-widget__label">Téléphone :</span>
                                            <a href="tel:{$assigned.details.user->telephone}" class="kt-widget__data" data-toggle="kt-tooltip" data-placement="right" title="Appeler {$assigned.details.user->utilisateur}">{$assigned.details.user->telephone}</a>
                                        </div>
                                    {/if}
                                    {if !empty($assigned.details.user->portable)}
                                        <div class="kt-widget__info">
                                            <span class="kt-widget__label">Portable :</span>
                                            <a href="tel:{$assigned.details.user->portable}" class="kt-widget__data" data-toggle="kt-tooltip" data-placement="right" title="Appeler {$assigned.details.user->utilisateur}">{$assigned.details.user->telephone}</a>
                                        </div>
                                    {/if}
                                </div>
                                <div class="tab-pane" id="kt_portlet_base_demo_2_2_tab_content" role="tabpanel">

                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Adresse :</span>
                                        <span class="kt-widget__data">{$assigned.details.user->adresse}</span>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Code postal :</span>
                                        <span class="kt-widget__data">{$assigned.details.user->cp}</span>
                                    </div>
                                    <div class="kt-widget__info">
                                        <span class="kt-widget__label">Ville :</span>
                                        <span class="kt-widget__data">{$assigned.details.user->ville}</span>
                                    </div>
                                </div>
                                {if $assigned.details.user->departements_autorises|count gt 0}
                                    <div class="tab-pane" id="kt_portlet_base_demo_2_4_tab_content" role="tabpanel">
                                        <div class="kt-widget__info">
                                            <span class="kt-widget__data">
                                                {foreach from=$assigned.details.user->departements_autorises item=departement}
                                                    <span class="btn btn-bold btn-sm btn-font-sm  btn-label-primary mt-2 mr-1">{$departement->nom}</span>
                                                {/foreach}
                                            </span>
                                        </div>
                                    </div>
                                {/if}
                            </div>      
                        </div>
                    </div>
                </div>
                <div class="kt-widget__items">
                    <a class="kt-widget__item aj_details_per_link {if !isset($assigned.actived) || $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="personalInformations">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <polygon points="0 0 24 0 24 24 0 24" />
                                <path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                <path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Informations personnelles
                            </span>
                        </span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="managePassword">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path d="M4,4 L11.6314229,2.5691082 C11.8750185,2.52343403 12.1249815,2.52343403 12.3685771,2.5691082 L20,4 L20,13.2830094 C20,16.2173861 18.4883464,18.9447835 16,20.5 L12.5299989,22.6687507 C12.2057287,22.8714196 11.7942713,22.8714196 11.4700011,22.6687507 L8,20.5 C5.51165358,18.9447835 4,16.2173861 4,13.2830094 L4,4 Z" fill="#000000" opacity="0.3" />
                                <path d="M12,11 C10.8954305,11 10,10.1045695 10,9 C10,7.8954305 10.8954305,7 12,7 C13.1045695,7 14,7.8954305 14,9 C14,10.1045695 13.1045695,11 12,11 Z" fill="#000000" opacity="0.3" />
                                <path d="M7.00036205,16.4995035 C7.21569918,13.5165724 9.36772908,12 11.9907452,12 C14.6506758,12 16.8360465,13.4332455 16.9988413,16.5 C17.0053266,16.6221713 16.9988413,17 16.5815,17 C14.5228466,17 11.463736,17 7.4041679,17 C7.26484009,17 6.98863236,16.6619875 7.00036205,16.4995035 Z" fill="#000000" opacity="0.3" />
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Changement de mot de passe
                            </span>
                        </span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="statistiques">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <rect fill="#000000" opacity="0.3" x="17" y="4" width="3" height="13" rx="1.5"/>
                                <rect fill="#000000" opacity="0.3" x="12" y="9" width="3" height="8" rx="1.5"/>
                                <path d="M5,19 L20,19 C20.5522847,19 21,19.4477153 21,20 C21,20.5522847 20.5522847,21 20,21 L4,21 C3.44771525,21 3,20.5522847 3,20 L3,4 C3,3.44771525 3.44771525,3 4,3 C4.55228475,3 5,3.44771525 5,4 L5,19 Z" fill="#000000" fill-rule="nonzero"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="11" width="3" height="6" rx="1.5"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Statistiques
                            </span>
                        </span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="courriers">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,17 C21,18.1045695 20.1045695,19 19,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Courriers
                            </span>
                        </span>
                        <span class="kt-badge kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">5</span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="taches">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3"/>
                                <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Tâches
                            </span>
                        </span>
                        <span class="kt-badge kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">5</span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="notes">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000"/>
                                <path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Notes
                            </span>
                        </span>
                        <span class="kt-badge kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">5</span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="notifications">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <path d="M17,12 L18.5,12 C19.3284271,12 20,12.6715729 20,13.5 C20,14.3284271 19.3284271,15 18.5,15 L5.5,15 C4.67157288,15 4,14.3284271 4,13.5 C4,12.6715729 4.67157288,12 5.5,12 L7,12 L7.5582739,6.97553494 C7.80974924,4.71225688 9.72279394,3 12,3 C14.2772061,3 16.1902508,4.71225688 16.4417261,6.97553494 L17,12 Z" fill="#000000"/>
                                <rect fill="#000000" opacity="0.3" x="10" y="16" width="4" height="4" rx="2"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Notifications
                            </span>
                        </span>
                        <span class="kt-badge kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder kt-pulse kt-pulse--warning">5</span>
                    </a>
                    <a class="kt-widget__item aj_details_per_link {if isset($assigned.actived) && $assigned.actived eq ""} kt-widget__item--active {/if}" data-link="timeline">
                        <span class="kt-widget__section">
                            <span class="kt-widget__icon">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24"/>
                                <path d="M10.9630156,7.5 L11.0475062,7.5 C11.3043819,7.5 11.5194647,7.69464724 11.5450248,7.95024814 L12,12.5 L15.2480695,14.3560397 C15.403857,14.4450611 15.5,14.6107328 15.5,14.7901613 L15.5,15 C15.5,15.2109164 15.3290185,15.3818979 15.1181021,15.3818979 C15.0841582,15.3818979 15.0503659,15.3773725 15.0176181,15.3684413 L10.3986612,14.1087258 C10.1672824,14.0456225 10.0132986,13.8271186 10.0316926,13.5879956 L10.4644883,7.96165175 C10.4845267,7.70115317 10.7017474,7.5 10.9630156,7.5 Z" fill="#000000"/>
                                <path d="M7.38979581,2.8349582 C8.65216735,2.29743306 10.0413491,2 11.5,2 C17.2989899,2 22,6.70101013 22,12.5 C22,18.2989899 17.2989899,23 11.5,23 C5.70101013,23 1,18.2989899 1,12.5 C1,11.5151324 1.13559454,10.5619345 1.38913364,9.65805651 L3.31481075,10.1982117 C3.10672013,10.940064 3,11.7119264 3,12.5 C3,17.1944204 6.80557963,21 11.5,21 C16.1944204,21 20,17.1944204 20,12.5 C20,7.80557963 16.1944204,4 11.5,4 C10.54876,4 9.62236069,4.15592757 8.74872191,4.45446326 L9.93948308,5.87355717 C10.0088058,5.95617272 10.0495583,6.05898805 10.05566,6.16666224 C10.0712834,6.4423623 9.86044965,6.67852665 9.5847496,6.69415008 L4.71777931,6.96995273 C4.66931162,6.97269931 4.62070229,6.96837279 4.57348157,6.95710938 C4.30487471,6.89303938 4.13906482,6.62335149 4.20313482,6.35474463 L5.33163823,1.62361064 C5.35654118,1.51920756 5.41437908,1.4255891 5.49660017,1.35659741 C5.7081375,1.17909652 6.0235153,1.2066885 6.2010162,1.41822583 L7.38979581,2.8349582 Z" fill="#000000" opacity="0.3"/>
                                </g>
                                </svg></span>
                            <span class="kt-widget__desc">
                                Timeline
                            </span>
                        </span>
                        <span class="kt-badge kt-badge--unified-danger kt-badge--sm kt-badge--rounded kt-badge--bolder">5</span>
                    </a>
                </div>
            </div>
        </div>

        <!--end::Widget -->
    </div>
</div>
<!--end:: Widgets/Applications/User/Profile1-->
