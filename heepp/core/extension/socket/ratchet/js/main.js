$(document).ready(function() {
    var conn = new WebSocket('ws://localhost:8080');
    var chatForm = $(".chatForm"),
        messageInputField = chatForm.find("#message");
        messageList = $(".message-list");

    chatForm.on("submit", function(e) {
        e.preventDefault();
        var message = messageInputField.val();
        conn.send(message);
        messageList.prepend(`<li>${message}</li>`);
    })
    
    conn.onopen = function(e) {
        console.log("Connection established");
        //conn.send("message test from a browser client");
    };
    
    conn.onmessage = function(e) {
        console.log(e.data);
        messageList.prepend(`<li>${e.data}</li>`);
    }
});

