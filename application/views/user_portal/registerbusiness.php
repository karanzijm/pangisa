<div class="side_padding ">
    <div class="register-client-account">
        <section>
            <h3>Create Vendor Account</h3><br>
        </section>

        <form action='<?= base_url() ?>Accounts/businessregistration/save' method='post' enctype='multipart/form-data'>
            <div class='container-fluid'>
                <div class='row'>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=name>Individual or Company Name * </label>
                            <input class='form-control' type='text' name='name' required/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Individual or Company
                                Name
                            </small>
                        </div>
                    </div>

                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=location>Business Location * </label><input class='form-control' type='text'
                                                                                   name='location' required/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Business Location
                            </small>
                        </div>
                    </div>

                </div>

                <div class='row'>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=phone>Phone Number * </label>
                            <input class='form-control phone_number' type='number' name='phone' required
                                   placeholder="Format 2567---------"/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Phone Number</small>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=alternative_phone>Alternative Telephone</label><input class='form-control'
                                                                                             type='number'
                                                                                             name='alternative_phone'/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Alternative Telephone
                            </small>
                        </div>
                    </div>
                </div>

                <div class='row'>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=email>Email *</label><input class='form-control' type='email' name='email'
                                                                   required/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Email</small>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=id_photo>Company Certificate Or National Id(for individuals) *</label><input
                                    class='form-control-file' type='file' name='id_photo' required
                                    accept='image/png, image/jpeg,image/jpg,*/pdf/*doc,*/dicx'/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Company Certificate Or
                                National
                                Id(for individuals)
                            </small>
                        </div>
                    </div>
                </div>
                <div class='row'>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=admin_password>Password</label>
                            <input class='form-control admin_password' type='password' name='admin_password' required/>
                            <small id="emailHelp" class="form-text text-muted">Please enter Your Password, password be 6
                                characters and above
                            </small>
                        </div>
                    </div>
                    <div class='col-sm-6'>
                        <div class="form-group build_form_form_group">
                            <label for=confirm_password>Confirm Password</label><input class='form-control'
                                                                                       type='password'
                                                                                       name='confirm_password'
                                                                                       required/>
                            <small id="emailHelp" class="form-text text-muted">Please enter the Confirm Password</small>
                        </div>
                    </div>
                </div>
                <div class='row'></div>
            </div>
            <br><Br> <input type='submit' value='SUBMIT FORM' class='btn btn-info'/>
        </form>
    </div>
</div>

<script>
    $(".phone_number").mouseout(function () {
        var phone = $(this).val();
        if (!phone.startsWith("256")) {
            $(this).parent().children("small").html("The Phone number must start with 256");
        }

        if (phone.length != 12) {
            $(this).parent().children("small").html("The Phone number should be 12 characters long, in the format 2567****");
        } else {
            $(this).parent().children("small").hide();
        }


    });

    $(".admin_password").mouseout(function () {
        var pwd = $(this).val();

        if (pwd.length < 6) {
            $(this).parent().children("small").html("Password Must be 6 characters and above");
        } else {
            $(this).parent().children("small").hide();
        }


    });
</script>
