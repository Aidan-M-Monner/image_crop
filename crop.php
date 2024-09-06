<?php 
    // Define variables
    $source = imagecreatefromjpeg("image.jpg");
    $dest = imagecreatetruecolor(400, 400);
    
    imagecopyresampled($dest, $source, 0, 0, $_GET['x'], $_GET['y'], 400, 400, $_GET['width'], $_GET['height']);
    imagejpeg($dest, "cropped.jpg", 98);

    echo "<img src='cropped.jpg?" . rand(0,100) . "' style='width: 400px' >";