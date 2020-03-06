<div class="container-fluid">
    <div class="row">
        <?=form_open_multipart(base_url()."AppAdmin/users/"); ?>
        <SPAN><b>System Users</b></SPAN> &nbsp;&nbsp;&nbsp;
        <span class="filter_search_item">
                <input type="text"  name="filter_search_item" placeholder="name email or phone"/>
            </span> &nbsp;
        <button type="submit" class="btn btn-sm btn-success" ><i class="fa fa-search" aria-hidden="true"></i> </button>
        <a class="btn btn-sm btn-success" href="<?=base_url()?>AppAdmin/users/add">Add New User </a>
        </form>
    </div>

    <table class='table  lib_table' style='width: 100% !important;'>
    <tr>
        <td>#</td>
        <td>Name</td>
        <td>Email</td>
        <td>Phone</td>
        <td>Role</td>
        <td>Location</td>
        <td>Status</td>
        <td>Date</td>
        <td>Action</td>
    </tr>

        <?php
            $i=1;
            foreach ($data as $user){
                echo " <tr>";
                echo "<td>".$i."</td>";
                echo "<td>".$user['name']."</td>";
                echo "<td>".$user['email']."</td>";
                echo "<td>".$user['phone']."</td>";
                echo "<td>".$user['role']."</td>";
                echo "<td>".$user['location']."</td>";
                echo "<td>".$user['status']."</td>";
                echo "<td>".$user['date']."</td>";
                echo "<td>";
                if($this->session->user['role']=="sys_admin"){
                    if($user['status']=="Active"){
                        echo "<a href='".base_url()."AppAdmin/users/block/".$user['id']."'><i class='fa fa-lock'></i></a>";
                    }else{
                        echo "<a href='".base_url()."AppAdmin/users/unblock/".$user['id']."'><i class='fa fa-lock'></i></a>";
                    }

                    echo " &nbsp;&nbsp;&nbsp;<a href='".base_url()."AppAdmin/users/edit/".$user['id']."'><i class='fa fa-edit'></i></a>";
                }
                echo "</td>";
                echo "</tr>";

                $i++;

            }
        ?>

</table>
</div>