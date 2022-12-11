{* <!-- barba container start -->
<div class="barba-container" data-barba="container">


    <main class="main-content  ">

        {if $get.module eq "accueil"}
            <header data-anim="fade" data-add-bg="bg-dark-1" class="header -type-5 js-header">
                <div class="d-flex items-center bg-purple-1 py-10">
                    <div class="container">
                        <div class="row y-gap-5 justify-between items-center">
                            <div class="col-auto">
                                <div class="d-flex x-gap-40 y-gap-10 items-center">
                                    <div class="d-flex items-center text-white md:d-none">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1">{__TELEPHONE}</div>
                                    </div>
                                    <div class="d-flex items-center text-white">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1">{__WEBMAIL}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="d-flex x-gap-30 y-gap-10">
                                    <div>
                                        <div class="d-flex x-gap-20 items-center text-white">
                                            <a href="{HTTP_PATH}"><i class="icon-facebook text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-twitter text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-instagram text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-linkedin text-11"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="container py-10">
                    <div class="row justify-between items-center">

                        <div class="col-auto">
                            <div class="header-left">

                                <div class="header__logo ">
                                    <a data-barba href="index.html">
                                        {* <img src="{_FEIMG_}/general/logo.png" alt="logo"> *}
                                        L'ATHENEE
                                    </a>
                                </div>

                            </div>
                        </div>


                        <div class="col-auto">
                            <div class="header-right d-flex items-center">

                                <div class="header-menu js-mobile-menu-toggle ">
                                    <div class="header-menu__content">
                                        <div class="mobile-bg js-mobile-bg"></div>

                                        <div class="menu js-navList">
                                            <ul class="menu__nav text-white -is-active">
                                                <li><a data-barba href="{HTTP_PATH}">Accueil</a></li>

                                                <li><a data-barba href="{HTTP_PATH}/a-propos-de-nous">L'Athénée</a></li>
                                                
                                                <li><a data-barba href="{HTTP_PATH}/evenements">Évènements</a></li>

                                                <li><a data-barba href="{HTTP_PATH}/actualites">Actualités</a></li>

                                                <li><a data-barba href="{HTTP_PATH}/galerie">Galerie</a></li>

                                                <li><a data-barba href="{HTTP_PATH}/recrutement">Recrutement</a></li>

                                                <li><a data-barba href="{HTTP_PATH}/contact">Contact</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="header-menu-bg"></div>
                                </div>


                            </div>
                        </div>

                    </div>
                </div>
            </header>
        {else}
            <header data-anim="fade" data-add-bg="bg-dark-1" class="header -type-1 js-header">
                {* <div class="d-flex items-center bg-purple-1 py-10">
                    <div class="container">
                        <div class="row y-gap-5 justify-between items-center">
                            <div class="col-auto">
                                <div class="d-flex x-gap-40 y-gap-10 items-center">
                                    <div class="d-flex items-center text-white md:d-none">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1">{__TELEPHONE}</div>
                                    </div>
                                    <div class="d-flex items-center text-white">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1">{__WEBMAIL}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="d-flex x-gap-30 y-gap-10">
                                    <div>
                                        <div class="d-flex x-gap-20 items-center text-white">
                                            <a href="{HTTP_PATH}"><i class="icon-facebook text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-twitter text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-instagram text-11"></i></a>
                                            <a href="{HTTP_PATH}"><i class="icon-linkedin text-11"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> *}
                <div class="header__container">
                    <div class="row justify-between items-center">

                        <div class="col-auto">
                            <div class="header-left">

                                <div class="header__logo ">
                                    <a data-barba href="index.html">
                                    {* <img src="{_FEIMG_}/general/logo.svg" alt="logo"> *} L'ATHENEE
                                    </a>
                                </div>
                            </div>
                        </div>


                        <div class="header-menu js-mobile-menu-toggle ">
                            <div class="header-menu__content">
                                <div class="mobile-bg js-mobile-bg"></div>

                                <div class="d-none xl:d-flex items-center px-20 py-20 border-bottom-light">
                                    <a href="login.html" class="text-dark-1">Log in</a>
                                    <a href="signup.html" class="text-dark-1 ml-30">Sign Up</a>
                                </div>

                                <div class="menu js-navList">
                                    <ul class="menu__nav text-white -is-active">
                                    <li><a data-barba href="{HTTP_PATH}">Accueil</a></li>

                                    <li><a data-barba href="{HTTP_PATH}/a-propos-de-nous">L'Athénée</a></li>
                                    
                                    <li><a data-barba href="{HTTP_PATH}/evenements">Évènements</a></li>

                                    <li><a data-barba href="{HTTP_PATH}/actualites">Actualités</a></li>

                                    <li><a data-barba href="{HTTP_PATH}/galerie">Galerie</a></li>

                                    <li><a data-barba href="{HTTP_PATH}/recrutement">Recrutement</a></li>

                                    <li><a data-barba href="{HTTP_PATH}/contact">Contact</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="header-menu-close" data-el-toggle=".js-mobile-menu-toggle">
                                <div class="size-40 d-flex items-center justify-center rounded-full bg-white">
                                    <div class="icon-close text-dark-1 text-16"></div>
                                </div>
                            </div>

                            <div class="header-menu-bg"></div>
                        </div>
                    </div>
                </div>
            </header>
{/if} *}
