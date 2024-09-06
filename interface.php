<style>
    #image {
        position: relative;
        -khtml-user-select: none;
        -o-user-select: none;
        -moz-user-select: none;
        -webkit-user-select: none;
        user-select: none;
    }

    #container {
        height: 400px;
        margin: auto;
        overflow: hidden;
        width: 400px;
    }

    #cropper {
        background-image:url(crop_area.png);
        background-size: 100% 100%;
        cursor: move;
        height: 400px;
        position: absolute;
        width: 400px;
    }

    #range {
        display: block;
        margin: auto;
        width: 400px;
    }

    #output {
        margin: auto;
        width: 400px;
    }
</style>

<div id="container" onmousedown="mouseDown_on(event)" onmouseup="mouseDown_off(event)" onmouseenter="mouseMove_on(event)" onmouseleave="mouseMove_off(event)">
    <img id="image" src="image.jpg" style="width: 400px">
    <div id="cropper"></div>
</div>
<br>
<input id="range" type="range" min="10" max="40" onmousemove="resize_image(event)">
<br>
<button onclick="crop(event)">Crop</button>
<br>
<div id="output"></div>


<script type="text/javascript">
    // Variables
    var image = document.getElementById("image");
    var container = document.getElementById("container");
    var cropper = document.getElementById("cropper");
    var range = document.getElementById("range");
    var output = document.getElementById("output");

    var mouseMove = false;
    var mouseDown = false;

    var initMouseX = 0;
    var initMouseY = 0;

    var initImageX = 0;
    var initImageY = 0;

    var ratio = 1;
    var margin = 50;

    cropper.style.top = container.offsetTop;
    cropper.style.left = container.offsetLeft;

    reset_image();

    // Get image size
    var originalImageWidth = image.clientWidth;
    var originalImageHeight = image.clientHeight;

    window.onmousemove = function(event) {
        // Move Image With Mouse Move
        if(mouseMove && mouseDown) {
            // Movement of x and y
            var x = event.clientX - initMouseX;
            var y = event.clientY - initMouseY;

            // Current position + movement
            x = initImageX + x;
            y = initImageY + y;

            // Position of image
            if(x > margin) {x = margin}
            if(y > margin) {y = margin}

            xlimit = container.clientWidth - image.clientWidth - margin;
            if(x < xlimit) {x = xlimit}
            ylimit = container.clientHeight - image.clientHeight - margin;
            if(y < ylimit) {y = ylimit}

            image.style.left = x;
            image.style.top = y;
        }
    }

    // Keeps image in place
    window.onmouseup = function(event) {
        mouseDown = false;
    }

    // Resize Image
    function resize_image() {
        // Resizes Image from Top-Left
        var w = image.clientWidth;
        var h = image.clientHeight;

        image.style.width = (range.value / 10) * originalImageWidth;
        image.style.height = (range.value / 10) * originalImageHeight;

        // Move Image to Resize From Center
        var w2 = image.clientWidth;
        var h2 = image.clientHeight;

        if(w - w2 != 0) {
            var diff = (w - w2) / 2;
            var diff2 = (h - h2) / 2;

            var x = (image.offsetLeft - container.offsetLeft) + diff;
            var y = (image.offsetTop - container.offsetTop) + diff2;

            if(x > margin) {x = margin}
            if(y > margin) {y = margin}

            xlimit = container.clientWidth - image.clientWidth - margin;
            if(x < xlimit) {x = xlimit}
            ylimit = container.clientHeight - image.clientHeight - margin;
            if(y < ylimit) {y = ylimit}

            image.style.left = x;
            image.style.top = y;
        }
    }

    // Puts image in proper place within cropper
    function reset_image() {
        // If image's width is > natural height
        if(image.naturalWidth > image.naturalHeight) {
            ratio = image.naturalWidth / image.naturalHeight;

            image.style.height = container.clientHeight - (margin * 2);
            image.style.width = (container.clientWidth - (margin * 2)) * ratio;

            image.style.top = margin;

            // Centers image
            var extra = (image.clientWidth - container.clientWidth) / 2;
            image.style.left = extra * -1;
        } else {
            ratio = image.naturalHeight / image.naturalWidth;

            image.style.width = container.clientWidth - (margin * 2);
            image.style.height = (container.clientHeight - (margin * 2)) * ratio;

            image.style.left = margin;

            // Centers image
            var extra = (image.clientHeight - container.clientHeight) / 2;
            image.style.top = extra * -1;
        }

        range.value = 10;
    } 

    // Mouse Clicked
    function mouseDown_on(event) {
        mouseDown = true;

        // Position of mouse
        initMouseX = event.clientX;
        initMouseY = event.clientY;

        // Position of image
        initImageX = image.offsetLeft - container.offsetLeft;
        initImageY = image.offsetTop - container.offsetTop;
    }

    function mouseDown_off() {
        mouseDown = false;
    }
    
    function mouseMove_on() {
        mouseMove = true;
    }

    function mouseMove_off() {
        mouseMove = false;
    }

    // Crop Image 
    function crop() {
        if(image.naturalWidth > image.naturalHeight) {
            ratio = image.naturalHeight / (container.clientHeight - (margin * 2));
        } else {
            ratio = image.naturalWidth / (container.clientWidth - (margin * 2));
        }
        var x1 = image.style.left;
        x1 = x1.replace("px", "");
        x1 = x1 - margin;
        if(x1 < 0) {x1 = x1 * -1}

        var y1 = image.style.top;
        y1 = y1.replace("px", "");
        y1 = y1 - margin;
        if(y1 < 0) {y1 = y1 * -1}

        var x2 = x1 + (container.clientWidth - (margin * 2));
        var y2 = y1 + (container.clientHeight - (margin * 2));

        var width = (x2 - x1) * ratio;
        var height = (y2 - y1) * ratio;

        x1 = x1 * ratio;
        y1 = y1 * ratio;

        // Zoom factor
        var zoomFactor = (range.value / 10);
        x1 = x1 / zoomFactor;
        y1 = y1 / zoomFactor;
        width = width / zoomFactor;
        height = height / zoomFactor;

        xhr = new XMLHttpRequest();
        xhr.open("GET", "crop.php?x=" + x1 + "&y=" + y1 + "&width=" + width + "&height=" + height, true);

        xhr.onload = function() {
            if(xhr.status === 200) {
                output.innerHTML = xhr.responseText;
            }
        }
        xhr.send();
    }
</script>