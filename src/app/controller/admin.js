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


var checkInactivity = function () {

    $.ajax({
        url: 'src/php/checkSession.php',
        type: 'POST',
        data: "action=checkInactivity", //serializzo dati a mano
        success: function (responseText) {

            console.log("[debug] session.php response: " + responseText + "\n");

            if (responseText == "SESSION_ENDED") {

                alert("Sei rimasto inattivo per troppo tempo.\n\nEffettua nuovamente il Login.")
                window.location.replace("index.html"); //effettua redirect a pagina di login

            }
        }
    });
}

/************************
 * Callbacks definition
 */

//callback chiamata al ritorno della promise della chiamata ajax dell'adminForm
var placeBidCallback = function (responseText) {
    console.log("[debug] risultato ottenuto da bid.php: " + responseText);

    if( responseText == "SESSION_ENDED"){
        //redirect to homepage
        window.location.replace("index.html");
    }

    if (responseText == "BEST_BIDDER") {
        //l'utente è diventato il miglior offerente!
        getCurrentThr(); //aggiorna template visualizzando la nuova thr
        loadAuctionModel(); //aggiorna modello visualizzando la nuova bid dato che è cambiata

        $("#response").text("Sei il miglior offerente!");
        $("#response").addClass("my-response-text-ok");

    } else if (responseText == "THR_UPDATE_OK") {
        //nuova thr impostata correttamente, ma l'utente non è il miglior offerente
        getCurrentThr(); //aggiorna tempalte visualizzando la nuova thr
        loadAuctionModel(); //aggiorna template visualizzando la nuova bid dato che potrebbe essere cambiata

        $("#response").text("Non sei il miglior offerente.");
        $("#response").addClass("my-response-text-ko");

    } else if (responseText == "THR_UPDATE_KO" || responseText == "UNACCEPTED_INPUT") {
        alert("Impossibile impostare nuova offerta.\n\nNOTA: il valore impostato deve essere maggiore della massima offerta fin ora puntata");
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

            if (responseText == "SESSION_ENDED") {

                window.location.replace("index.html"); //effettua redirect a pagina di login

            } else showLogoutFailed();
        }
    });
}


/************************************************
 *     Here starts the Controller execution
 * 
 ************************************************/
$(document).ready(function () {


    //verifica se l'utente che si è appena collegato è il best bidder, notificaglielo
    if (user.email == auction.emailBestBidder) {

        $("#response").text("Sei il miglior offerente!");
        $("#response").addClass("my-response-text-ok");

    } else {
        $("#response").text("Non sei il miglior offerente.");
        $("#response").addClass("my-response-text-ko");
    }


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


    /*
    * Effettua controllo sull'inattività ogni secondo
    */
    setInterval(function () {
        
        checkInactivity();

    }, 1000);


})