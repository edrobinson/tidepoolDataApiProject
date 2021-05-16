
     
    jQuery(document).ready(function(){
       toggleUploader();
    });
      
    /*
        Call the server passing the operation and form data
        using the Jaxon PHP call. The call is sent to the
        runReports method in the client side class.
        
    */
    function runReports()
    {
        var formdata  = JSON.stringify(jaxon.getFormValues('form1'));            
        jaxon_dispatch('runReports', formdata);
    }

       
    /*
        Response handler - change to a new page to display the report
        Called from the server side class
    */
    function displayReport()
    {
        openInNewTab("reportDisplay.php");//,'_blank').focus();
    }
 

    //Create a link object with target and _blank tag to
    //open the report in a new tab. Then click it.
    function openInNewTab(href) {
        Object.assign(document.createElement('a'), {
        target: '_blank',
        href: href,
    }).click();
    }
    
    //Main page file upload frame visibility toggler.
    function toggleUploader()
    {
        //Clear the upload flag
        $('#uploadflag').val(0);
        //Clear the file input
        $("#file").val('');
        //Toggle it's visibility
        $("#uploader").toggle("slow");
    }

    // Upload file with ajax 
    //Called by the the button asociated with the file input
    function uploadFile() {

       var files = document.getElementById("file").files;

       if(files.length > 0 )
       {
          var formData = new FormData();
          formData.append("file", files[0]);

          //Instance the ajax thing
          var xhttp = new XMLHttpRequest();

          // Set POST method and ajax file path a.d open the request
          xhttp.open("POST", "ajaxFileUploader.php", true);

          // call on request changes state
          xhttp.onreadystatechange = function() {
             if (this.readyState == 4 && this.status == 200) {

               var response = this.responseText;
               if(response == 1)
               {
                  alert("Upload successfull.");
                  $('#uploadflag').val(1);
               }else
               {
                  alert("Upload failed.");
                  $('#uploadflag').val(0);
               }
               toggleUploader(); 
             }
          };

          // Send request with data
          xhttp.send(formData);

       }else{
          alert("Please select a file");
       }

    }
    
    
