var script = document.createElement('script');
script.src = 'https://code.jquery.com/jquery-3.6.3.min.js';
document.getElementsByTagName('head')[0].appendChild(script);

function removeMissage(idMessage){
    idMessage.classList.add("remove")
    setTimeout(() => {
        idMessage.remove();
    }, 400);
}

var errorMissageId = 0;
function displayMessage(missageContent, elementFather,codeMessage = 3){
    // CodeMessage: 0 => success, 1 => info,2 => warning,3 => error,
    switch (codeMessage) {
        case 0:
            elementFather.append(
                "<div id='displayMessage" + errorMissageId + "' class='displayMessage success'> \
                    <i class='fa fa-check-circle'></i> \
                    " + missageContent + "\
                    <button onclick='removeMissage(displayMessage"+ errorMissageId + ")' class='closeMessageBtn'> <i class='fa fa-close'></i> </button>\
                </div>"
            );
            break;
        case 1:
            elementFather.append(
                "<div id='displayMessage" + errorMissageId + "' class='displayMessage info'> \
                    <i class='fa fa-info'></i> \
                    " + missageContent + "\
                    <button onclick='removeMissage(displayMessage"+ errorMissageId + ")' class='closeMessageBtn'> <i class='fa fa-close'></i> </button>\
                </div>"
            );
            break;
        case 2:

            elementFather.append(
                "<div id='displayMessage" + errorMissageId + "' class='displayMessage warning'> \
                    <i class='fa fa-warning'></i> \
                    " + missageContent + "\
                    <button onclick='removeMissage(displayMessage"+ errorMissageId + ")' class='closeMessageBtn'> <i class='fa fa-close'></i> </button>\
                </div>"
            );
            break;
        case 3:
            elementFather.append(
                "<div id='displayMessage" + errorMissageId + "' class='displayMessage error'> \
                    <i class='fa fa-exclamation-circle'></i> \
                    " + missageContent + "\
                    <button onclick='removeMissage(displayMessage"+ errorMissageId + ")' class='closeMessageBtn'> <i class='fa fa-close'></i> </button>\
                </div>"
            );
            break;
        default:
            elementFather.append(
                "<div id='displayMessage" + errorMissageId + "' class='displayMessage error'> \
                    <i class='fa fa-exclamation-circle'></i> \
                    " + missageContent + "\
                    <button onclick='removeMissage(displayMessage"+ errorMissageId + ")' class='closeMessageBtn'> <i class='fa fa-close'></i> </button>\
                </div>"
            );
            break;
    }

    errorMissageId = errorMissageId + 1;


    //Create elements
    function createInputOnlyRead(id, text, className, parentID, group = ''){
        var newInput = $('<input>');
        newInput.attr("type", "text");
        newInput.attr("id", id);
        newInput.val(text);
        newInput.addClass(className);
        newInput.attr('name', group);
        newInput.attr('readonly', true);
        $("#"+parentID+"").append(newInput);
    }

}