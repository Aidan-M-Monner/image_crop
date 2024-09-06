<?php
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        $source = $_FILES['file']['tmp_name'];
        move_uploaded_file($source, "image.jpg");
        header("Location: interface.php");
        die;
    }
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="file"/>
    <input type="submit" name="" value="upload"/>
</form>