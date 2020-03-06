<div class="container-fluid display_order_details">
    <h5>VIEW ORDER DETAILS FOR <?= $data->orderNumber?>  &nbsp;&nbsp; - <?= $data->itemName?></h5>
    <ul>
        <li><a href="<?=base_url()?>Customer/ExtendItemOrderRequest/<?=base64_encode($data->orderNumber)?>/extend"><button class="btn btn-sm btn-outline-success">Extend Order</button></a> </li>

        <?php if($data->orderStatus==0 && $data->orderUserCancel==0):?>
        <li><a href="<?=base_url()?>Customer/Orders/cancel/<?=$data->orderId?>"><button class="btn btn-sm btn-outline-danger">Cancel Order</button></a> </li>
        <?php endif ?>

        <!--<li><a href=""><button class="btn btn-sm btn-outline-success">Rank Order</button></a> </li>-->
        <li><a href="<?=base_url()?>Customer/Messages/<?=$data->ownerId*999999 ?>"><button class="btn btn-sm btn-outline-success">Chat with Someone</button></a> </li>
        <li><a href="<?=base_url()?>Customer/Orders/ConfirmCompletion/<?=$data->orderId ?>"><button class="btn btn-sm btn-outline-success">CONFIRM ORDER COMPLETION</button></a> </li>
    </ul>
</div>
<br>



<div class="container-fluid display_order_details">
    <h5>Oder Details</h5>
<!--    o.id as orderId,-->

    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%"">
            <tr><td>Order Number</td><td><?= $data->orderNumber?></td> <td>Order Status</td><td><?= ($data->orderStatus==0?"PENDING":($data->orderStatus==1?"Approved":"Rejected"))?></td></tr>
            <tr><td>Order Item</td><td><?= $data->itemName?></td> <td>Place of Use</td><td><?= $data->place_of_use?></td></tr>
            <tr><td>Number of Days</td><td><?= $data->number_of_days?></td> <td>Oder Item Price</td><td><?= $data->orderPricePerUnit?></td></tr>
            <tr><td>Order Amount</td><td><?= $data->orderAmount?></td> <td>Place of Use</td><td><?= $data->place_of_use?></td></tr>
            <tr><td>Usage Description</td><td colspan="3"><?= $data->usage_description?></td> </tr>

            <tr><td>User Cancelled</td><td><?= ($data->orderUserCancel==1?"True":null)?></td> <td>Order Approved/Cancelled date</td><td><?= $data->orderApprovalDate?></td></tr>
            <tr><td>Pick Up Date</td><td><?= $data->orderPickUpDate?></td> <td>Return Date</td><td><?= $data->orderReturnDate?></td></tr>
            <tr><td>Admin Approval</td><td><?= $data->orderAdminApproval?></td> <td>Admin Approval Date</td><td><?= $data->orderAdminApprovalDate?></td></tr>
            <tr><td>Comments</td><td><?= $data->orderComments?></td> <td>Order Closed</td><td><?= $data->orderClosedStatus?></td></tr>
        </table>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <h5>Item Details</h5>
    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%"">
            <tr><td>System Item Number</td><td><?= $data->item_number?></td> <td>Identification Number</td><td><?= $data->identification_number?></td></tr>
            <tr><td>Item Name</td><td><?= $data->itemName?></td> <td>Price</td><td><?= number_format($data->price)?></td></tr>
            <tr><td>Item Color</td><td><?= $data->itemColor?></td> <td>Item Size</td><td><?= $data->itemSize?></td></tr>
            <tr><td>Brief Description</td><td colspan="3"><?= $data->brief_description?></td> </tr>
            <tr><td>Pick Up Location</td><td><?= $data->pick_up_location?></td> <td>Return Date</td><td><?= $data->orderReturnDate?></td></tr>

        </table>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <h5>Payment Balances</h5>
    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%"">
            <tr><td>Total AMount</td><td><?= $data->amount?></td> <td>Amount Paid</td><td><?= $data->total_paid?></td>
                <td>Balance</td><td><?= $data->balance?></td> <td>Last Transaction Date</td><td><?= $data->last_transaction_date?></td></tr>
        </table>
    </div>
</div>
