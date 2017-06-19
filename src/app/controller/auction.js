/****************************************
 * Controller of the auction.html view    *
 ****************************************/


/************************
 * Functions
 */

/*
*   Carica i dati dell'asta dal backend e li inietta nel template
*/
var loadAuctionModel = function (){

    //carica il modello dal backend
    $.ajax({
        url: 'src/php/getData.php',
        type: 'POST',
        data: "action=getCurrentBid", //serializzo dati a mano
        success: function (responseText) {

            console.log("[debug] valore della migliore offerta attuale e miglior offerente: " + responseText);

            var res = responseText.split("&"); //ricavo dati da stringa ritornata (bid&email)
            var bid = res[0];
            var email_best_bidder = res[1];

            if (!isNaN(bid)) {

                //esiste l'offerta, posso mostrare valore di bid
                
                //carico nel modlllo ed aggiorno la view
                auction.currentBid = bid;
                $("#currentBid").text(auction.currentBid + "€");

                //se esiste best bidder ne mostro il valore, se è l'utente connesso ad essere il best bidder glielo comunico
                if (email_best_bidder == "NULL" || email_best_bidder == "") {

                    $("#emailBestBidder").text("nessun offerente");

                } else {

                    //esiste offerente, posso mostrare la sua mail
                    auction.emailBestBidder = email_best_bidder;
                    $("#emailBestBidder").text(auction.emailBestBidder);

                }

            } else {
                $("#currentBid").text("nessuna offerta"); //offerta è ancora a NULL
                $("#currentEmailBestBidder").text("nessun offerente");
            }
        }
    });
}
