import $ from 'jquery';
class MyNotes {
    constructor(){
        this.events();
    }

    events(){
        $("#my-notes").on("click",".delete-note",this.deleteNote)
        $("#my-notes").on("click",".edit-note",this.editNote.bind(this))
        $("#my-notes").on("click",".update-note",this.updateNote.bind(this))
        $(".submit-note").on("click",this.createNote.bind(this))
    }

    createNote(){
        var ourNewPost ={
            'title' : $(".new-note-title").val(),
            'content' : $(".new-note-body").val(),
            'status': 'publish'
        }
        //to control what type of request we are sending
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/",
            type: 'POST',
            data: ourNewPost,
            success: (response) => {
                $(".new-note-title, .new-note-body").val('');
                $(`
                    <li data-id = "${response.id}"> <!-- if you are gettin the values from the database into the html attributes then we have to wrap that with esc_attr() -->
                    <input class="note-title-field" value="${response.title.raw}" readonly required>
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i>Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i>Delete</span>
                    <textarea readonly class="note-body-field">${response.content.raw}</textarea>
                    <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-rigth" aria-hidden="true"></i>Save</span>
                    </li>                
                `).prependTo("#my-notes").hide().slideDown();
                console.log("congrats");
                console.log(response);
            },
            error: (response) => {
                if(response.responseText == "You have reached your note limit") {
                    $(".note-limit-message").addClass("active");
                }
                console.log("sorry");
                console.log(response);
            }
        })
    }

    editNote(e){
        var thisNote = $(e.target).parents("li");
        if(thisNote.data("state") == "editable"){
            //make readonly
            this.makeNoteReadonlyLoad(thisNote);
        } else {
            //make editable
            this.makeNoteEditable(thisNote);
        }
    }

    makeNoteEditable(thisNote){
        thisNote.find(".edit-note").html('<i class="fa fa-times" aria-hidden="true"></i>Cancel')
        thisNote.find(".note-title-field, .note-body-field").removeAttr("readonly").addClass("note-active-field");
        thisNote.find(".update-note").addClass("update-note--visible");
        thisNote.data("state", "editable");
    }

    makeNoteReadonlyLoad(thisNote){
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i>Edit')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        location.reload();
        thisNote.data("state", "cancel");
    }

    makeNoteReadonly(thisNote){
        thisNote.find(".edit-note").html('<i class="fa fa-pencil" aria-hidden="true"></i>Edit')
        thisNote.find(".note-title-field, .note-body-field").attr("readonly", "readonly").removeClass("note-active-field");
        thisNote.find(".update-note").removeClass("update-note--visible");
        thisNote.data("state", "cancel");
    }

    updateNote(e){
        var thisNote = $(e.target).parents("li");
        //console.log(thisNote);
        var ourUpdatedPost ={
            'title' : thisNote.find(".note-title-field").val(),
            'content' : thisNote.find(".note-body-field").val()
        }
        //to control what type of request we are sending
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data('id'),
            type: 'POST',
            data: ourUpdatedPost,
            success: (response) => {
                this.makeNoteReadonly(thisNote);
                console.log("congrats");
                console.log(response);
            },
            error: (response) => {
                console.log("sorry");
                console.log(response);
            }
        })
    }

    //Methods will go here
    //NONCE: if we are successfully logged in the website then wp generates a nonce for you
    deleteNote(e){
        var thisNote = $(e.target).parents("li");
        //console.log(thisNote);
        //to control what type of request we are sending
        $.ajax({
            beforeSend: (xhr) => {
                xhr.setRequestHeader('X-WP-Nonce', universityData.nonce);
            },
            url: universityData.root_url + "/wp-json/wp/v2/note/" + thisNote.data('id'),
            type: 'DELETE',
            success: (response) => {
                thisNote.slideUp();
                console.log("congrats");
                console.log(response);
                if(response.userNoteCount < 5) {
                    $(".note-limit-message").removeClass("active");
                }
            },
            error: (response) => {
                console.log("sorry");
                console.log(response);
            }
        })
    }
}

export default MyNotes;