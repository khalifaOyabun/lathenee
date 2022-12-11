<?php
/* Smarty version 3.1.32, created on 2022-11-20 14:02:30
  from '/Applications/XAMPP/xamppfiles/htdocs/plateformes/amarys/lathenee/templates/frontend/educrat/common/_supfooter.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_637a256629d479_54076706',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '60032d805b19269ed96f03228205ed8501af13e5' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/plateformes/amarys/lathenee/templates/frontend/educrat/common/_supfooter.tpl',
      1 => 1656620945,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_637a256629d479_54076706 (Smarty_Internal_Template $_smarty_tpl) {
?><footer class="footer -type-4 bg-dark-2">
    <div class="container">
        <div class="row y-gap-30 justify-between pt-60 pb-10">
            <div class="col-lg-7 col-md-6">
                <div class="text-17 fw-500 text-white uppercase mb-25">
                    RESTEZ PROCHE DE NOUS
                </div>
                <form action="post" class="form-single-field -base mt-15">
                    <input class="py-20 px-30 bg-dark-6 rounded-200 text-white" type="text"
                        placeholder="Saisissez votre adresse e-mail">
                    <button class="button -white rounded-full" type="submit">
                        <i class="icon-arrow-right text-24 text-dark-1"></i>
                    </button>
                </form>
            </div>

            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="footer-header__logo">
                     L'ATHENEE
                </div>

                <div class="d-flex justify-between mt-30">
                    <div class="">
                        <div class="text-white opac-70">Appelez nous au</div>
                        <div class="text-18 lh-1 fw-500 text-white mt-5"><?php echo __TELEPHONE;?>
</div>
                    </div>
                    <div class="">
                        <div class="text-white opac-70">Ou écrivez nous à</div>
                        <div class="text-18 lh-1 fw-500 text-white mt-5"><?php echo __WEBMAIL;?>
</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row y-gap-30 justify-between pb-60">
            <div class="col-xl-8 col-lg-7 col-md-6">
                <div class="d-flex justify-between mt-60">
                    <div class="">
                        <div class="text-white opac-70 text-18">Située en bordure de route menant vers la pointe des
                            Almadies</div>
                        <div class="lh-1 text-18 text-white mt-5"><?php echo __ADRESSE;?>
 <?php echo __CP;?>
</div>
                        <div class="lh-1 text-18 text-white mt-5"><?php echo __VILLE;?>
</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 col-md-6">
                <div class="footer-header-socials mt-60">
                    <div class="text-17 uppercase text-white fw-500">SUIVEZ NOUS SUR</div>
                    <div class="footer-header-socials__list d-flex items-center mt-15">
                        <a href="#" class="size-40 d-flex justify-center items-center text-white"><i
                                class="icon-facebook"></i></a>
                        <a href="#" class="size-40 d-flex justify-center items-center text-white"><i
                                class="icon-twitter"></i></a>
                        <a href="#" class="size-40 d-flex justify-center items-center text-white"><i
                                class="icon-instagram"></i></a>
                        <a href="#" class="size-40 d-flex justify-center items-center text-white"><i
                                class="icon-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-30 border-top-light-15">
            <div class="row justify-between items-center y-gap-20">
                <div class="col-auto">
                    <div class="d-flex items-center h-100 text-white">
                        © 2022 <?php echo mb_strtoupper(__ENSEIGNE, 'UTF-8');?>
. All Right Reserved.
                    </div>
                </div>

                <div class="col-auto">
                    <div class="d-flex x-gap-20 y-gap-20 items-center flex-wrap">
                        <div>
                            <div class="d-flex x-gap-15 text-white">
                                <a href="<?php echo HTTP_PATH;?>
">Accueil</a>
                                <a href="<?php echo HTTP_PATH;?>
/a-propos-de-nous">L'Athénée</a>
                                <a href="<?php echo HTTP_PATH;?>
/mentions-legales">Mentions Légales</a>
                                <a href="<?php echo HTTP_PATH;?>
/conditions-generales-d-utilisations">Conditions Générales</a>
                                <a href="<?php echo HTTP_PATH;?>
/contact">Contact</a>
                            </div>
                        </div>

                        <div>
                            <a href="#" class="button px-30 h-50 -dark-6 rounded-200 text-white">
                                <i class="icon-worldwide text-20 mr-15"></i><span class="text-15">Français</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>
</main>
</div><?php }
}
