{* Tidepool Local File Report Template*}
{* templatebody receives the generatedhtml of the table*}
    {include file="header.tpl"}
	{include file="headLinks.tpl"}

  <body>
  {include file="nav.tpl"}
      <div class="container"> 
      <form id="form1" class="form_main">
          <div class="form-group row">
            <label for="userfile" class="col-sm-4 col-form-label">Path to Json File:</label>
            <input type="text" class="form-control" id="userfile" name="userfile">
          </div>
          <div class="form-actions">
            <button type="button" class="btn btn-primary" onclick="sendFile()">Run</button>
          </div>
      </form>
      </div> <!--end container-->

    <!--JQuery and Bootstrap JS-->
    <script src="assets/js/reportFromLocalFile.js"></script>
	{include file="footlinks.tpl"}
	
    {include file="footer.tpl"}
	</body>
</html>
