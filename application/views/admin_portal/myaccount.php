<div class="edit_account_table">

    <h2>Edit Account Details</h2>

    <?= ($data['error']?"<p class='alert alert-danger'>".$data['error']."</p>":null) ?>
    <form method="post" action="<?=base_url()?>/AppClient/account/update">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" name="name" aria-describedby="emailHelp" readonly  value="<?= $data['user']['name']?>">
            <small id="emailHelp" class="form-text text-muted">Please enter your correct full name/Business Name here.</small>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" aria-describedby="emailHelp" readonly value="<?= $data['user']['email']?>">
            <small id="emailHelp" class="form-text text-muted">Please enter your correct fll name here.</small>
        </div>

        <div class="form-group">
            <label for="ame">Location</label>
            <input type="text" class="form-control" name="location" aria-describedby="emailHelp"  value="<?= $data['user']['location']?>">
            <small id="emailHelp" class="form-text text-muted">Please enter your Location</small>
        </div>

        <div class="form-group">
            <label for="alternative_phone">Alterative Phone</label>
            <input type="number" class="form-control" name="alternative_phone" aria-describedby="emailHelp"  value="<?= $data['user']['alternative_phone']?>" />
            <small id="emailHelp" class="form-text text-muted">Please enter your alternative phone number here for contact.</small>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="phone" aria-describedby="emailHelp" readonly value="<?= $data['user']['phone']?>"/>
        </div>

        <div class="form-group">
            <label for="alternative_phone">Password</label>
            <input type="password" class="form-control" name="password" aria-describedby="emailHelp" />
            <small id="emailHelp" class="form-text text-muted">Please make sure passwords are the same.</small>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" aria-describedby="emailHelp"/>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-success" id="submit" value="Edit Account" />
        </div>


    </form>
</div>
