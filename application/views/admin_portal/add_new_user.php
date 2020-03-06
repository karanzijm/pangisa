<div class="container-fluid clientregistration" style="max-width: 100vw; padding: 30px;">
    <div class="row">
        <h1>Add User Account</h1>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <form method="post" action="<?=base_url()?>AppAdmin/users/save">
                <div class="form-group">
                    <label for="email">Name</label>
                    <input type="text" class="form-control" name="name" aria-describedby="emailHelp" required>
                    <small id="emailHelp" class="form-text text-muted">Please enter your  full name here.</small>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" aria-describedby="emailHelp" readonly >
                    <small id="emailHelp" class="form-text text-muted">please follow this format lastname.firstname@pangisa.co.ug.</small>
                </div>


                <div class="form-group">
                    <label for="email">User Role</label>
                    <select  name="role" aria-describedby="emailHelp" required >
                        <option value="">select Role</option>
                        <option value="sys_user" selected>System User</option>
                        <option value="sys_admin">System Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="email">Location</label>
                    <input type="text" class="form-control" name="location" value="Pangisa Head Quarters" readonly aria-describedby="emailHelp" required >
                    <small id="emailHelp" class="form-text text-muted">Where are you Located.</small>
                </div>

                <div class="form-group">
                    <label for="email">Phone</label>
                    <input type="number" class="form-control" name="phone" aria-describedby="emailHelp" required >
                    <small id="emailHelp" class="form-text text-muted">Where are you Located.</small>
                </div>

                <div class="form-group">
                    <label for="email">Alternative Phone</label>
                    <input type="number" class="form-control" name="alternative_phone" aria-describedby="emailHelp"  >
                    <small id="emailHelp" class="form-text text-muted">Where are you Located.</small>
                </div>

                <div class="form-group">
                    <label for="email">Password</label>
                    <input type="password" class="form-control" name="password" aria-describedby="emailHelp" minlength="6" >
                    <small id="emailHelp" class="form-text text-muted">Make sure passwords are the same.</small>
                </div>

                <div class="form-group">
                    <label for="email">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" aria-describedby="emailHelp" minlength="6" >
                    <small id="emailHelp" class="form-text text-muted">Make sure passwords are the same.</small>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-success" id="submit" value="Add Account" />
                </div>

            </form>
        </div>
    </div>
</div>
