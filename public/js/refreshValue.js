
/**
 * Refresh the value attribute
 *
 * @param evt
 */
function refreshValue(evt)
{
    let elt = evt.target;
    elt.setAttribute('value', elt.value);
}
