<?php
/*
    This page is displayed by a call from the main page
    javascript. The main page uses ajax for communication
    and must reply to the call. It cannot display the report
    by itself. The js redirects to this page.
    
    This is the commonly used method to display a pdf from a  web page...
*/
    session_start();
    
    $pdfname = $_SESSION['pdfname'];

    //This code is explained a lot on the net...
    header('Content-type:application/pdf');
    header('Content-disposition: inline; filename="'.$pdfname.'"');
    header('content-Transfer-Encoding:binary');
    header('Accept-Ranges:bytes');
    readfile($pdfname);

