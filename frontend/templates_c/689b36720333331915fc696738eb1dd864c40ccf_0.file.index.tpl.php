<?php
/* Smarty version 3.1.32, created on 2022-12-08 21:19:08
  from 'C:\xampp\htdocs\lathenee\templates\frontend\educrat\modules\postuler\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_639246bc541ad7_24262976',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '689b36720333331915fc696738eb1dd864c40ccf' => 
    array (
      0 => 'C:\\xampp\\htdocs\\lathenee\\templates\\frontend\\educrat\\modules\\postuler\\index.tpl',
      1 => 1670530698,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_639246bc541ad7_24262976 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="barba-container" data-barba="container">

	<main class="main-content">

	<?php if (isset($_smarty_tpl->tpl_vars['assigned']->value['success'])) {?>
		<div class="modal-container">
			<div class="overlay"></div>
			<div class="modal">
				<div class="d-grid gap-2">
					<h1><?php echo $_smarty_tpl->tpl_vars['assigned']->value['success'];?>
</h1>
					<button type="button" name="" id=""
						class="close-modal modal-trigger button -md bg-green-5 text-white">OK</button>
				</div>
			</div>
		</div>
	<?php } elseif (isset($_smarty_tpl->tpl_vars['assigned']->value['error'])) {?>
		<div class="modal-container">
			<div class="overlay"></div>
			<div class="modal">
				<div class="d-grid gap-2">
					<h1><?php echo $_smarty_tpl->tpl_vars['assigned']->value['error'];?>
</h1>
					<button type="button" name="" id=""
						class="close-modal modal-trigger button -md bg-red-3 text-white">OK</button>
				</div>
			</div>
		</div>
	<?php } elseif (isset($_smarty_tpl->tpl_vars['assigned']->value['erreur'])) {?>
		<div class="modal-container">
			<div class="overlay"></div>
			<div class="modal">
				<div class="d-grid gap-2">
					<h1><?php echo $_smarty_tpl->tpl_vars['assigned']->value['erreur'];?>
</h1>
					<button type="button" name="" id=""
						class="close-modal modal-trigger button -md bg-red-3 text-white">OK</button>
				</div>
			</div>
		</div>
	<?php } else { ?>

		<div class="content-wrapper js-content-wrapper">
			<div class="dashboard -home-9 js-dashboard-home-9">
				<div class="dashboard__sidebar scroll-bar-1">


					<div class="sidebar -dashboard">

						<div class="sidebar__item ">
							<a href="?role=Professeur de Sciences Physiques"
								class="d-flex items-center text-17 lh-1 fw-500 sidebar_nav_item">
								<i class="text-20 icon-chevron-right mr-15"></i>
								Professeur de Sciences Physiques
							</a>
						</div>


						<div class="sidebar__item ">
							<a href="?role=Professeur de Sciences Naturelles"
								class="d-flex items-center text-17 lh-1 fw-500 sidebar_nav_item">
								<i class="text-20 icon-chevron-right mr-15"></i>
								Professeur de Sciences Naturelles
							</a>
						</div>

						<div class="sidebar__item ">
							<a href="?role=Professeur de Français"
								class="d-flex items-center text-17 lh-1 fw-500 sidebar_nav_item">
								<i class="text-20 icon-chevron-right mr-15"></i>
								Professeur de Français
							</a>
						</div>

						<div class="sidebar__item ">
							<a href="?role=Professeur de Mathématiques"
								class="d-flex items-center text-17 lh-1 fw-500 sidebar_nav_item">
								<i class="text-20 icon-chevron-right mr-15"></i>
								Professeur de Mathématiques
							</a>
						</div>

					</div>

				</div>

				<div class="dashboard__main">
					<div class="dashboard__content bg-light-4">
						<div class="row pb-50 mb-10">
							<div class="col-auto">

								<h1 class="text-30 lh-12 fw-700 role_title"><?php echo $_smarty_tpl->tpl_vars['assigned']->value['role'];?>
</h1>

							</div>
						</div>


						<div class="row y-gap-30">
							<div class="col-xl-5">
								<div class="rounded-16 bg-white -dark-bg-dark-1 shadow-4 h-100">
									<div class="d-flex items-center py-20 px-30 border-bottom-light">
										<h2 class="text-17 lh-1 fw-500">Postuler</h2>
									</div>

									<div class="py-30 px-30">
										<div class="y-gap-30">
											<form method="post" class="contact-form" name="role_form">

												<div class="form-select col-12 mb-30">
													<label
														class="text-16 lh-1 fw-500 text-dark-1 mb-10">Civilité</label>
													<input type="text" name="civilite" list="civility" required
														placeholder="Monsieur">
													<datalist id="civility">
														<option value="Monsieur">
														<option value="Madame">
														<option value="Mademoiselle">
													</datalist>
													<div class="civilite_validator text-red-3 mt-3 ml-10"></div>
												</div>


												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Prénom</label>

													<input type="text" required name="prenom">
													<div class="prenom_validator text-red-3 mt-3 ml-10"></div>
												</div>


												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Nom</label>

													<input type="text" required name="nom">
													<div class="nom_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Date de
														naissance</label>

													<input type="date" name="date_naiss" min="1952-01-01"
														max="2004-12-31" required />
													<div class="date_naiss_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label
														class="text-16 lh-1 fw-500 text-dark-1 mb-10">Nationalité</label>

													<input type="text" name="nationalite" list="nationalité"
														placeholder="Sénégalaise" required>
													<datalist id="nationalité">
														<option value="Sénégalaise">
														<option value="Française">
													</datalist>
													<div class="nationalite_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Adresse</label>

													<input type="text" placeholder="Almadies" required name="adress">
													<div class="adress_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Ville</label>

													<input type="text" placeholder="Dakar" required name="ville">
													<div class="ville_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">E-mail</label>

													<input type="email" placeholder="mymail@mail.com" required
														name="mail">
													<div class="mail_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label
														class="text-16 lh-1 fw-500 text-dark-1 mb-10">Téléphone</label>

													<input type="tel" placeholder="77-121-21-21" required name="tel">
													<div class="tel_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Professeur en
														exercice</label><br />

													<input checked="checked" name="en_exercice" value="Oui"
														type="radio"> Oui &nbsp;&nbsp;&nbsp;
													<input name="en_exercice" value="Non" type="radio"> Non
													<div class="en_exercice_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Dernier diplôme
														obtenu</label>

													<input type="text" placeholder="DEA Mathématiques" required
														name="diplome">
													<div class="diplome_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="col-12 mb-30">

													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Année
														d'obtention du dernier diplôme</label>

													<input type="number" min="1980" max="2022" placeholder="2012"
														required name="date_dd">
													<div class="date_dd_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="form-select col-12 mb-30">
													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Matière</label>
													<input type="text" required name="matiere" list="matiere_"
														placeholder="Mathématiques">
													<datalist id="matiere_">
														<option value="Mahtématiques">
														<option value="Histoire">
														<option value="Histoire">
														<option value="Biologie">
														<option value="Sport">
														<option value="Français">
													</datalist>
													<div class="matiere_validator text-red-3 mt-3 ml-10"></div>

												</div>

												<div class="form-select col-12 mb-30">
													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">Niveau</label>
													<input type="text" required name="niveau" list="niveau"
														placeholder="Lycée">
													<datalist id="niveau">
														<option value="Lycée">
														<option value="Collège">
														<option value="Primaire">
													</datalist>
													<div class="niveau_validator text-red-3 mt-3 ml-10"></div>
												</div>

												<div class="input-group col-12 mb-30">
													<label class="text-16 lh-1 fw-500 text-dark-1 mb-10">CV</label>

													<div class="custom-file">
														<input type="file" class="custom-file-input"
															accept=".pdf, .png, .jpeg" id="cv"
															aria-describedby="inputGroupFileAddonx" required name="cv">
														<div class="cv_validator text-red-3 mt-3 ml-10"></div>
													</div>
												</div>
												<div class="col-12 d-flex justify-center">
													<button type="submit" class="button -md -purple-1 text-white"
														id="button" name="envoyer_demande">Envoyer</button>
												</div>

											</form>
										</div>
									</div>
								</div>
							</div>

							<div class="col-xl-7 mailbox">
								<div class="rounded-16 bg-white -dark-bg-dark-1 shadow-4 h-100">
									<div class="d-flex justify-between p-3">
										<div>
											<span class="replace" id="civilite">Monsieur</span> <span class="replace"
												id="prenom">John</span> <span class="replace" id="nom">Doe</span> <br />
											Titulaire d'un <span class="replace" id="diplome">CAPES</span> <br />
											Courriel : <span class="replace" id="mail">johndoe@hotmail.fr</span> <br />
											Tel : <span class="replace" id="tel">77-222-22-22</span><br />
											Adresse : <span class="replace" id="adress">Point-E</span>
										</div>
										<div>
											<span class="replace" id="ville">Dakar</span>, le <?php echo $_smarty_tpl->tpl_vars['assigned']->value['today'];?>

										</div>
									</div>
									<div class="d-flex justify-end items-end p-5">
										A Madame la<br />
										Directrice de L'Athénée<br />
									</div>
									<div class="p-5">
										<span class="fw-700 underline">Objet :</span> Candidature au poste de
										<?php echo $_smarty_tpl->tpl_vars['assigned']->value['role'];?>
.<br /><br />
										Madame,<br /><br />
										&nbsp;&nbsp;&nbsp;&nbsp;Titulaire d'un <span class="replace"
											id="diplome">CAPES</span> depuis <span class="replace"
											id="date_dd">2012</span>, Je souhaite vous faire part de mon intérêt pour le
										poste de <?php echo $_smarty_tpl->tpl_vars['assigned']->value['role'];?>
, que vous proposez sur www.lathenee.sn. Les
										compétences en <span class="replace" id="matiere">Mathématiques</span>, acquises
										lors de mes précédentes expériences, correspondent au descriptif de votre
										offre.<br />

										Vous trouverez mon CV plus détaillée en pièces jointes.<br /><br />

										&nbsp;&nbsp;&nbsp;&nbsp;Dans l’attente de vous rencontrer prochainement, je vous
										prie de croire, Madame, à l’expression de mes salutations
										distinguées.<br /><br />

										Cordialement,

									</div>

									<div class="d-flex justify-end p-5"><span class="replace" id="prenom">John</span>
										&nbsp;<span class="replace" id="nom">Doe</span></div>

								</div>
							</div>

						</div>

					</div>

					<footer class="footer -dashboard py-30">
						<div class="row items-center justify-between">
							<div class="col-auto">
								<div class="text-13 lh-1">© 2022 Educrat. All Right Reserved.</div>
							</div>

							<div class="col-auto">
								<div class="d-flex items-center">
									<div class="d-flex items-center flex-wrap x-gap-20">
										<div>
											<a href="help-center.html" class="text-13 lh-1">Help</a>
										</div>
										<div>
											<a href="terms.html" class="text-13 lh-1">Privacy Policy</a>
										</div>
										<div>
											<a href="#" class="text-13 lh-1">Cookie Notice</a>
										</div>
										<div>
											<a href="#" class="text-13 lh-1">Security</a>
										</div>
										<div>
											<a href="terms.html" class="text-13 lh-1">Terms of Use</a>
										</div>
									</div>

									<button class="button -md -rounded bg-light-4 text-light-1 ml-30">English</button>
								</div>
							</div>
						</div>
					</footer>
				</div>
			</div>
		</div>

	</main>

</div>

<?php }
if (isset($_smarty_tpl->tpl_vars['erreur']->value) || isset($_smarty_tpl->tpl_vars['succes']->value)) {?>
	<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_notification.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
}
}
