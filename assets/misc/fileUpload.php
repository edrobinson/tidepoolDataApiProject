<?php

    $target_dir = "./uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $msg = '';
    //The Tidepool download always has the same name so
    //if file already exists, delete it.
    if (file_exists($target_file))
    {
      unlink($target_file);
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
      $msg .= "Your file is too large.<br>";
      $uploadOk = 0;
    }

    // Allow only json files.
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if($fileType != "json")
    {
      $msg .= 'The file type must be json.<br>';
      $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      $msg .= "Your file was not uploaded.";
      echo $msg;
    // if everything is ok, try to upload file
    } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "1";
      } else {
        echo "There was an error uploading your file.";
      }
    }
?>