$('p').linkify();
$('#sidebar').linkify({
    target: "_blank"
});
function getSelectionText() {
    var text = "";
    if (window.getSelection) {
        text = window.getSelection().toString();
    } else if (document.selection && document.selection.type != "Control") {
        text = document.selection.createRange().text;
    }
    return text;
}

function insert(text)
{  
    
    //Stolen from Wakaba and 4chan
	var textarea=document.forms[0].postText;
    var selc=getSelectionText();
    if (selc!=""){
        text+="\n>";
        text+=selc.replace(/[\r\n]+/g, "\n>");
        text+="\n";
    }
	if(textarea)
	{
		if(textarea.createTextRange && textarea.caretPos) // IE
		{
			var caretPos=textarea.caretPos;
			caretPos.text=caretPos.text.charAt(caretPos.text.length-1)==" "?text+" ":text;
		}
		else if(textarea.setSelectionRange) // Firefox
		{
			var start=textarea.selectionStart;
			var end=textarea.selectionEnd;
			textarea.value=textarea.value.substr(0,start)+text+textarea.value.substr(end);
			textarea.setSelectionRange(start+text.length,start+text.length);
		}
		else
		{
			textarea.value+=text+" ";
		}
		textarea.focus();
	} 
}


function toggleGlowInTheDark(){
    if(localStorage.dark=="true"){
        localStorage.dark="false";
    }else{
        localStorage.dark="true";
    }
    location.reload();
}

window.addEventListener('load', addToggle());
