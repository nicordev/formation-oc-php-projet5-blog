/**
 * Some functions to erase stuff
 */

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

    eraseElementOnClick(closingCrossElt);

    return closingCrossElt;
}

/**
 * Erase an element on click by emptying the parent node inner HTML
 *
 * @param element
 */
function eraseElementOnClick(element)
{
    element.addEventListener('click', eraseElementFromEvent);
}

/**
 * Erase an element determined by an event by emptying the parent node inner HTML
 *
 * @param evt
 */
function eraseElementFromEvent(evt)
{
    eraseElement(evt.target);
}

/**
 * Erase an element by emptying the parent node inner HTML
 *
 * @param element
 */
function eraseElement(element)
{
    element.parentNode.innerHTML = '';
}