<?php

class FileTransfer {

    public static function fileToFolder($image) {
        $target_file = "images/products/" . basename($image["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $error = "";

        // Check if file already exists
        if (file_exists($target_file)) {
            $error .= "Er bestaat al een afbeelding met deze naam. Verander de naam en probeer het nog eens.<br>";
            $uploadOk = 0;
        }

        // Check file size
        if ($image["size"] > 500000) {
            $error .= "Het bestand is te groot. Probeer het bestand te verkleinen.<br>";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $error .= "Het bestand is geen JPG, JPEG of PNG.<br>";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $error .= "Het bestand is niet geüpload.<br>";
        } else {
            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                return true;
            } else {
                $error .= "Er is iets misgegaan bij het uploaden van het bestand.<br>";
            }
        }

        return $error;
    }

}