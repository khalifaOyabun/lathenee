<?php
/* Smarty version 3.1.32, created on 2022-11-28 17:06:48
  from 'C:\xampp\htdocs\lathenee\templates\frontend\educrat\index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.32',
  'unifunc' => 'content_6384dc98952784_37396231',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd97c41e87ce6c0df86163ee50d0aeac27285b856' => 
    array (
      0 => 'C:\\xampp\\htdocs\\lathenee\\templates\\frontend\\educrat\\index.tpl',
      1 => 1669651605,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6384dc98952784_37396231 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['get']->value['module'] == '404' || $_smarty_tpl->tpl_vars['get']->value['action'] == 'changementmotdepasse' || $_smarty_tpl->tpl_vars['get']->value['action'] == 'changementmotdepasseoublie') {?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/modules/".((string)$_smarty_tpl->tpl_vars['get']->value['module'])."/".((string)$_smarty_tpl->tpl_vars['get']->value['action']).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
} elseif ($_smarty_tpl->tpl_vars['get']->value['module'] == 'postuler') {?>
    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_subheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/modules/".((string)$_smarty_tpl->tpl_vars['get']->value['module'])."/".((string)$_smarty_tpl->tpl_vars['get']->value['action']).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
    
    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

<?php } else { ?>
    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_subheader.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/modules/".((string)$_smarty_tpl->tpl_vars['get']->value['module'])."/".((string)$_smarty_tpl->tpl_vars['get']->value['action']).".tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_supfooter.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>

    <?php $_smarty_tpl->_subTemplateRender(((string)$_smarty_tpl->tpl_vars['template']->value)."/common/_footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
}
}
