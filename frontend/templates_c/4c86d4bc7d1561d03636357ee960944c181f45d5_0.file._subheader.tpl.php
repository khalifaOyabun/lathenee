<?php
/* Smarty version 3.1.32, created on 2022-11-28 17:04:27
  from 'C:\xampp\htdocs\lathenee\templates\frontend\educrat\common\_subheader.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_6384dc0b9abc20_40381838',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4c86d4bc7d1561d03636357ee960944c181f45d5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\lathenee\\templates\\frontend\\educrat\\common\\_subheader.tpl',
      1 => 1669651446,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6384dc0b9abc20_40381838 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- barba container start -->
<div class="barba-container" data-barba="container">


    <main class="main-content  ">

        <?php if ($_smarty_tpl->tpl_vars['get']->value['module'] == "accueil") {?>
            <header data-anim="fade" data-add-bg="bg-dark-1" class="header -type-5 js-header">
                <div class="d-flex items-center bg-purple-1 py-10">
                    <div class="container">
                        <div class="row y-gap-5 justify-between items-center">
                            <div class="col-auto">
                                <div class="d-flex x-gap-40 y-gap-10 items-center">
                                    <div class="d-flex items-center text-white md:d-none">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1"><?php echo __TELEPHONE;?>
</div>
                                    </div>
                                    <div class="d-flex items-center text-white">
                                        <div class="icon-email mr-10"></div>
                                        <div class="text13 lh-1"><?php echo __WEBMAIL;?>
</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="d-flex x-gap-30 y-gap-10">
                                    <div>
                                        <div class="d-flex x-gap-20 items-center text-white">
                                            <a href="<?php echo HTTP_PATH;?>
"><i class="icon-facebook text-11"></i></a>
                                            <a href="<?php echo HTTP_PATH;?>
"><i class="icon-twitter text-11"></i></a>
                                            <a href="<?php echo HTTP_PATH;?>
"><i class="icon-instagram text-11"></i></a>
                                            <a href="<?php echo HTTP_PATH;?>
"><i class="icon-linkedin text-11"></i></a>
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
                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
">Accueil</a></li>

                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/a-propos-de-nous">L'Athénée</a></li>
                                                
                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/evenements">Évènements</a></li>

                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/actualites">Actualités</a></li>

                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/galerie">Galerie</a></li>

                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/recrutement">Recrutement</a></li>

                                                <li><a data-barba href="<?php echo HTTP_PATH;?>
/contact">Contact</a></li>
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
        <?php } else { ?>
            <header data-anim="fade" data-add-bg="bg-dark-1" class="header -type-1 js-header">
                                <div class="header__container">
                    <div class="row justify-between items-center">

                        <div class="col-auto">
                            <div class="header-left">

                                <div class="header__logo ">
                                    <a data-barba href="index.html">
                                     L'ATHENEE
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
                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
">Accueil</a></li>

                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/a-propos-de-nous">L'Athénée</a></li>
                                    
                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/evenements">Évènements</a></li>

                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/actualites">Actualités</a></li>

                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/galerie">Galerie</a></li>

                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/recrutement">Recrutement</a></li>

                                    <li><a data-barba href="<?php echo HTTP_PATH;?>
/contact">Contact</a></li>
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
<?php }
}
}
