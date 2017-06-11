<?php
/**
* libreria per gestire la Sessione Utente
*/

/****************
 *   Constants
 ****************/
DEFINE('MAX_INACTIVITY_TIME','120'); //tempo massimo di inattività consentito in secondi



/***********************
 *      Funzioni
 ***********************/

//distrugge forzatamente la sessione
function destroySession(){

        session_start(); //resumes the current session

        $_SESSION=array(); //pulisco array 
    
        //distruggi il cookie associato alla sessione
        if (ini_get("session.use_cookies")) { // the cookie is used to expose the identifier of the session to the remote browse
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600*24, $params["path"], /*trick: imposto tempo a valore negativo per uccidere il cookie*/
                    $params["domain"], $params["secure"], $params["httponly"]);
        }

        //distruggi la sessione
        session_destroy();

        // redirect client to login/default page
        //header('HTTP/1.1 307 temporary redirect');
        //header('Location: ../../index.html');
        //exit; // IMPORTANT to avoid further output from the script
}


//Avvia la sessione o verifica se è scaduta, se scaduta redirect a pagina di login
function startOrTestSession(){
    
    session_start(); //creates a session or resumes the current one (using the id in the http request) 
    
    $t=time(); $diff=0; $new=false;

    if (isset($_SESSION['time'])){
    
        $t0=$_SESSION['time']; $diff=($t-$t0); // inactivity

    } else {
        $new=true;
    }

    if( $new ){
        //primo collegamento, apro sessione

        $_SESSION['time']=time(); /* imposto tempo */

    }else if ($diff > MAX_INACTIVITY_TIME) { 
        //passato troppo tempo, sessione va distrutta
        
        destroySession();

    } else {

        //utente si è ricollegato in tempo
        $_SESSION['time']=time(); /* aggiorno time */
    }
}


//end
?>