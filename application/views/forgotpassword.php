<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="<?=base_url()?>resources/images/icon.png">

    <title><?= $title ?></title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>resources/css/bootstrap/bootstrap.css" />
    <script type="text/javascript" src="<?=base_url()?>resources/js/jquery3_3_1.js"></script>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <!-- Custom styles for this template -->

    <link href="<?=base_url()?>" rel="canonical">
    <link href="<?=base_url()?>" rel="home">
    <link href="<?=base_url()?>" rel="alternate" hreflang="x-default">


    <meta name="author" content="akankwasa brian +256778693362">
    <meta name="description" content="Pangisa is an online market place that links equipment owners with potential hirers and clients.The platform that bridges the hire gap between the asset owner and the potential hirer. Find equipment closest to you that will fill your need hustle free. Rent that asset, vehicle, equipment at a click of a button! We having new and amazing products to choose from.">
    <meta name="keywords" content="akankwasa brian, Uganda, Items for sale, rent , rent , Pangisa, Pangisa Uganda , Pangisa (U) limited, items for hires, cars, rent a car,Pangisa,pagnisa,pagisa,rental,car rental,auto hires,vehicle,vehicles,vehicle hire,equipment equipment rental,equipment hire,uganda car hire,uganda equipment,hire company,uganda hire companies,emergency,vacation hires,vacation rentals,corporate rentals ,corporate hires,construction equipment ,earth moving equipment,">

    <meta property="og:title" content="Pangisa" />
    <meta property="og:url" content="<?=base_url()?>/Index/" />
    <meta property="og:description" content="Pangisa is an online market place that links equipment owners with potential hirers and clients.The platform that bridges the hire gap between the asset owner and the potential hirer. Find equipment closest to you that will fill your need hustle free. Rent that asset, vehicle, equipment at a click of a button! We having new and amazing products to choose from.">
    <meta property="og:image" content="<?=base_url()?>resources/images/icon.png">
    <meta property="og:type" content="article" />
    <meta property="og:locale" content="en_GB" />


</head>

<style>
    html,
    body {
        height: 100%; font-family: 'Montserrat', sans-serif;
        display: -ms-flexbox;
        display: -webkit-box;
        display: flex;
        -ms-flex-align: center;
        -ms-flex-pack: center;
        -webkit-box-align: center;
        align-items: center;
        -webkit-box-pack: center;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #fff;
    }

    .form-signin {
        width: 100%;
        max-width: 730px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-signin .checkbox {
        font-weight: 400;
    }
    .form-signin .form-control {
        position: relative;
        box-sizing: border-box;
        height: auto;
        padding: 10px;
        font-size: 16px;
    }
    .form-signin .form-control:focus {
        z-index: 2;
    }
    .form-signin input[type="email"] {
        margin-bottom: -1px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
    }
    .form-signin input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .login_button{width: 50%; padding:5px;}
</style>

<body class="text-centeru">
<form class="form-signin" method="post" action="<?=base_url()?>Index/forgotpassword/verify" autocomplete="off">
    <img class="mb-4" src="<?=base_url()?>resources/images/icon.png" alt="" width="170" height="150"><br><br>
    <label for="inputEmail" >Enter a valid email address to reset account. </label>
    <input type="email" id="inputEmail" class="form-control" placeholder="Email address" name="email" required autofocus autocomplete="random">
    <br>
    <label for="inputPassword" >Choose Mode of Reception</label>
    <select  id="inputPassword" class="form-control"  name="mode" required>
        <option selected="selected" value="email">Email</option>
        <option  value="sms">Phone Message</option>
    </select>

    <bR>
    <button class="btn btn-sm btn-danger btn-block login_button" type="submit"><i class="fa fa-sign-in" aria-hidden="true"></i> Reset</button>
    <br>
    <span>
            <?php if($this->session->nav_url): ?>
                <a href="<?=base_url()?>Index/clientregistration/add"><small>Register account</small></a> &nbsp;&nbsp;&nbsp;
            <?php else: ?>
                <a href="<?=base_url()?>Index/businessregistration/add"><small>Register account</small></a> &nbsp;&nbsp;&nbsp;
            <?php endif; ?>

            <a href="<?=base_url()?>Index/"><small>Home</small></a>
        </span>

    <br>
    <br>
    <?=($error?"<p class='alert alert-danger'>".$error."</p>":null)?>
    <small class="mt-5 mb-3 text-muted" style="position: relative; bottom: 0px;">Auto Mobils | Electronics | Mechanical   &copy;  2018</small>
</form>
</body>
</html>


<script>
    $(".login_button").click(function (e) {

        var user_res=window.confirm("Please confirm Password Reset?");

        if(user_res==true){

        }else{
            e.preventDefault();
        }
    });
</script>
