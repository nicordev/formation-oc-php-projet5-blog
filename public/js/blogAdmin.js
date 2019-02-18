

/*
* Tags
*
*
*
* */

const TAG_CLASSES = 'available-tag text-center admin-link';

/**
 * Highlight any incorrect tag from the list of tags
 */
function highlightIncorrectTags()
{
    let tagElts = document.getElementsByClassName(TAG_CLASSES);

    myApp.formatBadElements(tagElts, 'bad', true, true, true);
}

// Update the list of tags

let tagListFormElt = document.getElementById('admin-tag-list-form');

tagListFormElt.addEventListener('submit', submitTagList);

/**
 * Submit the list of tags if everything is fine
 *
 * @param evt the event
 */
function submitTagList(evt)
{
    evt.preventDefault();
    let tagElts = document.getElementsByClassName(TAG_CLASSES);

    if (myApp.elementValuesAreCorrect(tagElts)) {
        evt.target.submit();
    } else {
        console.log('Erreur dans la liste des tags');
    }
}


// Add a new tag in the list

let newTagBtnElt = document.getElementById('new-tag-btn');

newTagBtnElt.addEventListener('click', addTag);

/**
 * Add a tag in the list of tags if its a new one
 */
function addTag()
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
        let tagElts = document.getElementsByClassName(TAG_CLASSES);

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
        let tagInputElt = myApp.elementBuilder.createInputElt('text', '', tag);
        let deleteBtnElt = myApp.elementBuilder.createDeleteBtnElt();

        tagInputElt.setAttribute('class', TAG_CLASSES);
        tagInputElt.setAttribute('onkeyup', 'highlightIncorrectTags()');
        tagInputElt.setAttribute('name', 'tag_names[]');

        tagElt.appendChild(tagHiddenInfoElt);
        tagElt.appendChild(tagInputElt);
        tagElt.appendChild(deleteBtnElt);

        return tagElt;
    }
}