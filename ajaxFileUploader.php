<?php
/*
    This is the server side of the main page file upload procedure.
    It is called via an ajax call and processes the upload
    and returns the outcome. 0 = error, 1 = OK
*/

    if(isset($_FILES['file']['name'])){
       // file name
       $filename = $_FILES['file']['name'];

       // Location
       $location = './uploads/'.$filename;

       // Get file extension
       $file_extension = pathinfo($location, PATHINFO_EXTENSION);
       $file_extension = strtolower($file_extension);
       
       // Valid extensions
       $valid_ext = 'json';

       $response = 0;
       if($file_extension == $valid_ext){
          // Upload file
          if(file_exists($location))
          {
              unlink($location); //Remove previous uploaded files
          }
          if(move_uploaded_file($_FILES['file']['tmp_name'],$location)){
            $response = 1;
            $json = file_get_contents($location);
           //Copy the json file to the app json file folder.
           file_put_contents('./assets/jsonfiles/tidepooldata.json',$json);
          } 
       }

       echo $response;
       exit;
    }
