{* Tidepool setup template*}
{* templatebody receives the generatedhtml of the table*}
    {include file="header.tpl"}
	{include file="headLinks.tpl"}

  <body>
  {include file="nav.tpl"}
      <div class="container"> 
      <form id="form1" class="form_main">
            <div class="form-group row">
            <label for="useremail" class="col-sm-4 col-form-label">Email address</label>
            <div class="col-sm-8">
            <input type="email" class="form-control" id="useremail" name="useremail"  placeholder="Enter email" required value="{$useremail}">
            </div>
          </div>
          <div class="form-group row">
            <label for="password" class="col-sm-4 col-form-label">Enter Password</label>
            <div class="col-sm-5">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required value="{$password}">
            </div>
          </div>
          <div class="form-group row">
            <label for="username" class="col-sm-4 col-form-label">Name</label>
            <div class="col-sm-7">
            <input type="text" class="form-control" id="username" name="username" placeholder="Your Name" required value="{$username}">
            </div>
          </div>
          <div class="form-group row">
            <label for="birthdate" class="col-sm-4 col-form-label">Birth Date</label>
            <div class="col-sm-7">
            <input type="date" class="form-control" id="birthdate" name="birthdate" placeholder="Birth Date" required value="{$birthdate}">
            </div>
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-primary" onclick="submitForm()">Process</button>
          </div>
      </form>
      </div> <!--end container-->

    <!--JQuery and Bootstrap JS-->
    <script src="assets/js/TidepoolSetup.js"></script>
	{include file="footlinks.tpl"}
	
    {include file="footer.tpl"}
	</body>
</html>
