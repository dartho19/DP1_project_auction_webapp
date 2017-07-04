<?php
/**
* libreria per gestire interazione con DB MySQL via mysqli API 
*/

/****************
 *   Constants
 ****************/

//db info
DEFINE ('DB_USER', 'username');
DEFINE ('DB_PASSWORD', 'password');
DEFINE ('DB_HOST', 'localhost');
DEFINE ('DB_NAME', 'auction-db');

//db errors
DEFINE ('DB_ERROR', -1);
DEFINE ('DB_CONNECTED', 1);

//query return values
DEFINE ('LOGIN_OK', 10);
DEFINE ('LOGIN_KO', -10);
DEFINE ('REGISTRATION_OK', 11);
DEFINE ('REGISTRATION_KO', -11);
DEFINE ('THR_UPDATE_OK', 12);
DEFINE ('THR_UPDATE_KO', -12);


/*******************
 * Global variables
 *******************/
$conn; //rappresenta la connessione al DB


/********************************************************
 *          Funzioni per collegamento al db
 ********************************************************/

/**
 * Effettua connessione al database
 */
function db_connect(){
    
    global $conn; //forzo utilizzo variabile fuori dallo scope
    $DB_STATUS;

    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if(!$conn){

        //impossibile connettersi al database
        $DB_STATUS = DB_ERROR;
        
    }else {
        //connessione avvenuta
        $DB_STATUS = DB_CONNECTED;
    }

    return $DB_STATUS;
}


/**
 * Effettua chiusura connessione
 */
function db_close(){
    global $conn; //forzo utilizzo variabile fuori dallo scope
    mysqli_close($conn);
}


/********************************************************
 *          Funzioni per accesso al database
 ********************************************************/

/**
 * Verifica che l'utente sia iscritto e può quindi completare il login
 */
function db_login_user($email, $password){

    global $conn; //forzo utilizzo variabile fuori dallo scope

    //sanitize user credentials
    $san_email = mysqli_real_escape_string($conn, $email);
    $san_password = mysqli_real_escape_string($conn, $password);
    
    try {

        //estrai hashed password dal db
        $query = "SELECT password FROM users WHERE email = '".$san_email."';";

        $res = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
        $hashedPassword = $row['password'];

        //confronta hashed password presente nel db con quella inserita dall'utente
        if( password_verify($password, $hashedPassword) == TRUE){
        
            //user esiste, può essere autenticato
            mysqli_free_result($res);
            return LOGIN_OK;

        } else {

            //user non registrato / credenziali errate
            mysqli_free_result($res);
            return LOGIN_KO;
        }

    } catch (Exception $e){
        
        mysqli_free_result($res);
        return LOGIN_KO;
    }
}

/**
 * Registra nuovo utente 
 */
function db_register_user($email, $password){

    global $conn; //forzo utilizzo variabile fuori dallo scope

    //sanitize user credentials
    $san_email = mysqli_real_escape_string($conn, $email);
    $san_password = mysqli_real_escape_string($conn, $password);

    //now create hashed password with random salt (using the "$2y$" crypt format, which is always 60 characters wide.)
    $hashedPassword = password_hash($san_password, PASSWORD_BCRYPT);
    
    try {

        //insert user credential into database
        $query = "INSERT INTO users VALUES('".$san_email."','".$hashedPassword."', NULL, NULL)";

        $res = mysqli_query($conn, $query);

        if($res == TRUE){

            //user esiste, può essere autenticato
            return REGISTRATION_OK;

        } else {

            //user non registrato / credenziali errate
            return REGISTRATION_KO;
        }

    } catch(Exception $e) {

        return REGISTRATION_KO;
    }
}

/**
 * Ritorna THR corrente per l'user passato
 */
function db_get_current_thr($email){
    
    global $conn; //forzo utilizzo variabile fuori dallo 
    
    //sanitize user credentials
    $san_email = mysqli_real_escape_string($conn, $email);

    try {   

        $query = "SELECT thr FROM users WHERE email='".$san_email."'";

        $res = mysqli_query($conn, $query);

        if(!$res) 
            throw new Exception("impossibile ricavare thr dell'utente".$san_email);

        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
    
        $thr = $row['thr'];

        mysqli_free_result($res);
        return $thr;

    } catch( Exception $e ) {
        
        mysqli_free_result($res);
        return "NULL";
    }
}

/**
 * Ritorna BID corrente
 */
function db_get_current_bid(){
    
    global $conn; //forzo utilizzo variabile fuori dallo scope

    try {   

        $query = "SELECT bid, email_best_bidder FROM auction";

        $res = mysqli_query($conn, $query);

        if(!$res) 
            throw new Exception("impossibile ricavare bid da tabella auction");

        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
    
        $bid = $row['bid'];
        $email_best_bidder = $row['email_best_bidder'];

        mysqli_free_result($res);
        
        return $bid."&".$email_best_bidder;

    } catch( Exception $e ) {
        
        mysqli_free_result($res);
        return "NULL";
    }
}

/**
 * Imposta nuova THR per lo user passato, se maggiore di BID, imposta nuovo miglior offerente
 */
function db_set_new_thr($email, $newThr){
    
    global $conn; //forzo utilizzo variabile fuori dallo scope
    $ret = ""; //contiene valore che verrà ritornato
    $res = ""; //contiene valore di ritorno delle query

    //sanitize user credentials
    $san_email = mysqli_real_escape_string($conn, $email);
    $san_newThr = mysqli_real_escape_string($conn, $newThr);

    //INIZIO TRANSAZIONE
    try {   
        mysqli_autocommit($conn, false); //effettuerò commit manualmente alla fine della transazione

        $res = mysqli_query($conn, "SELECT bid FROM auction");
        
        if(!$res) 
            throw new Exception("impossibile ricavare bid da tabella auction");

        $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
        $bid = $row['bid']; //ricava valore attuale di bid

        if($newThr > $bid){
            
             /**
             * Utente ha effettuato offerta valida: Aggiorna THR dell'utente e last_bid_time con il timestamp
             */
            $res = mysqli_query($conn, "UPDATE users SET thr = '".$san_newThr."', last_bid_time = CURRENT_TIMESTAMP  WHERE email = '".$san_email."' ");
           
            if(!$res) 
                throw new Exception("impossibile impostare nuovo threshold per l'utente");

            /**
             * Aggiorna BID e best bidder, dato che la nuova thr impostata è maggiore di bid verifico se l'offerta del'utente è la migliore
             */

            $email_best_bidder = update_best_bidder(); //chiamo funzione che contiene la logica di aggiornamento della BID
            
            if($email_best_bidder == $san_email){
                //l'utente che ha effettuato l'offerta è il nuovo miglior offerente
                $ret = $email_best_bidder;
            } else {
                //l'offerta appena inserita non è abbastanza alta, è stata superata
                $ret = THR_UPDATE_OK;
            }

        } else {
            
            //utente ha tentato di impostare valore di thr minore di bid
            $ret = THR_UPDATE_KO;
        }

    } catch( Exception $e ) {
        //rollback in caso di fail di uno degli statement SQL
        mysqli_rollback($conn);
        $ret = THR_UPDATE_KO;
        return $ret;
    }
    
    //effettuo commit manuale
    mysqli_commit($conn);

    //FINE TRANSAZIONE

    return $ret;
}


/*****************************************************************************************
 *          Funzioni utilizzate per operazioni non espressamente richieste lato client
 *****************************************************************************************/

/**
 * Funzione chiamata all'interno di db_set_new_thr() per aggiornare la BID
 * 
 * quando viene chiamata è ancora aperta la transazione di aggiornamento della thr dell'utente, eventuali eccezioni sono gestite
 * dalla funzione a monte.
 */
function update_best_bidder(){

    global $conn;
    $res = ""; //contiene valore di ritorno delle query
    $row = "";
    $bestBid = "";


    $query = "SELECT * 
                FROM users 
                WHERE thr=(SELECT MAX(thr) 
                            FROM users) 
                AND last_bid_time = (SELECT MIN(last_bid_time) 
                                     FROM users 
                                     WHERE thr=(SELECT MAX(thr)
                                                FROM users)
                                    )";

    $res = mysqli_query($conn, $query);
           
    if(!$res) 
        throw new Exception("impossibile ricavare informazioni su best bidder"); //catched by external function

    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);
    
    $bestThr = $row['thr']; //ricava thr più alta
    $email_best_bidder = $row['email']; //ricava email dell'offerente con soglia più alta

    /*
    *calcola nuova bestBid, pari alla più alta thr impostata da un utente diverso dal best_bidder + 0.01
    */
    $res = mysqli_query($conn, "SELECT MAX(thr) AS thr FROM users WHERE email != '".$email_best_bidder."' ");            
    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);

    //calcola valore di bestBid
    if($row['thr'] == $bestThr){
        
        //ci sono due o più utenti con stessa thr, la nuova bid è pari alla thr, il miglior offerente è quello che ha offerto prima
        $bestBid = $bestThr;
    
    } else if($row['thr'] == NULL){
       
        //non c'è nessun utente che ha inserito altre offerte, la bid rimane invariata. L'utente ha solamente aggiornato la sua thr ed è rimasto il miglior offerente
        $res = mysqli_query($conn, "UPDATE auction SET email_best_bidder = '".$email_best_bidder."' "); //aggiorno l'email del best bidder

        if(!$res) 
            throw new Exception("impossibile effettuare update_bid"); //catched by external function

        return $email_best_bidder;

    } else {

         $bestBid = $row['thr'] + 0.01; //aggiorno bestBid 
    }

    //aggiorno tabella auction con il nuovo valore di bid
    $res = mysqli_query($conn, "UPDATE auction SET bid = '".$bestBid."', email_best_bidder = '".$email_best_bidder."' "); //aggiorno tabella auction
        
    if(!$res) 
        throw new Exception("impossibile effettuare update_bid"); //catched by external function

    return $email_best_bidder; //ritorno email del miglior offerente per comunicare all'utente se è lui o no
     
}

//end
?>
