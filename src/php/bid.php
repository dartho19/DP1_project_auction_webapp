<?php
//error reporting for DEBUG -> to be removed in production
error_reporting(E_ALL);
ini_set("display_errors", 1);

/************
 * Includes
 ***********/
//verify if the script has been requested over https, if not do a self-redirect thour https
include 'checkSSL.php';

//include 'session_manager.php'; //Fai partire la sessione / aggiornala
include 'utilities/db_utilities.php';

// including the session functions
include "utilities/session_utilities.php";

/*********************
 *      Constants
 ********************/
define('UNACCEPTED_INPUT', -10);
define('UNACCEPTED_BID', -1);
define('ACCEPTED_BID', 1);


/*******************
 * Global variables
 *******************/
$bidResult = 0;
$inputData = "VALID"; //per verificare se input valido o meno

$thr;
$email;

/*********************
* Inizializza sessione
*/
startOrUpdateSession();
/*********************/

/*************************************************
*   Inizio esecuzione script
*/

/**
 * 3) E' stato richiesto inserimento nuovo THR o altra action da parte l'utente
 */

//controllo se thr impostato, ed è un valore numerico decimale consentito
if( isset($_POST['thr']) ){

    $thr = $_POST['thr'];

    if( preg_match("/^\d+(\.\d{1,2})?$/i", $thr) == 0 || $thr < 0) //ritorna 0 se non matcha la regexp
        $inputData = UNACCEPTED_INPUT;
}

//check sull'action richiesta
if( isset($_POST['action'])){

    //check if action is admitted
    $action = $_POST['action'];

    if($action != "placeNewThr"){ //testa se l'action è una di quelle previste
        $inputData == UNACCEPTED_INPUT;
    } 
}

//testo se thr ed action risultano entrambi validi
if( $inputData == UNACCEPTED_INPUT ){

    echo "UNACCEPTED_INPUT"; //ritorno al client questo valore
    exit; //blocco esecuzione script
}


//effettuo l'azione richiesta
if( db_connect() == DB_ERROR ){
    
    echo "DB_ERROR";

}else{

    if( $action == "placeNewThr" ){
        
        //imposto nuova threshold per l'utente
        $email = $_SESSION['email'];
        
        $bidResult = db_set_new_thr($email, $thr);

        if($bidResult == $email){
            //thr aggiornata ed utente è il miglior offerente!
            echo "BEST_BIDDER";

        } else if($bidResult == THR_UPDATE_OK){
            //thr aggiornata correttamente, ma l'utente non è risultato essere il miglior offerente
            echo "THR_UPDATE_OK";

        } else if ($bidResult == THR_UPDATE_KO){
            //thr non aggiornata, si è verificato un errore
            echo "THR_UPDATE_KO";
        }
    }
}

//DISCONNESSIONE DAL DB
db_close();



?>