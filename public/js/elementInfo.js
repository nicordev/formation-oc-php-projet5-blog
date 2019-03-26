var myElementInfo = {

    show: function (showId, targetId) {
        document.getElementById(showId).textContent = myElementInfo.width(targetId) + 'x' + myElementInfo.height(targetId);
    },

    width: function (targetId) {
        return document.getElementById(targetId).clientWidth;
    },

    height: function (targetId) {
        return document.getElementById(targetId).clientHeight;
    }
}