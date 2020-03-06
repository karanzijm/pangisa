
<div class="render_page side_padding ">
    <div class="register-client-account">
        <section>
            <h3>Create Client Account</h3><br>
        </section>
        <form action='<?= base_url() ?>Index/clientregistration/save' method='post' enctype='multipart/form-data'>
            <div class='container-fluid' style='mmin-width: 70%' c,ellspacing='0px'>
                <div class='row'>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=name>First Name <?php echo form_error('first_name'); ?></label><input class='form-control' type='text' name='first_name'
                                                                    value="<?php echo set_value('first_name'); ?>"/>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=name>Last Name <?php echo form_error('last_name'); ?></label><input class='form-control' type='text' name='last_name'
                                                                       value="<?php echo set_value('last_name'); ?>" required/>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=name>Gender</label>
                            <select class="form-control">
                                <option value=""></option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=phone>Phone Number</label><input class='form-control phone_number' type='number'
                                                                        minlength="10" maxlength="12"
                                                                        name='phone' required/>
                            <small></small>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=location>Location </label><input class='form-control' type='text' name='location'
                                                                        required/>
                            <small></small>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=email>Email</label><input class='form-control' type='email' name='email'
                                                                 required/>
                            <small></small>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=admin_password>Password</label><input class='form-control admin_password'
                                                                             type='password'
                                                                             name='admin_password' required/>
                            <small></small>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=confirm_password>Confirm Password</label><input class='form-control'
                                                                                       type='password'
                                                                                       name='confirm_password'
                                                                                       required/>
                            <small></small>
                        </div>
                    </div>
                </div>

            </div>
            <br><Br> <b>
                <button type='submit' value='Register Account' class='btn btn-info '>Register Account</button>
            </b>
        </form>
    </div>
</div>


<script>
    $(".phone_number").keyup(function () {
        var phone = $(this).val();
        var phone_length = phone.length;
        console.log(phone);

        if (!phone.startsWith("256")) {
            $(this).parent().children("small").html("The Phone number must start with 256");
        }

        if (phone_length != 12) {
            $(this).parent().children("small").html("The Phone number should be 12 characters long, in the format 2567****");
        } else {
            $(this).parent().children("small").hide();
        }
    });

    $(".admin_password").keyup(function () {
        var pwd = $(this).val();

        if (pwd.length < 6) {
            $(this).parent().children("small").html("Password Must be 6 characters and above");
        } else {
            $(this).parent().children("small").hide();
        }


    });

</script>
