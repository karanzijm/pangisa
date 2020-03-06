<div class="container-fluid">
    <div class="row">
        <?php echo form_open_multipart(base_url()."AppAdmin/partners/"); ?>
            <SPAN><b>PARTNERS</b></SPAN> &nbsp;&nbsp;&nbsp;
            <span class="filter_search_item">
                <input type="text"  name="filter_search_item" placeholder="name, phone ,location or mail"/>
                start date <input type="date"  name="startdate" />
                end date <input type="date"  name="enddate" />
            </span> &nbsp;
                <button type="submit" class="btn btn-sm btn-success" ><i class="fa fa-search" aria-hidden="true"></i> </button>
        </form>
    </div>

    <div class="row">
        <table class='table lib_table' style='width: 100% !important;'>
            <tr>
                <td>#</td>
                <td>Vendor Code</td>
                <td>Name</td>
                <td>Email</td>
                <td>Phone</td>
                <td>Location</td>
                <td>Items</td>
                <td>Orders</td>
                <td>Successful</td>
                <td>Status</td>
                <td>Date</td>

                <?php if($this->session->user['role']=="admin"): ?>
                    <td>Action</td>
                <?php endif; ?>
            </tr>

                <?php
                    $i=1;
                    foreach ($data as $partner):
                        echo "<tr>";
                        echo "<td>".$i."</td>";
                        echo "<td>" . $partner['user_code'] . "</td>";
                        echo "<td>".$partner['name']."</td>";
                        echo "<td>".$partner['email']."</td>";
                        echo "<td>".$partner['phone']."</td>";
                        echo "<td>".$partner['location']."</td>";
                        echo "<td>".$partner['Items']."</td>";
                        echo "<td>".$partner['Orders']."</td>";
                        echo "<td>".$partner['Successful']."</td>";
                        echo "<td>".$partner['status']."</td>";
                        echo "<td>".$partner['date']."</td>";

                        if($this->session->user['role']=="admin"):
                            echo"<td>";
                            echo"<a href='".base_url()."AppAdmin/partners/view/".$partner['id']."'><i class='fa fa-eye'></i> </a> &nbsp;";

                            if($partner['status']=="Active"){
                                echo"<a href='".base_url()."AppAdmin/partners/suspend/".$partner['id']."'><i class='fa fa-lock'></i> </a>";
                            }else{
                                echo"<a href='".base_url()."AppAdmin/partners/renew/".$partner['id']."'><i class='fa fa-unlock'></i> </a>";
                            }
                            echo"</td>";
                        endif;

                        echo "</tr>";
                        $i++;
                    endforeach;

                ?>
        </table>
    </div>
</div>

<!--<div class="container-fluid">--><? //= $pagination  ?><!--</div>-->
