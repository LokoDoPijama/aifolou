<?php

function deleteOldestImageIfNeeded($dir) {
    $fi = new FilesystemIterator($dir, FilesystemIterator::SKIP_DOTS);
    $fileCount = iterator_count($fi);
    $fi->rewind();

    if ($fileCount >= 200) {
        $oldestFile = $fi->current();

        foreach ($fi as $file) {
            if ($file->getFilename() < $oldestFile->getFilename()) {
                $oldestFile = $file;
            }
        }

        unlink($oldestFile);
    }
}

$target_dir = "playerImages/";
$target_file = $target_dir . date('YmdHis') . "." . strtolower(pathinfo($_FILES["playerImage"]["name"],PATHINFO_EXTENSION));
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

$response = ['status' => 400, 'message' => ''];

if (!file_exists($target_dir)) {
    mkdir($target_dir);
}

// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["playerImage"]["tmp_name"]);
    if ($check !== false) {
        $response['message'] = "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        // echo "File is not an image.";
        $response['message'] = "File is not an image.";
        $uploadOk = 0;
    }
}

// Check if file already exists
if (file_exists($target_file)) {
    $response['message'] = "Sorry, file already exists.";
    $uploadOk = 0;
}

// Check file size
if ($_FILES["playerImage"]["size"] > 4000000) { // 4MB limit
    $response['message'] = "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    $response['message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  $response['message'] = "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    deleteOldestImageIfNeeded($target_dir);
    if (move_uploaded_file($_FILES["playerImage"]["tmp_name"], $target_file)) {
        $response['message'] = "The file ". htmlspecialchars( basename( $_FILES["playerImage"]["name"])). " has been uploaded.";
    } else {
        $response['message'] = "Sorry, there was an error uploading your file.";
        $uploadOk = 0;
    }
}

if ($uploadOk == 1) {
    $response['status'] = 200;
    $response['message'] = 'All is well!';
    $response['filename'] = $target_file;
}

echo json_encode($response);

http_response_code($response['status']);

?>