
let newTagBtnElt = document.getElementById('new-tag-btn');
let tagListFormElt = document.getElementById('admin-tag-list-form');

const AVAILABLE_TAG_CLASS = 'available-tag';
const BAD_TAG_COLOR = 'rgb(255, 178, 178)';
const GOOD_TAG_COLOR = 'white';

/**
 * Highlight any incorrect Tag from the list of tags
 */
function highlightIncorrectTags()
{
    let tagElts = document.getElementsByClassName(AVAILABLE_TAG_CLASS);
    let tags = [];
    let numberOfTags = tagElts.length;
    let i;

    // Let's get the names of the tags first
    for (i = 0; i < numberOfTags; i++) {
        tagElts[i].style.backgroundColor = GOOD_TAG_COLOR; // Reset the color
        if (!tagElts[i].value) {
            tagElts[i].style.backgroundColor = BAD_TAG_COLOR;
        } else {
            tags.push(tagElts[i].value);
        }
    }

    // Counting the occurrences of each tag
    for (i = 0; i < numberOfTags; i++) {
        if (countOccurrences(tags, tags[i]) > 1) {
            tagElts[i].style.backgroundColor = BAD_TAG_COLOR;
        }
    }
}

// Update the list of tags

tagListFormElt.addEventListener('submit', function(evt) {
    evt.preventDefault();

    if (tagListIsCorrect()) {
        console.log('List OK')
        // evt.target.submit(); // TODO check this syntax
    } else {
        console.log('Erreur dans la liste');
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
    let numberOfTags = tagElts.length;
    let i;

    if (!tagElts) {
        return false;
    }

    // Let's get the names of the tags first
    for (i = 0; i < numberOfTags; i++) {
        if (!tagElts[i].value) {
            return false;
        }
        tags.push(tagElts[i].value);
    }

    // Counting the occurrences of each tag
    for (i = 0; i < numberOfTags; i++) {
        if (countOccurrences(tags, tags[i]) > 1) {
            return false;
        }
    }
    return true;
}

// Add a new tag in the list

newTagBtnElt.addEventListener('click', function(evt) {
    evt.preventDefault();
    addTag();
});

/**
 * Count the number of occurrences of a value in an array
 *
 * @param theArray
 * @param theValue
 * @returns {number}
 */
function countOccurrences(theArray, theValue)
{
    let count = 0;

    for (let i = 0, size = theArray.length; i < size; i++) {
        if (theArray[i] === theValue) {
            count++;
        }
    }
    return count;
}

/**
 * Add a tag in the list of tags if its new
 */
function addTag()
{
    let newTag = document.getElementById('new-tag-input').value;

    if (isUniqueTag(newTag)) {
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
    tagInputElt.setAttribute('onkeyup', 'highlightIncorrectTags()');

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
