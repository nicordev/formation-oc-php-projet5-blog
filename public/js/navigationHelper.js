var myNavigationHelper = {
    saveUrl: function (url) {
        sessionStorage.setItem('page', url);
    },

    getUrl: function () {
        return sessionStorage.getItem('page');
    }
}