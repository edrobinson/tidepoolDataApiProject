
 /*     
    jQuery(document).ready(function(){
       // $('#nextPage').val(1); //Set the pagination page value
        nextPage(0); //Load it.
    });
      
*/
    /*
        Call the server passing the operation and form data
        
    */
    function sendFile()
    {
        var formdata  = JSON.stringify(jaxon.getFormValues('form1'));            
        jaxon_dispatch('runReports', formdata);
    }

          
    /*
        Response handler - change to a new page to display the report
    */
    function displayReport()
    {
        openInNewTab("reportDisplay.php");//,'_blank').focus();
    }
 

    function openInNewTab(href) {
        Object.assign(document.createElement('a'), {
        target: '_blank',
        href: href,
    }).click();
}