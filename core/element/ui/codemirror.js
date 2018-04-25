var editor = null;
function initCodeMirror() {
	editor = CodeMirror.fromTextArea(document.getElementById("codeMirror"), {
    	lineNumbers: true,
        viewportMargin: Infinity,
        indentUnit: 4,
    	mode: "application/x-httpd-php",
    	theme: "liquibyte"
  	});


}

$(document).ready(function() {
	initCodeMirror();
});