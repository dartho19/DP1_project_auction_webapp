/*******************************
 * Utility Function Definition
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


/*******************************
 * Template Injectors
 */


//AUCTION-TEMPLATE
function injectAuctionTemplate() {

    $("#main-content").load("src/app/template/auction.html"); //carica il template
}

//LOGIN-TEMPALTE
function injectLoginTemplate() {
    $("#left-menu").load("src/app/template/login.html"); //carica il template
}

//ADMIN-TEMPLATE
function injectAdminTemplate() {

    console.log("[debug] user logged in. preparing to show admin panel.");
    $("#left-menu").load("src/app/template/admin.html"); //carica il template
}


/***********************************************
 *          Entry Point of the App
 * 
 ***********************************************/
$(document).ready(function () {

    //Check if cookies are enabled
    if (!navigator.cookieEnabled) {
        $("#container").load("src/app/template/nocookie.html"); //carica il template
    }

    //inject tempaltes and load data from backend
    injectAuctionTemplate(); //lo inserisce nella view
    injectLoginTemplate(); //carica il template del login nel left-menu
    disableEnterKey(); //return non invia il form

    loadAuctionModel(); //carica il modello dal backend (l'asta)

    //carica dal backend il valore impostato per bid ogni 3 secondi
    setInterval(function () {
        loadAuctionModel();

    }, 3000);

})