<?php
    if( empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on'  ){
        
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST:URI']);
        exit();
    }    
?>

<!doctype html>
<html>

<!-- HEAD -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>pd1-web-project</title>
    <meta name="description" content="Auction web app" />
    <meta name="author" content="giovanni.garifo@polito.it" />
    <meta http-equiv="pragma" content="no-cache" />

    <!-- Immediatly redirect to https if user access via http
    <script type="text/javascript">
        if (window.location.protocol != 'https:') {
            location.href = location.href.replace("http://", "https://");
        }
    </script>-->

    <!-- Imports all styles -->
    <link href="resources/bootstrap.min.css" rel="stylesheet">
    <link href="src/css/custom.css" rel="stylesheet">

    <!--Imports of JQuery from CDN-->
    <script src="resources/jquery-3.2.1.js"></script>

    <!-- Imports all js scripts-->
    <!-- 1. carica modello -->
    <script src="src/app/model/auction.js"></script>
    <!-- 2. carica app -->
    <script src="src/app/app.js"></script>
</head>

<!-- BODY -->

<body class="container">

    <!-- header -->
    <div class="row page-header my-header">
        <h1 class="my-title">Auction Web App</h1>
        <small class="my-sub-title">Here you can place a bid to our current auction!</small>
    </div>

    <!-- center -->
    <div class="row">

        <!--left menu-->
        <div id="left-menu" class="col-xs-3 my-left-menu">
        </div>

        <!-- content -->
        <div id="main-content" class="col-xs-9 my-main-content">

        </div>

    </div>


    <!-- footer -->
    <div class="row my-footer">
        <h6 style="position: absolute;">This application was developed by
            <a href="https://bitbucket.org/dartho19/">Giovanni Garifo</a>. You can find source code on </h6>
        <a href="https://bitbucket.org/dartho19/pd1-web-project">
        <img src="img/bitbucket-logo.png" class="my-bitbucket-logo"/>
        </a>
    </div>

</body>

</html>