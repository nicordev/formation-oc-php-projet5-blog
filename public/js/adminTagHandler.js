
let newTagBtnElt = document.getElementById('new-tag-btn');
let tagListFormElt = document.getElementById('admin-tag-list-form');

const AVAILABLE_TAG_CLASS = 'available-tag';

/**
 * Highlight any incorrect Tag from the list of tags
 */
function highlightIncorrectTags()
{
    let tagElts = document.getElementsByClassName(AVAILABLE_TAG_CLASS);

    myApp.formatBadElements(tagElts, 'bad', true, true, true);
}

// Update the list of tags

tagListFormElt.addEventListener('submit', function(evt) {
    evt.preventDefault();

    if (tagListIsCorrect()) {
        evt.target.submit();
    } else {
        console.log('Erreur dans la liste des tags');
    }
});

/**
 * Check if the list of tags is correct
 *
 * @returns {boolean}
 */
function tagListIsCorrect()
{
    let tagElts = document.getElementsByClassName(AVAILABLE_TAG_CLASS);
    let tags = [];

    if (!tagElts) {
        return false;
    }

    for (let i = 0, numberOfTags = tagElts.length; i < numberOfTags; i++) {
        if (!tagElts[i].value) {
            return false;
        }
        tags.push(tagElts[i].value);
    }

    return !myApp.hasForbiddenValues(tags, null, false, false);
}

// Add a new tag in the list

newTagBtnElt.addEventListener('click', function(evt) {
    evt.preventDefault();
    addTag();
});

/**
 * Add a tag in the list of tags if its new
 */
function addTag()
{
    let newTag = document.getElementById('new-tag-input').value;

    if (newTag && isUniqueTag(newTag)) {
        addTagInTheList(newTag);
    }
}

/**
 * Check if a tag is in the list of tags
 *
 * @param tag
 * @returns {boolean}
 */
function isUniqueTag(tag)
{
    let tagElts = document.getElementsByClassName(AVAILABLE_TAG_CLASS);

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
    let tagHiddenInfoElt = myApp.createInputElt('hidden', 'tag_ids[]', 'new');
    let tagInputElt = myApp.createInputElt('text', '', tag);
    let closingCrossElt = myApp.eraseTools.createClosingCrossElt();

    tagInputElt.setAttribute('class', AVAILABLE_TAG_CLASS);
    tagInputElt.setAttribute('onkeyup', 'highlightIncorrectTags()');
    tagInputElt.setAttribute('name', 'tag_names[]');

    tagElt.appendChild(tagHiddenInfoElt);
    tagElt.appendChild(tagInputElt);
    tagElt.appendChild(closingCrossElt);

    return tagElt;
}
