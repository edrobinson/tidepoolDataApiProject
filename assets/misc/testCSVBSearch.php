<?php 
/*
    test the csv search class
*/
    require 'classes/binaryCSVSearch.php';
    // public function __construct($file, $target, $index=0, $sep= ',')
   
    try{
        $bsearch = new binaryCSVSearch('assets/includes/http-status-codes.csv',400,0);
    }catch (Exception $e){
        echo 'Search creation failed: '.$e->getMessage(); die;
    }
    
    if(!$res = $bsearch->binarySearch())
    {
        echo 'Code not found.';
    }
    else
    {
        $parts = explode(',', $res);
        echo 'Code 400 translates to "'.$parts[1].'"';
    }
    
    
    