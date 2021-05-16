<?php
/*
    Tidepool Data Reporting Configuration
*/
    $config = json_encode($post);
    var_dump($config);
    file_put_contents('./assets/include/tidepoolConfig.json', $config);
    include('indexx.html');

        