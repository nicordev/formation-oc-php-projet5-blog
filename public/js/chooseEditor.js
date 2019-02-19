var myEditor = {

    openText: "Ouvrir l'éditeur",
    closeText: "Fermer l'éditeur",

    /**
     * Switch the editor on or off
     *
     * @param event
     */
    switchEditor: function (event) {

        var btnElt = event.target;

        if (btnElt.textContent === myEditor.openText) {
            myEditor.runTinyMCE();
            btnElt.textContent = myEditor.closeText;
        } else {
            myEditor.closeTinyMCE();
            btnElt.textContent = myEditor.openText;
        }
    },

    /**
     * Launch TinyMCE
     */
    runTinyMCE: function () {
        tinymce.init({ selector:'#post-content' });
    },

    /**
     * Remove TinyMCE
     */
    closeTinyMCE: function () {
        tinymce.remove("#post-content");
    }
}