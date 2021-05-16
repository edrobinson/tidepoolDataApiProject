
 /*     
    $(document).ready(function(){
       // $('#nextPage').val(1); //Set the pagination page value
        nextPage(0); //Load it.
    });
      
*/
    /*
        Call the server passing the operation and form data
        
    */
    function submitForm()
    {
        var formdata  = JSON.stringify(jaxon.getFormValues('form1'));            
        jaxon_dispatch('processForm', formdata);
    }

          
          

            