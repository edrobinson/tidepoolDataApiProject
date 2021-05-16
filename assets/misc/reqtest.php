<?php



//Testing the Requeats classes for the Tidepoll api
    require "assets/vendor/autoload.php";

        $userid = '';   //Tidepool userid from the auth call
        $token  = '';   //Tidepool session token from the auth call
        $startDate = '';//Optional starting date for the data call
        $endDate = '';  //Optional end date for the data call
        
        if(getTidepoolAuthorization('edrobinsonjr@gmail.com', 'edr123467>'))
        {
            getTidepoolData('2020-12-01', '2021-04-08');
        }
            
        
        function getTidepoolAuthorization($uid, $pwd)
        {
            global $userid, $token;
            //Basic authorization credentials
            $options = array('auth' => new Requests_Auth_Basic(array($uid, $pwd)));
            $response = Requests::post('https://int-api.tidepool.org/auth/login', array(),null, $options);
           
            //Check the response status
            if ($response->success === false)
            {
                //msg('Auth. Request Failed');
                return false;
            }
            else
            {    
                //Decode the response body and extract the TP user id
                $json = json_decode($response->body,true);
                $userid =  $json['userid'];
                
                //Extract the TP token from the response headers
                $token = $response->headers['x-tidepool-session-token'];
                //msg('Auth Request Succeded.');
                return true;
            }
        }
        
        

    /*
        Get an optionally date ranged set of user data values
        
        Tidepool curl to be implemented with Requests:
        
        curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>"
    */
        function getTidepoolData($startDate='', $endDate='')
        {
            global $userid, $token;
           //Setup the basic url
            $url = "https://int-api.tidepool.org/data/$userid";
            
            //Setup the user submitted date range, if any, and append it to the url
            $dates = '';
            if($startDate != '')
            {
                $dates .= '?startDate='.convertDate($startDate);
            }
            if($endDate != '')
            {
                if($startDate != '')
                {
                    $dates .= '&ampendDate='.convertDate($endDate);
                }
                else
                {
                    $dates .= '?endDate='.convertDate($endDate);
                }
            }
           
           //If we have any dates, add them to the url along with the type 
           //otherwise just add the type. Seems the type must come after the date/s
           //or the dates will be ignored. The type maybe could be eliminated altogether.
           if($dates != '')
            {
                $dates .= '&amptype=smbg';
                $url .= $dates;
            }
            else
            {
                $url .= '?type=smbg';
            }
            //msg('URL: '.$url);
            //Setup the headers array    
            $headers = array('Content-Type' => 'application/json', 'x-tidepool-session-token' => $token);
            
            //Try to make the data call
            $response = Requests::get($url,$headers);
            if($response->success)
            {
                file_put_contents('./assets/jsonfiles/tidepooldata.json', $response->body);
                $json = json_decode($response->body);
                var_dump($json);
                //msg('Data request succeded. There were '.count($json ).' results returned');
                return true;
            }
            else
            {
                    //msg( 'Data Request Failed');
                    var_dump($response);
                    return false;
            }
        }
        
        function convertDate($date)
        {
            $dt = new DateTime($date);
            $tpdate = $dt->format('Y-m-d\Th:m:i.\0\0\0\Z');
            //msg('Tidepool Date: '.$tpdate);
            return $tpdate;
        }
        
        function msg($s)
        {
            echo $s.'<br><br>';
        }
    