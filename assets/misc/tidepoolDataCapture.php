<?php
/*
    Tidepool project home page - original version - no ajax
    
    The initial page load (request methos not a post request)
    loads the html of the page andsets the user's email and password
    from the config json and displays the page
    
    When the post request comes in from the browser
    we run the tidepool api classes to get an auth token
    followed by the data request and report generation..
    
    The post array is this:
    
    'useremail' => string 'edrobinsonjr@gmail.com' (length=22)
    'password' => string 'edr123467>' (length=10)
    'startdate' => string '2021-02-01' (length=10)
    'enddate' => string '2021-02-28' (length=10)
    'datatype' => string 'smbg' (length=4)
*/

    //Handle the initial page load
    if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        $conf_json = file_get_contents('./assets/includes/tidepoolConfig.json');
        $config = json_decode($conf_json, true);      
        $html = file_get_contents('./assets/includes/main.html');   //Load the html with tags
        $html = str_replace('[vuseremail]', $config['useremail'], $html);
        $html = str_replace('[vpassword]',  $config['password'], $html);
        echo($html);
        die;
    }

    require "assets/vendor/autoload.php";
      
    require 'classes/getTidepoolAuth.php'; //The authorization class   
    require 'classes/getMeterData.php';    //The meter data download class 
    require 'classes/wordReporter.php';    //Generate a word report
      

    $token;     //Authorization token
    $userid;    //Authorization user
    $json;      //Returned json string
    $ajson;     //Returned json array
    $postdata = $_POST;
    $fname = './assets/jsonfiles/tidepooldata.json'; //path to the saved json file 
        
    
    
    
    callTidepool();
    displayDocument();    
      
      //Download from Tidepool
      function callTidepool()
      {
           global $token, $userid, $json, $ajson,$fname,$postdata; 
            $datalist = $postdata;
            extract($datalist);
            getTidepoolAuthorization($datalist);
            getTidepoolData($datalist);
      }
      
      //Create a word document and 
      function displayDocument()
        {
            global $token, $userid, $json, $ajson,$fname,$postdata;  
            require_once('tableReport.php');
        }

      //Using the getTidepoolAuth class, 
      //Call for a token and userid from the Tidepool server 
      function getTidepoolAuthorization()
      {
            global $token, $userid, $json, $ajson,$fname,$postdata;  
            $getAuth = new getTidepoolAuth($_POST['useremail'], $_POST['password']);
            $getAuth->fetchNewToken();          //Run the api call
            $token = $getAuth->readToken();     //Get the auth token
            $userid = $getAuth->readUserid();  //and the returned test user
        
      }
      
      //If user entered either date check the input date qualifies
      function checkDates($testDate)
      {
        global $token, $userid, $json, $ajson,$fname,$data,$postdata;
        $indate = new DateTime($testDate);
        //Case 1 user did not enter any dates
        if($postdata['startdate'] =='' and $postdata['enddate'] == '') return true; //No dates entered
        //Case 2 start and end dates entered
        if($postdata['startdate'] !='' and $postdata['enddate'] != '')
        {
            $strt = new DateTime($postdata['startdate']);
            $end  = new DateTime($postdata['enddate']);
            if($indate >= $strt and $indate <= $end)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        //Case 3 Start date only
        if($postdata['startdate'] != '' and $postdata['enddate'] == '')
        {
            $strt = new DateTime($postdata['startdate']);
            if($indate >= $strt)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        //Case 4 enddate only
        if($postdata['startdate'] == '' and $postdata['enddate'] != '')
        {
            $end  = new DateTime($postdata['enddate']);
            if($indate <= $end)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
      }
            
        
        
    //Using, the getMeterData class, call for the Tidepool results
    function getTidepoolData()
    {
        global $token, $userid, $json, $ajson,$fname,$data,$postdata; 
        extract($postdata);
        //Create the meterdata api class
        $getData = new getMeterData($token, $userid, $datatype, $startdate, $enddate);
        $getData->readMeterData();        //Make the api call
        $json = $getData->readBody();     //Get the json
        $res = $getData->readHTTPResult(); //Get the entire response
        //Save the response
        file_put_contents('response.txt', $res);
        //Save the data to a file for the report writer
        file_put_contents($fname, $json);
    }
