/****************************************************
 *  Functions that loads templates into the DOM
 */

//load the auction into the view
function injectAuctionTemplate() {

    $("#main-content").load("src/app/template/auction.html"); //carica il template
}

//load Login template into left-menu
function injectLoginTemplate() {
    $("#left-menu").load("src/app/template/login.html"); //carica il template
}

//load Admin template into left-menu
function injectAdminTemplate() {
    
    console.log("[debug] user logged in. preparing to show admin panel.");
    $("#left-menu").load("src/app/template/admin.html"); //carica il template
}


/**********************************************************************
 * Functions that validates user input or change default behavior
 */

//non permettere invio di dati dai form premendo "enter"
function disableEnterKey() {

    $(window).keydown(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
}

//controlla se la password contiene almeno un carattere numerico ed uno alfabetico
function checkPassword(password) {

    var valid = true;

    if( password.length < 2)
        valid = false;

    var r1 = new RegExp("[0-9]+", "i"); //matches a digit
    var r2 = new RegExp("[a-zA-Z]+", "i"); //matches a word

    if( !r1.test(password) || !r2.test(password) )
        valid = false;

    return valid;
}




/**********************************************************
 * Functions that shows alerts, popups and other messages
 */

function showInvalidPassword(){
    console.log("[debug] passoword not well formatted");
    alert("La password deve contenere almeno un carattere numerico ed uno alfabetico.");
}

function showLoginFailed(){

    console.log("[debug] login failed, showing alert to user");
    alert("Credenziali non valide.");
}

function showLogoutFailed(){
    console.log("[debug] logout failed, showing alert to user");
    alert("Errore durante il logout");
}

