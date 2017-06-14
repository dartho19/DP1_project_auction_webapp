/****************************************
 * Controller of the admin.html view    *
 ****************************************/


/************************
 * Functions
 */

/**
 * Functions that shows alerts, popups and other messages
 */

function showLogoutFailed() {
    console.log("[debug] logout failed, showing alert to user");
    alert("Errore durante il logout");
}

/************************
 * Callbacks definition
 */

//callback chiamata al ritorno della promise della chiamata ajax dell'adminForm
var placeBidCallback = function (responseText) {
    console.log("[debug] risultato ottenuto da bid.php: " + responseText);

    if(responseText == "BEST_BIDDER"){
        //l'utente è diventato il miglior offerente!
        getCurrentThr(); //aggiorna tempalte visualizzando la nuova thr
        getCurrentBid(); //aggiorna template visualizzando la nuova bid dato che è cambiata
        alert("Sei il miglior offerente!");

    } else if (responseText == "THR_UPDATE_OK") {
        //nuova thr impostata correttamente
        getCurrentThr(); //aggiorna tempalte visualizzando la nuova thr
        getCurrentBid(); //aggiorna template visualizzando la nuova bid dato che potrebbe essere cambiata

    } else if (responseText == "THR_UPDATE_KO" || responseText == "UNACCEPTED_INPUT") {
        alert("Impossibile impostare nuova offerta massima.\n\nNOTA: il valore impostato deve essere maggiore della massima offerta fin ora puntata");
    }
}


//callback chiamata quando si verifica l'evento onclick sul button logout
var logoutCallback = function () {

    $.ajax({
        url: 'src/php/auth.php',
        type: 'POST',
        data: "action=logout", //serializzo dati a mano
        success: function (responseText) {

            console.log(responseText);

            if (responseText == "LOGOUT_OK") {

                window.location.href = "index.html"; //effettua redirect a pagina di login

            } else showLogoutFailed();
        }
    });
}


/************************************************
 *     Here starts the Controller execution
 * 
 ************************************************/
$(document).ready(function () {

    /**********
     * Handler for "submit" event of the "adminForm"
     * 
     * send the form via AJAX 
     */
    $("#adminForm").submit(function (event) {

        event.preventDefault(); // stops the default action="/"

        var serializedForm = $('#adminForm').serialize() + "&action=placeNewThr"; //crea stringa formattata come url encoded (spedita nel body della POST)                

        console.log("[debug] sending the serialized data: " + serializedForm);

        $.ajax({
            url: 'src/php/bid.php',
            type: 'POST',
            data: serializedForm,
            success: placeBidCallback
        });

    });


})