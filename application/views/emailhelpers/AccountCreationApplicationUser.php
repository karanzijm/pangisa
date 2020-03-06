<style>

</style>

<div style="padding:0px; margin:0px; font-family: Calibri; background: #fff;">
    <section style="background: #fff;">
        <img src="<?= base_url() ?>resources/images/icon.png" width="30%"/>
    </section>

    <section style="padding:20px; background: #fff;">
        Dear <?= $userObject->name ?> <br>

        <p>Thank you for choosing to work with Pangisa.</p>

        <p>Before your account is activated, we shall require your approval within a period of seven days beyond which it will be terminated.</p>

        <p>Approval of the account will require a valid email and/or phone number, which shall be registered as the primary communication channel with the platform</p>

        <p>Please follow the link below to approve account.
            <a href='<?=base_url()?>Index/confirmaccount/<?= $hashedActivationKey ?>'><button >Approve Account</button></a>
            or paste the follwing linke in your browser<br><br>
            <span style="padding:10px 40px;  color: #dc3545;"><?=base_url()?>Index/confirmaccount/<?= $hashedActivationKey ?></span>
        </p>
    </section>

</div>
