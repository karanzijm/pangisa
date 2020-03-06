<div class="container-fluid">
    <div class="row">
        <?php echo form_open_multipart(base_url()."AppAdmin/items/"); ?>
        <SPAN><b>Items</b></SPAN> &nbsp;&nbsp;&nbsp;
        <span class="filter_search_item">
                <input type="text"  name="filter_search_item" placeholder="name or Identification"/>
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
                <td>Ad Number</td>
                <td>Name</td>
                <td>Owner</td>
                <td>Category</td>
                <td>Identification</td>
                <td>Year</td>
                <td>Orders</td>
                <td>Verified</td>
                <td>Date</td>

                <?php if($this->session->user['role']=="sys_admin"): ?>
                    <td>Action</td>
                <?php endif; ?>
            </tr>

            <?php
            $i=1;
            foreach ($data as $item):
                echo "<tr>";
                echo "<td>".$i."</td>";
                echo "<td>".$item['item_number']."</td>";
                echo "<td>".$item['name']."</td>";
                echo "<td>".$item['owner']."</td>";
                echo "<td>".$item['category']."</td>";
                echo "<td>".$item['Identification']."</td>";
                echo "<td>".$item['Year of Make']."</td>";
                echo "<td>".$item['Orders']."</td>";
                echo "<td>".($item['verified']==1?'TRUE':null)."</td>";
                echo "<td>".$item['date']."</td>";

                if($this->session->user['role']=="sys_admin"):
                    $action_link_for_items = $item['status'] == 1 ?
                        "<a href='" . base_url() . "AppAdmin/items/suspend/" . $item['id'] . "'><i class='fa fa-lock'></i> </a>"
                        : "<a href='" . base_url() . "AppAdmin/items/unsuspend/" . $item['id'] . "'><i class='fa fa-unlock'></i> </a>";
                    echo"<td>";
                    echo"<a href='".base_url()."AppAdmin/items/view/".$item['id']."'><i class='fa fa-eye'></i> </a> &nbsp;";
                    echo $action_link_for_items;
                    echo"</td>";
                endif;

                echo "</tr>";
                $i++;
            endforeach;

            ?>
        </table>
    </div>
</div>
