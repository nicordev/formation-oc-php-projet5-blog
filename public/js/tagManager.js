(function () {
    let newTagBtnElt = document.getElementById('new-tag-btn');

    newTagBtnElt.addEventListener('click', function(evt) {
        evt.preventDefault();

        let newTag = document.getElementById('new-tag').value;

        if (isNewTag(newTag))
            addNewTag(newTag);
    });

    /**
     * Add a new tag in the selected tags list
     *
     * @param newTag
     */
    function addNewTag(newTag)
    {
        let newTagElt = createTagElt(newTag);
        let selectedTagsElt = document.getElementById("selected-tags");

        selectedTagsElt.appendChild(newTagElt);
    }

    /**
     * Create an element holding the tag
     *
     * @param tag
     * @returns {HTMLElement}
     */
    function createTagElt(tag)
    {
        let wrapperElt = document.createElement('li');
        let tagElt = document.createElement('span');
        let hiddenInputElt = createHiddenInputElt('tag', tag);
        let closingCrossElt = createClosingCrossElt();

        tagElt.setAttribute('class', 'selected-tag');
        tagElt.textContent = tag;

        wrapperElt.appendChild(closingCrossElt);
        wrapperElt.appendChild(tagElt);
        wrapperElt.appendChild(hiddenInputElt);

        return wrapperElt;
    }

    /**
     * Create a closing cross which erase the parent node on click
     *
     * @returns {HTMLElement}
     */
    function createClosingCrossElt()
    {
        let closingCrossElt = document.createElement('span');
        closingCrossElt.textContent = 'X';
        closingCrossElt.setAttribute('class', 'closing-cross');

        closingCrossElt.addEventListener('click', function(evt) {
            evt.target.parentNode.innerHTML = '';
        });

        return closingCrossElt;
    }

    /**
     * Create a hidden input
     *
     * @returns {HTMLElement}
     */
    function createHiddenInputElt(name, value)
    {
        let hiddenInputElt = document.createElement('input');
        hiddenInputElt.type = 'hidden';
        hiddenInputElt.name = name;
        hiddenInputElt.value = value;

        return hiddenInputElt;
    }

    /**
     * Check if the tag is new
     *
     * @param tag
     * @returns {boolean}
     */
    function isNewTag(tag)
    {
        let selectedTags = getSelectedTags();

        if (selectedTags && selectedTags.length > 0) {
            return !selectedTags.includes(tag);
        }
        return true;
    }

    /**
     * Get all selected tags
     *
     * @returns {Array}
     */
    function getSelectedTags()
    {
        let selectedTags = [];
        let selectedTagsElts = document.getElementsByClassName('selected-tag');

        if (selectedTagsElts.length === 0) {
            return false;
        }

        for (let i = 0, size = selectedTagsElts.length; i < size; i++) {
            selectedTags.push(selectedTagsElts[i].textContent);
        }

        return selectedTags;
    }
})();