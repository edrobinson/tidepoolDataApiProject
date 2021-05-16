<?php
/*
    This class implements the Tidepool data APIs.
    We are only interested in the authorization and user data APIs. 
    Metadata api is implemented and tested with the creds in the Using Tideppool API document
    and it returns the proper json data.
        
    The APIs are documented in the file assets/docs/Using Tidepool Data APIs.odt 
    which also contains references to online resources.
    
    The class uses Ryan McCue's Requests classes to do the HTTP procssing.
    
    See https://requests.ryanmccue.info/docs/usage.html for documentation
    
    5/3/21 - pointed to the production servers - works well - returned to int
  
*/
    require "assets/vendor/autoload.php";

    class tidepoolAPI{ 
    
            var $userid = '';       //Tidepool userid from the auth call
            var $token  = '';       //Tidepool session token from the auth call
            var $startDate = '';    //Optional starting date for the data call
            var $endDate = '';      //Optional end date for the data call
            var $credentialFile = './assets/includes/tidepoolCredentials.json'; 
        
        /*
            Call for Tidepool auth credentials.
            
            Tidepool authorization curl to be converted:
            
            curl -i -X POST -u masteremail@domain.com https://int-api.tidepool.org/auth/login
            Password: [your account password]
            
            Inputs:
            $uid - users id
            $pwd - users password
            
            Returns true or throws an exception if the call fails
        */
        public function getTidepoolAuthorization($uid, $pwd)
        {
            if($this->loadCredentials()) return true; //Attempt to load saved creds.
            
            //Basic authorization credentials
            $options = array('auth' => new Requests_Auth_Basic(array($uid, $pwd)));
            
            //Make the request call
            $response = Requests::post('https://int-api.tidepool.org/auth/login', array(),null, $options);
            if(!$response->success)
            {
                throw new Exception('Tidepool authorization request failed');
            }
            
            //Decode the response body and extract the TP user id
            $json = json_decode($response->body,true);
            $this->userid = $json['userid'];
            
            //Extract the TP token from the response headers
            $this->token = $response->headers['x-tidepool-session-token'];
            $this->makeCredentialFile(); //Save the Tidepool credentials
            return true;
        }
        
        //Attempt to load the saved credentials file
        //and set the credential values.
        //We're not certain how long the credentials are valid at this point
        //but we use them until the call fails.
        private function loadCredentials()
        {
            if(!file_exists($this->credentialFile)) return false; //No file 
            
            if(!$json = file_get_contents($this->credentialFile)) return false; //Can't load
            
            $jsonar = json_decode($json,true);  //Load the creds
            $this->token = $jsonar['token'];    //Make them available to this class
            $this->userid = $jsonar['userid'];
            return true; //We're done.
        }
            
        //Create the credential file after getting the creds from Tidepool.
        //This file holds the Tidepool token and useer id.
        //It is used every time the authorization is called.
        //The idea is to save hitting Tidepool's servers until the creds expire.
        private function makeCredentialFile()
        {
            $arr   = array('token' => $this->token, 'userid' => $this->userid);
            $creds = json_encode($arr);
            if(!file_put_contents($this->credentialFile, $creds))
            {
                throw new Exception('Failed to save Tidepool credentials file.');
            }
        }
        
        

    /*
        Get an optionally date ranged set of user data values
        
        Tidepool curl to be implemented with Requests:
        
        curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>"
        
        Inputs:
        Optional start date
        Optional end date
        
        Returns true or throws an exception if the call fails
        Saves the undecoded json data to the assets/jsonfiles folder
    */
        public function getTidepoolData($startDate='', $endDate='', $datatype='smbg')
        {
            //Setup the basic url
            $url = "https://int-api.tidepool.org/data/$this->userid";
            
            
            //Setup the user submitted date range, if any, and append it to the url
            $dates = '';
            if($startDate != '')
            {
                $stdt = $this->convertDate($startDate);
                $dates .= '?startDate='.$stdt;
            }
            if($endDate != '')
            {
                $ndt = $this->convertDate($endDate);
                if($startDate != '')
                {
                    $dates .= '&ampendDate='.$ndt;
                }
                else
                {
                    $dates .= '?endDate='.$ndt;
                }
                
            }
            //If some dates sent, add the type to the dates 
            //and add the dates string to the url.
            //If not, just add the datatype to the url
            if($dates != '')
            {
                $dates .= '&amptype='.$datatype;
                $url .= $dates;
            }
            else
            {
                $url .= '?type='.$datatype;
            }
            
            //Setup the headers array    
            $headers = array('Content-Type' => 'application/json', 'x-tidepool-session-token' => $this->token);
            
            //Try to make the data call
            $response = Requests::get($url,$headers);
            if(!$response->success)
            {    
                throw new Exception('Data request failed.');
            }
            //Save the json returned in the response body
            file_put_contents('assets/jsonfiles/tidepooldata.json', $response->body);
            return true;
        }
        
        //This converts our yyyy-mm-dd to a 2015-10-10T15:00:00.000Z format
        //that Tidepool requires
        private function convertDate($date)
        {
            $dt = new DateTime($date);
            $tpdate = $dt->format('Y-m-d\Th:m:i.\0\0\0\Z');
            return $tpdate;
        }
        
        /*
            This function implements the metadata aquisition par of the API.
            
            The curl request implemented is:
          
            curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/metadata/users/<your-userid>/users"
        */
        public function getTidepoolMetadata()
        {
            //1. Setup the URL
            $url = "https://int-api.tidepool.org/metadata/users/$this->userid/users";
            
            //2. Create the headers
            $headers = array('Content-Type' => 'application/json', 'x-tidepool-session-token' => $this->token);
            
            //3. Make the call
            $response = Requests::get($url,$headers);
            if(!$response->success)
            {    
                //var_dump($response);
                throw new Exception('Metadata request failed.');
            }
            //Save the json returned in the response body
            file_put_contents('assets/jsonfiles/tidepoolmetadata.json', $response->body);
            return true;
        }
    }