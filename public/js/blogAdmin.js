
var myBlogAdmin = {

    /*
    * Tags
    *
    *
    *
    * */

    TAG_CLASSES: 'available-tag text-center admin-tag underline-input green',

    /**
     * Add event listeners for tags
     */
    setTagEventListeners: function ()
    {
        // Update the list of tags

        let tagListFormElt = document.getElementById('admin-tag-list-form');

        if (tagListFormElt) {
            tagListFormElt.addEventListener('submit', myBlogAdmin.submitTagList);
        }

        // Add a new tag in the list

        let newTagBtnElt = document.getElementById('new-tag-btn');

        if (newTagBtnElt) {
            newTagBtnElt.addEventListener('click', myBlogAdmin.addTag);
        }
    },

    /**
     * Highlight any incorrect tag from the list of tags
     */
    highlightIncorrectTags: function ()
    {
        let tagElts = document.getElementsByClassName(myBlogAdmin.TAG_CLASSES);
        console.log(tagElts);

        myApp.formatBadElements(tagElts, 'bad', true, true, true);
    },

    // Update the list of tags

    /**
     * Submit the list of tags if everything is fine
     *
     * @param evt the event
     */
    submitTagList: function (evt)
    {
        evt.preventDefault();
        let tagElts = document.getElementsByClassName(myBlogAdmin.TAG_CLASSES);

        if (myApp.elementValuesAreCorrect(tagElts)) {
            evt.target.submit();
        } else {
            console.log('Erreur dans la liste des tags');
        }
    },

    /**
     * Add a tag in the list of tags if its a new one
     */
    addTag: function ()
    {
        let newTag = document.getElementById('new-tag-input').value;

        if (newTag && isUniqueTag(newTag)) {
            addTagInTheList(newTag);
        }

        /**
         * Check if a tag is in the list of tags
         *
         * @param tag
         * @returns {boolean}
         */
        function isUniqueTag(tag)
        {
            let tagElts = document.getElementsByClassName(myBlogAdmin.TAG_CLASSES);

            return !myApp.isInElements(tag, tagElts);
        }

        /**
         * Add a tag in the list of tags
         *
         * @param tag
         */
        function addTagInTheList(tag)
        {
            let newTagElt = createTagElt(tag);
            let newTagInputElt = document.getElementById('new-tag-input-li');

            newTagInputElt.insertAdjacentElement('beforebegin', newTagElt);
        }

        /**
         * Create a full DOM element for a tag
         *
         * @param tag
         * @returns {HTMLElement}
         */
        function createTagElt(tag)
        {
            let tagElt = document.createElement('li');
            let tagHiddenInfoElt = myApp.elementBuilder.createInputElt('hidden', 'tag_ids[]', 'new');
            let sharpElt = document.createElement('span');
            let tagInputElt = myApp.elementBuilder.createInputElt('text', '', tag);
            let deleteBtnElt = myApp.elementBuilder.createDeleteBtnElt(true);

            sharpElt.textContent = '#';

            tagInputElt.setAttribute('class', myBlogAdmin.TAG_CLASSES);
            tagInputElt.setAttribute('onkeyup', 'highlightIncorrectTags()');
            tagInputElt.setAttribute('name', 'tag_names[]');

            tagElt.appendChild(tagHiddenInfoElt);
            tagElt.appendChild(sharpElt);
            tagElt.appendChild(tagInputElt);
            tagElt.appendChild(deleteBtnElt);

            return tagElt;
        }
    },

    /**
     * Add a tag in the list when hitting enter
     *
     * @param event
     */
    addTagOnEnter: function (event) {

        if (event.keyCode === 13) {
            myBlogAdmin.addTag();
            event.preventDefault();
        }

        return event.keyCode !== 13;
    }
};