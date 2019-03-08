var myEditor = {

    openText: "Ouvrir l'éditeur",
    closeText: "Fermer l'éditeur",

    /**
     * Switch the editor on or off
     *
     * @param event
     * @param elementId
     */
    switchEditor: function (event, elementId) {

        var btnElt = event.target;

        if (btnElt.textContent === myEditor.openText) {
            myEditor.runTinyMCE(elementId);
            btnElt.textContent = myEditor.closeText;
        } else {
            myEditor.closeTinyMCE(elementId);
            btnElt.textContent = myEditor.openText;
        }
    },

    /**
     * Launch TinyMCE in a DOM element
     */
    runTinyMCE: function (elementId) {
        tinymce.init({ selector: elementId });
    },

    /**
     * Remove TinyMCE from a DOM element
     */
    closeTinyMCE: function (elementId) {
        tinymce.remove(elementId);
    }
}