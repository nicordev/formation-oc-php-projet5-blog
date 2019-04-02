var myCommentForm = {

    /**
     * Show a form to send a response to a comment
     *
     * @param evt
     * @param connectedMemberId
     * @param parentId
     * @param postId
     */
    showForm: function (evt, connectedMemberId, parentId, postId) {
        evt.target.style.display = 'none';
        var wrapperElt = evt.target.parentElement;
        var responseFormElt = buildResponseFormElt(connectedMemberId, parentId, postId);

        wrapperElt.appendChild(responseFormElt);

        /**
         * Build a form to send a response
         *
         * @returns {HTMLElement}
         */
        function buildResponseFormElt(connectedMemberId, parentId, postId) {

            var responseFormElt = document.createElement('form');
            var commentElt = document.createElement('textarea');
            var authorIdElt = myApp.elementBuilder.createInputElt('hidden', 'author-id', connectedMemberId);
            var parentIdElt = myApp.elementBuilder.createInputElt('hidden', 'parent-id', parentId);
            var postIdElt = myApp.elementBuilder.createInputElt('hidden', 'post-id', postId);
            var submitBtnElt = myApp.elementBuilder.createInputElt('submit', '', 'Répondre');

            responseFormElt.action = '/add-comment';
            responseFormElt.method = 'post';

            commentElt.classList.add('form-control');
            commentElt.setAttribute('name', 'comment');

            submitBtnElt.classList.add('btn');
            submitBtnElt.classList.add('btn-small');

            responseFormElt.appendChild(authorIdElt);
            responseFormElt.appendChild(parentIdElt);
            responseFormElt.appendChild(postIdElt);
            responseFormElt.appendChild(commentElt);
            responseFormElt.appendChild(submitBtnElt);

            return responseFormElt;
        }
    }
}