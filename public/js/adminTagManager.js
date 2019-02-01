(function () {

    let newTagBtn = document.getElementById('new-tag-btn');

    const AVAILABLE_TAG_CLASS = 'available-tag';

    newTagBtn.addEventListener('click', function(evt) {
        evt.preventDefault();
        addTag();
    });

    /**
     * Add a tag in the list of tags if its new
     */
    function addTag()
    {
        let newTag = document.getElementById('new-tag-input').value;

        if (isNewTag(newTag)) {
            addTagInTheList(newTag);
        }
    }

    /**
     * Check if a tag is in the list of tags
     *
     * @param tag
     * @returns {boolean}
     */
    function isNewTag(tag)
    {
        let tagElts = document.getElementsByClassName(AVAILABLE_TAG_CLASS);

        for (let i = 0, size = tagElts.length; i < size; i++) {
            if (tag === tagElts[i].value) {
                return false;
            }
        }
        return true;
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
        let tagHiddenInfoElt = createInputElt('hidden', 'tag_ids[]', 'new');
        let tagInputElt = createInputElt('text', '', tag);
        let closingCrossElt = createClosingCrossElt(); // From erase.js

        tagInputElt.setAttribute('class', AVAILABLE_TAG_CLASS);

        tagElt.appendChild(tagHiddenInfoElt);
        tagElt.appendChild(tagInputElt);
        tagElt.appendChild(closingCrossElt);

        return tagElt;
    }

    /**
     * Create an input DOM element
     *
     * @param type
     * @param name
     * @param value
     * @returns {HTMLElement}
     */
    function createInputElt(type = 'text', name = '', value = '')
    {
        let elt = document.createElement('input');
        elt.type = type;
        elt.name = name;
        elt.value = value;

        return elt;
    }
})();