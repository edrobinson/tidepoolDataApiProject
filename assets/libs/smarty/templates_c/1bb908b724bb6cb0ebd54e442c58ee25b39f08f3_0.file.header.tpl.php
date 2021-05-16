<?php
/* Smarty version 3.1.39, created on 2021-04-10 18:28:43
  from 'C:\wamp64\www\tidepoolDataApiProject\assets\smarty-templates\header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_6071ee5b070c92_98394529',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1bb908b724bb6cb0ebd54e442c58ee25b39f08f3' => 
    array (
      0 => 'C:\\wamp64\\www\\tidepoolDataApiProject\\assets\\smarty-templates\\header.tpl',
      1 => 1555278884,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:headLinks.tpl' => 1,
  ),
),false)) {
function content_6071ee5b070c92_98394529 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html lang="en" style="font-size: 14px;">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
    <?php echo $_smarty_tpl->tpl_vars['jaxoncss']->value;?>


    <?php $_smarty_tpl->_subTemplateRender("file:headLinks.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"><?php echo '</script'; ?>
>
      <?php echo '<script'; ?>
 src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"><?php echo '</script'; ?>
>
    <![endif]-->
  </head>
<?php }
}
