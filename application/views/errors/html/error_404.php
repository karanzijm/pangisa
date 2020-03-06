<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<head>
    <title>Pangisa - 404 Page not found</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css"
          href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"/>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

    <meta name="author" content="akankwasa brian +256778693362">
    <meta name="description" content="Make extra income by renting out an item to the public.">
    <meta name="keywords"
          content="akankwasa brian, Uganda, Items for sale, rent , rent , Pangisa, Pangisa Uganda , Pangisa (U) limited, items for hires, cars, rent a car">

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Pangisa</title>

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,900" rel="stylesheet">


    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>


<style>
    * {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    body {
        padding: 0;
        margin: 0;
    }

    #notfound {
        position: relative;
        height: 100vh;
    }

    #notfound .notfound {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound {
        max-width: 410px;
        width: 100%;
        text-align: center;
    }

    .notfound .notfound-404 {
        height: 120px;
        position: relative;
        z-index: -1;
    }

    .notfound .notfound-404 h1 {
        font-family: 'Montserrat', sans-serif;
        font-size: 230px;
        margin: 0px;
        font-weight: 900;
        position: absolute;
        left: 50%;
        color: #222222;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
        /*background: url('../img/bg.jpg') no-repeat;*/
        /*-webkit-background-clip: text;*/
        /*-webkit-text-fill-color: transparent;*/
        background-size: cover;
        background-position: center;
    }

    .notfound h2 {
        font-family: 'Montserrat', sans-serif;
        color: #000;
        font-size: 24px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 0;
    }

    .notfound p {
        font-family: 'Montserrat', sans-serif;
        color: #000;
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 20px;
        margin-top: 0px;
    }

    .notfound a {
        font-family: 'Montserrat', sans-serif;
        font-size: 11px !important;
        text-decoration: none;
        text-transform: uppercase;
        background: #0046d5;
        display: inline-block;
        padding: 10px 20px;
        border-radius: 40px;
        color: #fff;
        font-weight: 700;
        -webkit-box-shadow: 0px 4px 1px -2px #ccc;
        box-shadow: 0px 4px 1px -2px #ccc;
    }

    @media only screen and (max-width: 767px) {
        .notfound .notfound-404 {
            height: 142px;
        }

        .notfound .notfound-404 h1 {
            font-size: 112px;
        }
    }
</style>
<body>
<div id="notfound">
    <div class="notfound">
        <h2>Oops! Page Not Be Found</h2>
        <p>Sorry but the page you are looking for does not exist, have been removed. name changed or is temporarily
            unavailable</p>
        <a href="/Index/" class="btn btn-sm btn-secondary">Home</a>
        <a href="www.pangisa.co.ug/Index/login" class="btn btn-sm btn-secondary">Login</a>
        <a href="www.pangisa.co.ug" class="btn btn-sm btn-secondary">Latest Items</a>
    </div>
</div>

</body>
</html>