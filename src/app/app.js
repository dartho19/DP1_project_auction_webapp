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

//carica dal backend la thr corrente impostata dall'utente
var getCurrentThr = function () {

    $.ajax({
        url: 'src/php/bid.php',
        type: 'POST',
        data: "action=getCurrentThr", //serializzo dati a mano
        success: function (responseText) {

            console.log("[debug] THR dell'utente: " + responseText);

            if (!isNaN(responseText)) {

                //è un numero
                $("#currentThr").text(responseText + "€");

            } else $("#currentThr").text("nessuna");
        }
    });
}

var getCurrentBid = function () {

    $.ajax({
        url: 'src/php/bid.php',
        type: 'POST',
        data: "action=getCurrentBid", //serializzo dati a mano
        success: function (responseText) {

            console.log("[debug] valore della migliore offerta attuale e miglior offerente: " + responseText);

            var res = responseText.split("&"); //ricavo dati da stringa ritornata (bid&email)
            var bid = res[0];
            var email_best_bidder = res[1];

            if (!isNaN(bid)) {

                //esiste l'offerta, posso mostrare valore di bid
                $("#currentBid").text(bid + "€");

                //se esiste best bidder ne mostro il valore, se è l'utente connesso ad essere il best bidder glielo comunico
                if (email_best_bidder == "NULL") {
                    $("#emailBestBidder").text("nessun offerente");

                } else {
                    //esiste offerente, posso mostrare la sua mail
                    $("#emailBestBidder").text(email_best_bidder);
                }

            } else {
                $("#currentBid").text("nessuna offerta"); //offerta è ancora a NULL
                $("#currentEmailBestBidder").text("nessun offerente");
            }
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
    getCurrentThr();
}


/***********************************************
 *          Entry Point of the App
 * 
 ***********************************************/
$(document).ready(function () {

    loadAuction(); //carica il modello dal backend (l'asta)
    injectAuctionTemplate(); //lo inserisce nella view
    injectLoginTemplate(); //carica il template del login nel left-menu
    disableEnterKey(); //return non invia il form
    getCurrentBid(); //carica dal backend il valore impostato per bid
})