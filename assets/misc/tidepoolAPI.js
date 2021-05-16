/*
    Javascript based implementation of the Tidepool api routines 
*/    

    var tidepoolUseridee;       //User id from Tidepool
    var tidepoolToken;          //Auth token from Tidepool
    var tidepoolJson;           //The json string from Tidepool

/*    
    This function uses the fetch api to get the token and user id from Tidepool.
    
    CURL for this step
    curl -i -X POST -u masteremail@domain.com https://int-api.tidepool.org/auth/login
    Password: [your account password]
    
    fetch("https://int-api.tidepool.org/auth/login", {
    headers: {
    Authorization: "Basic ZWRyb2JpbnNvbmpyQGdtYWlsLmNvbQ=="
  },
  method: "POST"
})
*/    
    //Input: User id and password
    function getAuth(uid, pwd)
    {
        var cred64 = btoa(uid + ':' + pwd); //Credentials to base64
        var url = 'https://int-api.tidepool.org/auth/login';
        
        fetch(url, {
            method: 'post',
            headers: {Authorization: 'Basic '+cred64}
        }).then(response => {
                console.log(response);
        })/*.then(function(response){
                tidepoolToken = response.headers['x-tidepool-session-token'];
                tidepoolUseridee = respponse.json['userid'];
                tidepoolJson = response.body;
        })*/
                
          
    }
    
    function handleAuthResponse(response) 
    {
        
        
        alert('In handleAuthResponse');
        let res = 1;
        
        if (response.status !== 200) {
        alert('Looks like there was a problem. Status Code: ' + response.status);
        res = 0;
        }
        if(res == 1)
        {    
            json = response.json();
            json = JSON.parse(json);
            tidepoolUseridee = json['userid'];
            tidepoolToken = response.headers.get('X-Tidepool-session-Token');
        }
        return res;
    }
    
    function handleAuthError(err)
    {
        alert('Fetch Error :-S' + err);
        return 0;
    }

/*
    CURL to retrieve user device data from Tidepool
    
    curl -s -X GET -H "x-tidepool-session-token: <your-session-token>" -H "Content-Type: application/json" "https://int-api.tidepool.org/data/<subject-userid>"
    
    Optional query string elements
    type : The Tidepool data type to search for.
    startDate : Only data with 'time' field equal to or greater to this date will be returned
    endDate : Only data with 'time' field less than to or equal to this date will be returned
*/
    function getData()
    {
       //Make the call using the fetch api
       url = 'https://int-api.tidepool.org/data/' + tidepoolUseridee + '?type=smbg';
       response = fetch(url, {
          method: "GET",
          headers:  
          {
              'x-tidepool-session-token' : tidepoolToken,
              'Content-Type' : 'application/json'
          }
        })
        .then(
            function(response) {
              if (response.status !== 200) {
                alert('Looks like there was a data retrieval problem. Status Code: ' + response.status);
                return false;
                
              }

              //Retrieve the Tidepool json string body from the response
              response.blob().then(function(json) 
              {
                tidepoolJson = json;  
              });
              return true;
            }
          )
          .catch(function(err) {
            alert('Fetch Error :-S' + err);
            return false
          });
    }
