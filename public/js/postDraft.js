var myPostDraft = {

    idOfPostId: 'edit-post',
    idOfPostTitle: 'post-title',
    idOfPostExcerpt: 'post-excerpt',
    idOfPostContent: 'post-content',

    save: function () {
        localStorage.setItem('myPostDraft-id', myPostDraft.getValue(myPostDraft.idOfPostId));
        localStorage.setItem('myPostDraft-title', myPostDraft.getValue(myPostDraft.idOfPostTitle));
        localStorage.setItem('myPostDraft-excerpt', myPostDraft.getValue(myPostDraft.idOfPostExcerpt));
        localStorage.setItem('myPostDraft-content', myPostDraft.getValue(myPostDraft.idOfPostContent));
    },

    load: function (id = null) {
        if (
            parseInt(id, 10) === parseInt(localStorage.getItem('myPostDraft-id'), 10) ||
            id === -1 && localStorage.getItem('myPostDraft-id') === ''
        ) {
            myPostDraft.setValue(myPostDraft.idOfPostTitle, localStorage.getItem('myPostDraft-title'));
            myPostDraft.setValue(myPostDraft.idOfPostExcerpt, localStorage.getItem('myPostDraft-excerpt'));
            myPostDraft.setValue(myPostDraft.idOfPostContent, localStorage.getItem('myPostDraft-content'));
        }
    },

    getValue: function (fieldId) {
        if (document.getElementById(fieldId)) {
            return document.getElementById(fieldId).value;
        }
        return '';
    },

    setValue: function (fieldId, value) {
        var elt = document.getElementById(fieldId);
        if (elt) {
            elt.value = value;
            elt.textContent = value;
        }
    }
};