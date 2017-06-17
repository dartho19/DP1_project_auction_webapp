/****************************************
 * Controller of the login.html view    *
 ****************************************/


/************************
 * Functions
 */

//controlla se la password contiene almeno un carattere numerico ed uno alfabetico
function checkPassword(password) {

    var valid = true;

    if (password.length < 2)
        valid = false;

    var r1 = new RegExp("[0-9]+", "i"); //matches a digit
    var r2 = new RegExp("[a-zA-Z]+", "i"); //matches a word

    if (!r1.test(password) || !r2.test(password))
        valid = false;

    return valid;
}


/**
 * Functions that shows alerts, popups and other messages
 */

function showInvalidPassword() {
    console.log("[debug] passoword not well formatted");
    alert("La password deve contenere almeno un carattere numerico ed uno alfabetico.");
}

function showLoginFailed() {

    console.log("[debug] login failed, showing alert to user");
    alert("Credenziali non valide.");
}



/************************
 * Callbacks definition
 */

//Callback chiamata al ritorno della promise della chiamata ajax del loginForm
var loginCallback = function (responseText) {

    console.log("[debug] Login attempt response: " + responseText);

    if (responseText == "UNACCEPTED_CREDENTIALS" || responseText == "DB_ERROR") {

        showLoginFailed(); //mostra errore nel login e attendi nuovo tentativo

    } else if (responseText == "LOGIN_OK" || responseText == "REGISTRATION_OK") {

        //mostra pannello amministrazione
        injectAdminTemplate(); //è stata aperta la sessione, apri le funzionalità
       
    }
}


/************************************************
 *     Here starts the Controller execution
 * 
 ************************************************/
$(document).ready(function () {

    /**
     * Registo event handlers solo dopo 0.5s per essere sicuro che il DOM sia caricato 
     */
    setTimeout(function () {

            console.log("[debug] login.js - start to attach handlers to events.")

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

                    //saving email into model
                    user.email = $("#email").val();

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
            console.log("[debug] login.js - all handlers have been attached.")
        },
        500
    );


})