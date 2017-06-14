<?php
//error reporting for DEBUG -> to be removed in production
error_reporting(E_ALL);
ini_set("display_errors", 1);

/************
 * Includes
 ***********/
//include 'session_manager.php'; //Fai partire la sessione / aggiornala
include 'utilities/db_utilities.php';

 // including the session file
include "session_manager.php";

/*********************
 *      Constants
 ********************/
define('UNACCEPTED_CREDENTIALS', -1);
define('ACCEPTED_CREDENTIALS', 1);


/*******************
 * Global variables
 *******************/
$loginResult = 0;
$registrationResult = 0;
$userCredentials = ACCEPTED_CREDENTIALS;

$email = "";
$password = "";
$action = "";

/*************************************************
*   Inizio esecuzione script
*/

/**
 * 1) Verifica se è stato richiesto logout, se si elimina sessione e ritorna conferma
 */
if( isset($_POST['action']) ){

    $action = $_POST['action'];

    if( $action == "logout" ){
        
        destroySession();
        echo "LOGOUT_OK";
        exit;
    }
}


/**
 * 2) E' stato richiesto login o registrazione, verifica ed effettua azione
 */
if( isset($_POST['email']) ){

    //check and sanitize email
    $email = $_POST['email'];

    $domain = explode("@", $email); //separo email in due str

    if(count($domain) == 2 ){ //[0] = giovanni.garifo, [1] = polito.it

        $domain = $domain[1];
    
        if(!filter_var($email, FILTER_VALIDATE_EMAIL) || !checkdnsrr($domain, 'MX') ){
             $userCredentials = UNACCEPTED_CREDENTIALS;
        }

    } else $userCredentials = UNACCEPTED_CREDENTIALS; //se email non è composta da str1@str2 di sicuro non è accettata
}

if( isset($_POST['password'])){
    $password = $_POST['password']; //to be sanitized before quering db
}

if( isset($_POST['action'])){

    //check if action is admitted
    $action = $_POST['action'];

    if($action != "signinButton" || $action != "registerButton"){
        $userCredentials == UNACCEPTED_CREDENTIALS;
    } 
}


//testo se email ed username risultano validi
if( $userCredentials == UNACCEPTED_CREDENTIALS ){

    destroySession();
    echo "UNACCEPTED_CREDENTIALS"; //ritorno al client questo valore
    exit; //blocco esecuzione script
}

/**
 * Input saniterizzato, posso eseguire il login
 * 
 * CONNESSIONE AL DB
 */
if( db_connect() == DB_ERROR ){
    
    destroySession();
    echo "DB_ERROR";
    exit; 

}else{

    if( $action == "signinButton"){
        
        //EFFETTUA LOGIN
        $loginResult = db_login_user($email, $password);
    
        if($loginResult == LOGIN_OK){

            //comunico avvenuto login ed AVVIO SESSIONE
            $_SESSION["email"] = $email; //salvo i dati sull'email dell'utente connesso
            echo "LOGIN_OK";

        } else {
            
            destroySession();
            echo "UNACCEPTED_CREDENTIALS";
        } 
    
    } else if($action == "registerButton"){

        //REGISTRA UTENTE
        $registrationResult = db_register_user($email, $password);
      
        if($registrationResult == REGISTRATION_OK){

            //comunico avvenuta registraione ed AVVIO SESSIONE
            $_SESSION["email"] = $email; //salvo i dati sull'email dell'utente connesso
            echo "REGISTRATION_OK";

        } else {
            
            destroySession();
            echo "UNACCEPTED_CREDENTIALS";
        } 
    }

}

//DISCONNESSIONE DAL DB
db_close();

//end
?>