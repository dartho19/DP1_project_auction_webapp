/***********************************************
 *          Entry Point of the App
 * 
 ***********************************************/


/************************
 * Functions
 */


/************************
 * Callbacks definition
 */

//Callback chiamata al ritorno della promise della chiamata ajax del loginForm
var loginCallback = function (responseText) {

    console.log("[debug] response from php: " + responseText);

    if (responseText == "UNACCEPTED_CREDENTIALS" || responseText == "DB_ERROR") {

        showLoginFailed(); //mostra errore nel login e attendi nuovo tentativo

    } else if (responseText == "LOGIN_OK") {

        injectAdminTemplate(); //è stata aperta la sessione, apri le funzionalità

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
 *          Here's starts the App execution
 * 
 ************************************************/
$(document).ready(function () {

    loadAuction(); //carica il modello dal backend (l'asta)
    injectAuctionTemplate(); //lo inserisce nella view
    injectLoginTemplate(); //carica il template del login nel left-menu
    disableEnterKey();


    /**
     * Registo event handlers solo dopo 0.5s per essere sicuro che il DOM sia caricato 
     */
    setTimeout(function () {

            console.log("[debug] start to attach handlers to events.")

            /**********
             * Handler for "submit" event of the "loginForm"
             * 
             * send the form via AJAX 
             */
            $("#loginForm").submit(function (event) {

                event.preventDefault(); // stops the default action="/"

                //check if password is well formatted
                if (!checkPassword($("#password").val())) {
                    
                    showInvalidPassword();

                } else {
                    
                    var clickedButton = document.activeElement.id;
                    var serializedForm = $('#loginForm').serialize() + "&action=" + clickedButton; //crea stringa formattata come url encoded (spedita nel body della POST)                

                    console.log("[debug] sending the serialized data: " + serializedForm);

                    $.ajax({
                        url: 'src/php/auth.php',
                        type: 'POST',
                        data: serializedForm,
                        success: loginCallback
                    });
                }
            });


            //end of handler registration
            console.log("[debug] all handlers have been attached.")
        },
        500
    );


})