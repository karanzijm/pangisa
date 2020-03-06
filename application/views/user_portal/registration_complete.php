<style>
    .registration_complete {
        display: table;
        background: #ffffff;
    }

    .registration_complete_inner_div {
        height: 80vh;
        display: table-cell;
        vertical-align: center;
        text-align: center;
    }
</style>

<div class="container-fluid side_padding registration_complete">
    <div class="registration_complete_inner_div">
        Thank you for creating an account with Pangisa. We have sent you an activation link.
        Remember to click the activation link sent to your Email
        before <?php date_format(date_add(new DateTime(), date_interval_create_from_date_string('7 days')), "Y-m-d h:i:s") ?>
        else your registration shall be anullled.

        <br><br>

        All the best
    </div>
</div>
