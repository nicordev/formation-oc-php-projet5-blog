

var myTagHandler = {

    AVAILABLE_TAGS_CLASS: 'available-tag',

    /**
     * Add event listeners for tags
     */
    setTagEventListeners: function () {
        let newTagBtnElt = document.getElementById('new-tag-btn');

        newTagBtnElt.addEventListener('click', function(evt) {
            evt.preventDefault();
            myTagHandler.addNewTag();
        });
    },

    /**
     * Add a tag in the tags list if it's a new one
     */
    addNewTag: function () {
        let newTag = document.getElementById('new-tag').value;

        if (myTagHandler.isNewTag(newTag)) {
            myTagHandler.addTag(newTag);
        }
    },

    /**
     * Add a tag in the tags list
     *
     * @param newTag
     */
    addTag: function (newTag)
    {
        let newTagElt = myTagHandler.createTagElt(newTag);
        let availableTagsElt = document.getElementById("available-tags");

        availableTagsElt.appendChild(newTagElt);
    },

    /**
     * Create an element holding the tag
     *
     * @param tag
     * @returns {HTMLElement}
     */
    createTagElt: function (tag)
    {
        let tagElt = document.createElement('li');
        let checkboxElt = myApp.elementBuilder.createCheckboxElt('tags[]', tag, true);
        let labelElt = myApp.elementBuilder.createLabelElt(tag, tag);
        let eraseBtnElt = myApp.elementBuilder.createDeleteBtnElt(true);

        labelElt.setAttribute('class', 'available-tag right-label green');

        tagElt.appendChild(checkboxElt);
        tagElt.appendChild(labelElt);
        tagElt.appendChild(eraseBtnElt);

        return tagElt;
    },

    /**
     * Check if the tag is new
     *
     * @param tag
     * @returns {boolean}
     */
    isNewTag: function (tag)
    {
        let availableTags = myTagHandler.getAvailableTags();

        if (availableTags && availableTags.length > 0) {
            return !availableTags.includes(tag);
        }
        return true;
    },

    /**
     * Get all available tags
     *
     * @returns {Array}
     */
    getAvailableTags: function ()
    {
        let availableTags = [];
        let availableTagsElts = document.getElementsByClassName(myTagHandler.AVAILABLE_TAGS_CLASS);

        if (availableTagsElts.length === 0) {
            return false;
        }

        for (let i = 0, size = availableTagsElts.length; i < size; i++) {
            availableTags.push(availableTagsElts[i].textContent);
        }

        return availableTags;
    },

    /**
     * Add a tag in the list when hitting enter
     *
     * @param event
     */
    addTagOnEnter: function (event) {

        if (event.keyCode === 13) {
            let newTag = document.getElementById('new-tag').value;
            myTagHandler.addNewTag(newTag);
            event.preventDefault();
        }

        return event.keyCode !== 13;
    }
}
