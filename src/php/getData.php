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

//inizializza la sessione, ma NON setta time()
session_start();

/*******************
 * Global variables
 *******************/
$thr;
$email;


/*************************************************
*   Inizio esecuzione script
*/

/**
 * ricavo parametri dalla sessione, se è iniziata
 */
if( isset($_SESSION['email']) ) 
    $email = $_SESSION['email']; //non è settata se richiesto bid senza che sia partita la sessione

/**
 * 1) Verifica se è stato richiesto il valore corrente di THR
 */
if( isset($_POST['action']) ){

    $action = $_POST['action'];

    if( $action == "getCurrentThr" ){
        
        db_connect();
        echo db_get_current_thr($email);
        db_close();
        exit;
    }
}

/**
 * 2) Verifica se è stato richiesto il valore corrente di BID
 */
if( isset($_POST['action']) ){

    $action = $_POST['action'];

    if( $action == "getCurrentBid" ){
        
        db_connect();
        echo db_get_current_bid();
        db_close();
        exit;
    }
}