<style>
    .form-signin {
        width: 100%;
        max-width: 330px;
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

    .login_button {
        width: 50%;
        padding: 5px;
    }
</style>

<form class="form-signin" method="post" aaction="<?= base_url() ?>Index/login/verify" autocomplete="nope">
    <img class="mb-4" src="<?=base_url()?>resources/images/icon.png" alt="" width="170" height="150">
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" class="form-control login_email" placeholder="Email address" name="email"
           required autofocus>
    <br>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" class="form-control login_password" placeholder="Password" name="password"
           required>

    <button class="btn btn-sm btn-success btn-block login_button" type="submit"><i class="fa fa-sign-in"
                                                                                   aria-hidden="true"></i> Sign in
    </button>
    <br>
    <span>
            <?php if ($this->session->nav_url): ?>
                <a href="<?= base_url() ?>Index/clientregistration/add"><small>Register Client account</small></a> &nbsp;&nbsp;&nbsp;
            <?php else: ?>
                <a href="<?= base_url() ?>Index/businessregistration/add"><small>Register Vendor account</small></a> &nbsp;&nbsp;&nbsp;
            <?php endif; ?>

        <a href="<?= base_url() ?>Index/forgotpassword"><small>Forgot Password</small></a>
        </span>

    <br>
    <br>
    <?= ($error ? "<p class='alert alert-danger'>" . $error . "</p>" : null) ?>
    <p class="response_div"></p>
</form>

<script>
    $(".login_button").click(function (e) {
        e.preventDefault();

        var email = $(".login_email").val();
        var pwd = $(".login_password").val();
        var response_div = $(".response_div");

        $.ajax(
            {
                url: "<?=base_url()?>Index/AjaxLoginSimple/",
                method: 'POST',
                data: {"email": email, "password": pwd},
                success: function (response) {
                    if(response==0){
                        window.location.href=location.href;
                    }
                    response_div.html(response);
                }, error: function (err) {
                    response_div.html(err);
                }
            }
        );

    });
</script>