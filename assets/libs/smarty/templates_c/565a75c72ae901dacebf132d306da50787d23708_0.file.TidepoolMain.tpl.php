<?php
/* Smarty version 3.1.39, created on 2021-04-11 17:15:16
  from 'C:\wamp64\www\tidepoolDataApiProject\assets\smarty-templates\TidepoolMain.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.39',
  'unifunc' => 'content_60732ea464b232_89180260',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '565a75c72ae901dacebf132d306da50787d23708' => 
    array (
      0 => 'C:\\wamp64\\www\\tidepoolDataApiProject\\assets\\smarty-templates\\TidepoolMain.tpl',
      1 => 1618160820,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:header.tpl' => 1,
    'file:headLinks.tpl' => 1,
    'file:nav.tpl' => 1,
    'file:footlinks.tpl' => 1,
    'file:footer.tpl' => 1,
  ),
),false)) {
function content_60732ea464b232_89180260 (Smarty_Internal_Template $_smarty_tpl) {
?>
    <?php $_smarty_tpl->_subTemplateRender("file:header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	<?php $_smarty_tpl->_subTemplateRender("file:headLinks.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

  <body>
  <?php $_smarty_tpl->_subTemplateRender("file:nav.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href ="TidepoolSetup">Setup</a>
      </li>
    </ul>

      <div class="container"> 

    <form id="form1" class="form_main">
        <input type="hidden" name="uploadflag" id="uploadflag" value="0"/> 
        <input type="hidden" name="uploadname" id="uploadname" value=""/> 
        <div class="form-group row">
            <label for="useremail" class="col-sm-4 col-form-label">Email address</label>
        <div class="col-sm-5">
            <input type="email" class="form-control" id="useremail" name="useremail"  placeholder="Enter email" value="<?php echo $_smarty_tpl->tpl_vars['email']->value;?>
"/>
        </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-sm-4 col-form-label">Password</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="<?php echo $_smarty_tpl->tpl_vars['password']->value;?>
"/>
        </div>
        </div>
        <div class="form-group row">
            <label for="startdate" class="col-sm-4 col-form-label">Start Date</label>
        <div class="col-sm-5">
            <input type="date" class="form-control" id="startdate" name="startdate" placeholder="Start Date"/>
        </div>
        </div>
        <div class="form-group row">
            <label for="enddate" class="col-sm-4 col-form-label">End Date</label>
        <div class="col-sm-5">
            <input type="date" class="form-control" id="enddate" name="enddate" placeholder="End Date"/>
        </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-4 col-form-label" for="datatype">Data Type</label>
        <div class="col-sm-5">
                <select class="custom-select" id="datatype" name="datatype">
                <option value="smbg">Self Monitored Blood Glucoses</option>
                <option value="cbg">Continuous Blood Glucoses</option>
                <option value="basal">Basal Insulin</option>
                <option value="bloodKetone">Blood Ketones</option>
                <option value="bolus">Bolus Insulins</option>
                <option value="wizard">Bolus Calcular/Wizard</option>
                <option value="cgmSettings">Continuous Monitor Settings</option>
                <option value="pumpSettings">Insulin Pump Settings</option>
                <option value="deviceEvent">Misc. Device Events</option>
            </select>
        </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 col-form-label">Local File</label>
        <div class="col-sm-8 ">
            <button type="button" class="btn btn-primary" onclick="toggleUploader()">Click to Upload a File</button>
        </div>
        </div>
        <div class="form-group row" id="uploader" >
            <input type="file" name="file" id="file">
            <input type="button" class="btn btn-primary" id="btn_uploadfile" value="Upload" onclick="uploadFile()"/>
        </div>

        <div class="form-actions">
        <br>
            <button type="button" class="btn btn-primary" onclick="runReports()">Process Request</button>
        </div>
    </form>

    </div> <!--end container-->

    <!--JQuery and Bootstrap JS-->
	<?php $_smarty_tpl->_subTemplateRender("file:footlinks.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	<?php echo '<script'; ?>
 src="assets/js/TidepoolMain.js"><?php echo '</script'; ?>
>
    <?php $_smarty_tpl->_subTemplateRender("file:footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	</body>
</html>
<?php }
}
