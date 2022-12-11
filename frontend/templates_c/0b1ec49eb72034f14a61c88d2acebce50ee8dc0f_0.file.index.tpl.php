<?php
/* Smarty version 3.1.32, created on 2022-11-20 14:16:00
  from '/Applications/XAMPP/xamppfiles/htdocs/plateformes/amarys/lathenee/templates/frontend/educrat/modules/contact/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_637a2890242ec2_09065807',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0b1ec49eb72034f14a61c88d2acebce50ee8dc0f' => 
    array (
      0 => '/Applications/XAMPP/xamppfiles/htdocs/plateformes/amarys/lathenee/templates/frontend/educrat/modules/contact/index.tpl',
      1 => 1656405902,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_637a2890242ec2_09065807 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="content-wrapper  js-content-wrapper">

	<section class="">
		<div id="map" class="map"></div>
	</section>

	<section class="layout-pt-md layout-pb-lg">
		<div data-anim-wrap class="container">
			<div class="row y-gap-50 justify-between">
				<div class="col-lg-4">
					<h3 class="text-24 fw-500">Restez Proche De Nous.</h3>
					<p class="mt-25">Neque convallis a cras semper auctor. Libero id faucibus nisl tincidunt
						egetnvallis.</p>

					<div class="y-gap-30 pt-60 lg:pt-40">

						<div class="d-flex items-center">
							<div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
								<img src="<?php echo _FEIMG_;?>
/contact-1/1.svg" alt="icon">
							</div>
							<div class="ml-20"><?php echo __ADRESSE;?>
 <?php echo __CP;?>
 <?php echo __VILLE;?>
</div>
						</div>

						<div class="d-flex items-center">
							<div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
								<img src="<?php echo _FEIMG_;?>
/contact-1/2.svg" alt="icon">
							</div>
							<div class="ml-20"><?php echo __TELEPHONE;?>
</div>
						</div>

						<div class="d-flex items-center">
							<div class="d-flex justify-center items-center size-60 rounded-full bg-light-7">
								<img src="<?php echo _FEIMG_;?>
/contact-1/3.svg" alt="icon">
							</div>
							<div class="ml-20"><?php echo __WEBMAIL;?>
</div>
						</div>

					</div>
				</div>

				<div class="col-lg-7">
					<h3 class="text-24 fw-500">Envoyer Un Message.</h3>
					<p class="mt-25">Neque convallis a cras semper auctor. Libero id faucibus nisl<br>
						tincidunt egetnvallis.</p>

					<form class="contact-form row y-gap-30 pt-60 lg:pt-40" data-athenee-contact-form="true">
						<div class="col-md-6">
							<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Nom & Prénom</label>
							<input type="text" name="name" placeholder="Nom & Prénom ...">
						</div>
						<div class="col-md-6">
							<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">E-mail</label>
							<input type="email" name="email" placeholder="Email ...">
						</div>
						<div class="col-12">
							<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Message...</label>
							<textarea name="message" placeholder="Message" rows="8"></textarea>
						</div>
						<div class="col-12">
							<button type="submit" id="submit" class="button -md -purple-1 text-white">
								Envoyer Message
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</section>

	<section class="layout-pt-lg layout-pb-lg bg-light-4">
		<div class="container">
			<div class="row justify-center text-center">
				<div class="col-xl-8 col-lg-9 col-md-11">

					<div class="sectionTitle ">

						<h2 class="sectionTitle__title ">Foire Aux Questions.</h2>

						<p class="sectionTitle__text ">Ut enim ad minim veniam, quis nostrud exercitation
							ullamco</p>

					</div>


					<div class="accordion -block text-left pt-60 lg:pt-40 js-accordion">

						<div class="accordion__item">
							<div class="accordion__button">
								<div class="accordion__icon">
									<div class="icon" data-feather="plus"></div>
									<div class="icon" data-feather="minus"></div>
								</div>
								<span class="text-17 fw-500 text-dark-1">Quelle est l'expérience passée de vos professeurs ?</span>
							</div>

							<div class="accordion__content">
								<div class="accordion__content__inner">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
										eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim
										ad minim veniam, quis nostrud exercitation ullamco.</p>
								</div>
							</div>
						</div>

						<div class="accordion__item">
							<div class="accordion__button">
								<div class="accordion__icon">
									<div class="icon" data-feather="plus"></div>
									<div class="icon" data-feather="minus"></div>
								</div>
								<span class="text-17 fw-500 text-dark-1">Faites vous des cours à domicile ?</span>
							</div>

							<div class="accordion__content">
								<div class="accordion__content__inner">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
										eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim
										ad minim veniam, quis nostrud exercitation ullamco.</p>
								</div>
							</div>
						</div>

						<div class="accordion__item">
							<div class="accordion__button">
								<div class="accordion__icon">
									<div class="icon" data-feather="plus"></div>
									<div class="icon" data-feather="minus"></div>
								</div>
								<span class="text-17 fw-500 text-dark-1">Quels sont vos tarifs horaires ?</span>
							</div>

							<div class="accordion__content">
								<div class="accordion__content__inner">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
										eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim
										ad minim veniam, quis nostrud exercitation ullamco.</p>
								</div>
							</div>
						</div>

						<div class="accordion__item">
							<div class="accordion__button">
								<div class="accordion__icon">
									<div class="icon" data-feather="plus"></div>
									<div class="icon" data-feather="minus"></div>
								</div>
								<span class="text-17 fw-500 text-dark-1">Faites vous des programmes pour les enfants de la maternelle ?</span>
							</div>

							<div class="accordion__content">
								<div class="accordion__content__inner">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
										eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim
										ad minim veniam, quis nostrud exercitation ullamco.</p>
								</div>
							</div>
						</div>

						<div class="accordion__item">
							<div class="accordion__button">
								<div class="accordion__icon">
									<div class="icon" data-feather="plus"></div>
									<div class="icon" data-feather="minus"></div>
								</div>
								<span class="text-17 fw-500 text-dark-1">Prenez-vous des élèves du programme sénégalais ?</span>
							</div>

							<div class="accordion__content">
								<div class="accordion__content__inner">
									<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
										eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim
										ad minim veniam, quis nostrud exercitation ullamco.</p>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section><?php }
}
