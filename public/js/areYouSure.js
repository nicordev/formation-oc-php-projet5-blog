var areYouSure = {

    wrapperElt: null,

    /**
     * Show a confirmation message
     *
     * @param message
     * @param callback
     * @param callbackParameter
     */
    show: function (message, callback, callbackParameter = null) {

        areYouSure.wrapperElt = document.createElement('div');

        var messageWrapElt = document.createElement('div'),
            messageElt = document.createElement('div'),
            btnWrapperElt = document.createElement('div'),
            yesBtnElt = createAButton('Oui', callback, callbackParameter),
            noBtnElt = createAButton('Annuler');

        areYouSure.wrapperElt.style.backgroundColor = 'rgba(0, 0, 0, 0.75)';
        areYouSure.wrapperElt.style.position = 'fixed';
        areYouSure.wrapperElt.style.top = '0';
        areYouSure.wrapperElt.style.left = '0';
        areYouSure.wrapperElt.style.width = '100%';
        areYouSure.wrapperElt.style.height = '100%';
        areYouSure.wrapperElt.style.display = 'flex';
        areYouSure.wrapperElt.style.justifyContent = 'center';
        areYouSure.wrapperElt.style.alignItems = 'center';

        messageWrapElt.style.backgroundColor = '#b9bbbe';
        messageWrapElt.style.border = '2px solid #0c5460';
        messageWrapElt.style.padding = '50px';

        messageElt.textContent = message;
        messageElt.style.color = '#0c5460';
        messageElt.style.margin = '0 0 20px 0';

        btnWrapperElt.appendChild(yesBtnElt);
        btnWrapperElt.appendChild(noBtnElt);

        messageWrapElt.appendChild(messageElt);
        messageWrapElt.appendChild(btnWrapperElt);
        areYouSure.wrapperElt.appendChild(messageWrapElt);

        document.body.appendChild(areYouSure.wrapperElt);

        /**
         * Create a button
         *
         * @param text
         * @param callback
         * @param callbackParameter
         * @returns {HTMLElement}
         */
        function createAButton(text, callback = null, callbackParameter = null) {

            var btnElt = document.createElement('button');
            btnElt.textContent = text;
            btnElt.addEventListener('click', function () {
                if (callback) {
                    callback(callbackParameter);
                }
                destroy();
            });

            btnElt.style.backgroundColor = '#b9bbbe';
            btnElt.style.color = '#0c5460';
            btnElt.style.border = '2px solid #0c5460';
            btnElt.style.borderRadius = '5px';
            btnElt.style.margin = '0 20px';

            return btnElt;
        }

        /**
         * Remove the message
         */
        function destroy() {

            areYouSure.wrapperElt.remove();
        }
    }
}