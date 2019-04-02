var myElementInfo = {

    /**
     * Show the characteristics of the preview image
     *
     * @param showId
     * @param targetId
     */
    show: function (showId, targetId) {
        document.getElementById(showId).textContent = myElementInfo.width(targetId) + 'x' + myElementInfo.height(targetId);
    },

    /**
     * Get the width of the target element
     *
     * @param targetId
     * @returns {number}
     */
    width: function (targetId) {
        return document.getElementById(targetId).clientWidth;
    },

    /**
     * Get the height of the target element
     *
     * @param targetId
     * @returns {number}
     */
    height: function (targetId) {
        return document.getElementById(targetId).clientHeight;
    }
}