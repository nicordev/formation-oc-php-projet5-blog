(function () {

    let newTagBtnElt = document.getElementById('new-tag-btn');

    newTagBtnElt.addEventListener('click', function(evt) {
        evt.preventDefault();

        let newTag = document.getElementById('new-tag').value;

        if (isNewTag(newTag))
            addNewTag(newTag);
    });

    /**
     * Add a new tag in the tags list
     *
     * @param newTag
     */
    function addNewTag(newTag)
    {
        let newTagElt = createTagElt(newTag);
        let availableTagsElt = document.getElementById("available-tags");

        availableTagsElt.appendChild(newTagElt);
    }

    /**
     * Create an element holding the tag
     *
     * @param tag
     * @returns {HTMLElement}
     */
    function createTagElt(tag)
    {
        let tagElt = document.createElement('li');
        let checkboxElt = myApp.elementsBuilder.createCheckboxElt('tags[]', tag, true);
        let labelElt = myApp.elementsBuilder.createLabelElt(tag, tag);

        labelElt.setAttribute('class', 'available-tag');

        tagElt.appendChild(checkboxElt);
        tagElt.appendChild(labelElt);

        return tagElt;
    }

    /**
     * Check if the tag is new
     *
     * @param tag
     * @returns {boolean}
     */
    function isNewTag(tag)
    {
        let availableTags = getAvailableTags();

        if (availableTags && availableTags.length > 0) {
            return !availableTags.includes(tag);
        }
        return true;
    }

    /**
     * Get all available tags
     *
     * @returns {Array}
     */
    function getAvailableTags()
    {
        let availableTags = [];
        let availableTagsElts = document.getElementsByClassName('available-tag');

        if (availableTagsElts.length === 0) {
            return false;
        }

        for (let i = 0, size = availableTagsElts.length; i < size; i++) {
            availableTags.push(availableTagsElts[i].textContent);
        }

        return availableTags;
    }
})();