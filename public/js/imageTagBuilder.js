
var myImageTagBuilder = {

    /**
     * Build an <img> tag with a <figcaption> and within a <figure>
     *
     * @param imagePath
     */
    build: function (imagePath) {

        var imageTagBuilderElt = document.getElementById("image-tag-builder");

        imageTagBuilderElt.value = "<figure class=\"\">\n\t" +
            "<img src=\"" + imagePath + "\" alt=\"Une image\">\n\t" +
            "<figcaption>Légende</figcaption>" +
            "\n</figure>";
    }
}
