var myNavigationHelper = {

    /**
     * Save the url in the session
     *
     * @param url
     */
    saveUrl: function (url) {
        sessionStorage.setItem('page', url);
    },

    /**
     * Retrieve the url from the session
     *
     * @returns {string}
     */
    getUrl: function () {
        return sessionStorage.getItem('page');
    }
}