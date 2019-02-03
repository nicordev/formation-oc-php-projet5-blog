var myApp = {
    /**
     * Format elements regarding to criteria
     *
     * @param elements
     * @param badClass
     * @param checkValues
     * @param checkIfEmpty
     * @param checkIfDuplicate
     */
    formatBadElements: function(elements, badClass, checkValues = true, checkIfEmpty = true, checkIfDuplicate = true) {
        let data = [];

        if (checkValues) {
            for (let element of elements) {
                // Reset badClass
                if (badClass in element.classList) {
                    element.classList.remove(badClass);
                }
                if (checkIfEmpty && !element.value) {
                    element.classList.add(badClass);
                    continue;
                }
                data.push(element.value);
            }
        } else {
            for (let element of elements) {
                // Reset badClass
                if (badClass in element.classList) {
                    element.classList.remove(badClass);
                }
                if (checkIfEmpty && !element.textContent) {
                    element.classList.add(badClass);
                    continue;
                }
                data.push(element.textContent);
            }
        }

        if (checkIfDuplicate) {
            // Counting the occurrences of each data
            for (let i = 0, size = data.length; i < size; i++) {
                if (this.countOccurrences(data, data[i]) > 1) {
                    elements[i].classList.add(badClass);
                }
            }
        }
    },

    /**
     * Count the number of occurrences of a value in an array
     *
     * @param theArray
     * @param theValue
     * @returns {number}
     */
    countOccurrences: function (theArray, theValue) {
        let count = 0;

        for (let i = 0, size = theArray.length; i < size; i++) {
            if (theArray[i] === theValue) {
                count++;
            }
        }
        return count;
    },

    /**
     * Check if the array contains forbidden values
     *
     * @param arrayToCheck
     * @param forbiddenValues
     * @param allowEmptyValues
     * @param allowDuplicates
     * @returns {boolean}
     */
    hasForbiddenValues: function (arrayToCheck, forbiddenValues = null, allowEmptyValues = false, allowDuplicates = false)
    {
        for (let valueToCheck of arrayToCheck) {
            if (!allowEmptyValues && !valueToCheck) {
                return true;
            } else if (forbiddenValues && forbiddenValues.includes(valueToCheck)) {
                return true;
            } else if (!allowDuplicates && this.countOccurrences(arrayToCheck, valueToCheck) > 1) {
                return true;
            }
        }
        return false;
    },

    /**
     * Check if the value is in an array of DOM elements
     *
     * @param value
     * @param elements
     * @param checkType
     * @returns {boolean}
     */
    isInElements: function (value, elements, checkType = true) {

        if (checkType) {
            for (let i = 0, size = elements.length; i < size; i++) {
                if (value === elements[i].value) {
                    return true;
                }
            }
        } else {
            for (let i = 0, size = elements.length; i < size; i++) {
                if (value == elements[i].value) {
                    return true;
                }
            }
        }
        return false;
    },

    /**
     * Refresh the value attribute
     *
     * @param evt
     */
    refreshValue: function (evt)
    {
        let elt = evt.target;
        elt.setAttribute('value', elt.value);
    },

    /**
     * Methods to create DOM elements
     */
    elementBuilder: {

        /**
         * Create a closing cross which erase the parent node on click
         *
         * @returns {HTMLElement}
         */
        createClosingCrossElt: function ()
        {
            let closingCrossElt = document.createElement('span');
            closingCrossElt.textContent = 'X';
            closingCrossElt.setAttribute('class', 'closing-cross');

            myApp.eraseTool.eraseElementOnClick(closingCrossElt);

            return closingCrossElt;
        }, 
        /**
         * Create a checkbox
         *
         * @returns {HTMLElement}
         */
        createCheckboxElt: function (name, value, checked = true)
        {
            let checkboxElt = document.createElement('input');
            checkboxElt.type = 'checkbox';
            checkboxElt.name = name;
            checkboxElt.value = value;
            checkboxElt.id = value;
            checkboxElt.checked = checked;

            return checkboxElt;
        },

        /**
         * Create a label
         *
         * @param {string} forAttribute
         * @param {string} text
         */
        createLabelElt: function (forAttribute, text)
        {
            let labelElt = document.createElement('label');
            labelElt.setAttribute('for', forAttribute);
            labelElt.textContent = text;

            return labelElt;
        },

        /**
         * Create an input DOM element
         *
         * @param type
         * @param name
         * @param value
         * @returns {HTMLElement}
         */
        createInputElt: function (type = 'text', name = '', value = '')
        {
            let inputElt = document.createElement('input');

            inputElt.type = type;
            if (name) {
                inputElt.name = name;
            }
            if (value) {
                inputElt.value = value;
            }

            return inputElt;
        }
    },

    /**
     * Some methods to erase stuff
     */
    eraseTool: {

        /**
         * Erase an element on click by emptying the parent node inner HTML
         *
         * @param element
         */
        eraseElementOnClick: function (element)
        {
            var that = this;
            element.addEventListener('click', that.eraseElementFromEvent);
        },

        /**
         * Erase an element determined by an event by emptying the parent node inner HTML
         *
         * @param evt
         */
        eraseElementFromEvent: function (evt)
        {
            myApp.eraseTool.eraseElement(evt.target);
        },

        /**
         * Erase an element by emptying the parent node inner HTML
         *
         * @param element
         */
        eraseElement: function (element)
        {
            element.parentNode.innerHTML = '';
        }
    },

    messageTool: {
        buildMessageElt: function (message)
        {
            let messageWrapperElt = document.createElement('div');
            let messageTextElt = document.createElement('p');
            let closingCrossElt = myApp.elementBuilder.createClosingCrossElt();

            messageTextElt.textContent = message;
            messageTextElt.classList.add('message');
            messageWrapperElt.classList.add('message-wrapper');

            messageWrapperElt.appendChild(closingCrossElt);
            messageWrapperElt.appendChild(messageTextElt);

            return messageWrapperElt;
        }
    }
}