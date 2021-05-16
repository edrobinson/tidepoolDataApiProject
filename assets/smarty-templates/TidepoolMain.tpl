{* Tidepool main page template*}

    {include file="header.tpl"}
	{include file="headLinks.tpl"}

  <body>
  {include file="nav.tpl"}
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
            <input type="email" class="form-control" id="useremail" name="useremail"  placeholder="Enter email" value="{$email}"/>
        </div>
        </div>
        <div class="form-group row">
            <label for="password" class="col-sm-4 col-form-label">Password</label>
        <div class="col-sm-5">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" value="{$password}"/>
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
	{include file="footlinks.tpl"}
	<script src="assets/js/TidepoolMain.js"></script>
    {include file="footer.tpl"}
	</body>
</html>
