var myCropFrame = {
    update: function () {
        var frameElt = document.getElementById('image-preview'),
            originX = document.getElementById("crop-x").value,
            originY = document.getElementById("crop-y").value,
            width = document.getElementById("crop-width").value,
            height = document.getElementById("crop-height").value;

        console.log(width, height);

        frameElt.style.width = width + "px";
        frameElt.style.height = height + "px";
        frameElt.style.backgroundPositionX = -originX + "px";
        frameElt.style.backgroundPositionY = -originY + "px";
    }
}