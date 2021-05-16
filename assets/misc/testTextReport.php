<?php
/* 
    Test the text report class
*/

    require 'classes/textReport.php';
    $data = file_get_contents('userinput.txt');
    $report = new textReport($data);
    $report->generateReport();