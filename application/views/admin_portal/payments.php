<div class="container-fluid">
    <div class="row">
        <?php echo form_open_multipart(base_url()."AppAdmin/payments/"); ?>
        <SPAN><b>Payments</b></SPAN> &nbsp;&nbsp;&nbsp;
        <span class="filter_search_item">
                <input type="text"  name="filter_search_item" placeholder="Order number"/>
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
                <td>Order Number</td>
                <td>Client</td>
                <td>Item</td>
                <td>Owner</td>
                <td>Identification</td>
                <td>Amount</td>
                <td>Total Paid</td>
                <td>Balance</td>
                <td>Last Transaction Date</td>
            </tr>

            <?php
            $i=1;
            foreach ($data as $payment):
                echo "<tr>";
                echo "<td>".$i."</td>";
                echo "<td>".$payment['order_number']."</td>";
                echo "<td>".$payment['client']."</td>";
                echo "<td>".$payment['item']."</td>";
                echo "<td>".$payment['owner_name']."</td>";
                echo "<td>".$payment['identification_number']."</td>";
                echo "<td>".number_format($payment['amount'])."</td>";
                echo "<td>".number_format($payment['total_paid'])."</td>";
                echo "<td>".number_format($payment['balance'])."</td>";
                echo "<td>".$payment['last_transaction_date']."</td>";
                $i++;
            endforeach;

            ?>
        </table>
    </div>
</div>

<div class="container-fluid"><?= $pagination ?></div>