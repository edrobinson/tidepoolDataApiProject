<?php
/*
    Tidepool Data Reporting Configuration
*/
    //If this is a post call, just save the config string and reload the data page
    if($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $config = json_encode($_POST);
        file_put_contents('./assets/includes/tidepoolConfig.json', $config);
        header( "Location: indexx.html" );
    }

    //Otherwise this is a page load call so we need to load the html and
    //populate it values from the config json
    
    //1. Load and decode the config json
    $conf_json = file_get_contents('./assets/includes/tidepoolConfig.json');
    $config = json_decode($conf_json, true);
    //2. Load the settings html and populate its values
    //a. Load the html "template."
    $html = file_get_contents('./assets/includes/setup.html');
    
    //b. Replace the tags in the html
    $html = str_replace('[vemail]',    $config['useremail'], $html);
    $html = str_replace('[vpassword]', $config['password'], $html);
    $html = str_replace('[vusername]', $config['username'], $html);
    $html = str_replace('[vbirthdate]',$config['birthdate'], $html);
    
    //c. Show the html
    echo($html);