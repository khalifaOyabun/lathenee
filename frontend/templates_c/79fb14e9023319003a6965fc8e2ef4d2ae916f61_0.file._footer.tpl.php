<?php
/* Smarty version 3.1.32, created on 2022-12-08 21:02:03
  from 'C:\xampp\htdocs\lathenee\templates\frontend\educrat\common\_footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_639242bbe2dd95_11102715',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '79fb14e9023319003a6965fc8e2ef4d2ae916f61' => 
    array (
      0 => 'C:\\xampp\\htdocs\\lathenee\\templates\\frontend\\educrat\\common\\_footer.tpl',
      1 => 1670529720,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_639242bbe2dd95_11102715 (Smarty_Internal_Template $_smarty_tpl) {
?>  <!-- JavaScript -->
  <?php echo '<script'; ?>
 src="<?php echo _FEJS_;?>
/jQuery.min.js"><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
  	integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
  	crossorigin=""><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 src="<?php echo _FEJS_;?>
/vendors.js"><?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 src="<?php echo _FEJS_;?>
/main.js"><?php echo '</script'; ?>
>


  <?php echo '<script'; ?>
 src="<?php echo _FEJS_;?>
/scripting/website.js"><?php echo '</script'; ?>
>
  <?php if (isset($_smarty_tpl->tpl_vars['erreur']->value) || isset($_smarty_tpl->tpl_vars['succes']->value)) {?>
		<?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_notification.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
	<?php }?>

  </body>

</html><?php }
}
