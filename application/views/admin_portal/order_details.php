<?php
$orderDetails = $data['order'];
$paymentBalance = $data['payment_balances'];
?>

<div class="container-fluid display_order_details">
    <h5><?= $orderDetails->orderNumber ?> &nbsp;&nbsp; - <?= $orderDetails->itemName ?> &nbsp; <span
                class="alert alert-info"> <?= ($orderDetails->orderStatus == 0 ? "PENDING" : ($orderDetails->orderStatus == 1 ? "Approved" : "Rejected")) ?></span>
    </h5>
    <ul>
        <?php if ($orderDetails->orderStatus == 0 && $orderDetails->orderUserCancel == 0): ?>
            <li>
                <button class="btn btn-sm btn-outline-success" data-toggle="modal" data-target="#exampleModalCenter">
                    Accept Order
                </button>
            </li>
            <li>
                <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#rejectOrder">Reject
                    Order
                </button>
            </li>
        <?php endif ?>
    </ul>

</div>
<br>


<div class="container-fluid display_order_details">
    <h5>Oder Details</h5>
    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%">
            <tr>
                <td>Order Number</td>
                <td><?= $orderDetails->orderNumber ?></td>
                <td>Order Status</td>
                <td><?= ($orderDetails->orderStatus == 0 ? "PENDING" : ($orderDetails->orderStatus == 1 ? "Approved" : "Rejected")) ?></td>
            </tr>
            <tr>
                <td>Order Item</td>
                <td><?= $orderDetails->itemName ?></td>
                <td>Place of Use</td>
                <td><?= $orderDetails->place_of_use ?></td>
            </tr>
            <tr>
                <td>Number of Days</td>
                <td><?= $orderDetails->number_of_days ?></td>
                <td>Oder Item Price</td>
                <td><?= $orderDetails->orderPricePerUnit ?></td>
            </tr>
            <tr>
                <td>Order Amount</td>
                <td><?= $orderDetails->orderAmount ?></td>
                <td>Place of Use</td>
                <td><?= $orderDetails->place_of_use ?></td>
            </tr>
            <tr>
                <td>Usage Description</td>
                <td colspan="3"><?= $orderDetails->usage_description ?></td>
            </tr>

            <tr>
                <td>User Cancelled</td>
                <td><?= ($orderDetails->orderUserCancel == 1 ? "True" : null) ?></td>
                <td>Order Approved/Cancelled date</td>
                <td><?= $orderDetails->orderApprovalDate ?></td>
            </tr>
            <tr>
                <td>Pick Up Date</td>
                <td><?= $orderDetails->orderPickUpDate ?></td>
                <td>Return Date</td>
                <td><?= $orderDetails->orderReturnDate ?></td>
            </tr>
            <tr>
                <td>Admin Approval</td>
                <td><?= $orderDetails->orderAdminApproval ?></td>
                <td>Admin Approval Date</td>
                <td><?= $orderDetails->orderAdminApprovalDate ?></td>
            </tr>
            <tr>
                <td>Comments</td>
                <td><?= $orderDetails->orderComments ?></td>
                <td>Order Closed</td>
                <td><?= $orderDetails->orderClosedStatus ?></td>
            </tr>
        </table>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <h5>Item Details</h5>
    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%"
        ">
        <tr>
            <td>System Item Number</td>
            <td><?= $orderDetails->item_number ?></td>
            <td>Identification Number</td>
            <td><?= $orderDetails->identification_number ?></td>
        </tr>
        <tr>
            <td>Item Name</td>
            <td><?= $orderDetails->itemName ?></td>
            <td>Price</td>
            <td><?= number_format($orderDetails->price) ?></td>
        </tr>
        <tr>
            <td>Item Color</td>
            <td><?= $orderDetails->itemColor ?></td>
            <td>Item Size</td>
            <td><?= $orderDetails->itemSize ?></td>
        </tr>
        <tr>
            <td>Brief Description</td>
            <td colspan="3"><?= $orderDetails->brief_description ?></td>
        </tr>
        <tr>
            <td>Pick Up Location</td>
            <td><?= $orderDetails->pick_up_location ?></td>
            <td>Return Date</td>
            <td><?= $orderDetails->orderReturnDate ?></td>
        </tr>

        </table>

        <div class="row order_details_images">
            <div class="col-sm-4"><img class="d-block w-100"
                                       src="<?= base_url() ?>items/<?= $orderDetails->front_view ?>" alt="Front View">
            </div>
            <div class="col-sm-4"><img class="d-block w-100"
                                       src="<?= base_url() ?>items/<?= $orderDetails->side_view ?>" alt="Front View">
            </div>
            <div class="col-sm-4"><img class="d-block w-100"
                                       src="<?= base_url() ?>items/<?= $orderDetails->rear_view ?>" alt="Front View">
            </div>
        </div>

    </div>
</div>

<br>
<?php
if ($paymentBalance):
    ?>
    <div class="container-fluid display_order_details">
        <h5>Payment Balances</h5>
        <div class="row">
            <table class="table result_table_light table-hover myorders_table" width="100%"
            ">
            <tr>
                <td>Total AMount</td>
                <td><?= $paymentBalance->amount ?></td>
                <td>Amount Paid</td>
                <td><?= $paymentBalance->total_paid ?></td>
                <td>Balance</td>
                <td><?= $paymentBalance->balance ?></td>
                <td>Last Transaction Date</td>
                <td><?= $paymentBalance->last_transaction_date ?></td>
            </tr>
            </table>
        </div>
    </div>
<?php
endif;
?>


<div class="container-fluid" xmlns="http://www.w3.org/1999/html">


    <br>


    <div class="row">
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" action="<?= base_url() ?>AppAdmin/orders/approve/<?= $orderDetails->orderId ?>"
                      id="acceptOrder">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Accept Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="alternative_phone">Pick up Date</label>
                                <input type="date" class="form-control" name="pick_up_date" aria-describedby="emailHelp"
                                       value="" required/>
                                <small id="emailHelp" class="form-text text-muted">Please enter the date this item can
                                    be picked.
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="alternative_phone">Any additional Comment</label>
                                <input type="text" class="form-control" name="comment" aria-describedby="emailHelp"
                                       value=""/>
                                <small id="emailHelp" class="form-text text-muted">Any comment to te user before they
                                    pick item.
                                </small>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">close</button>
                            <button type="submit" class="btn btn-success btn-sm">update order</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="modal fade" id="rejectOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" action="<?= base_url() ?>AppAdmin/orders/reject/<?= $orderDetails->orderId ?>"
                      id="acceptOrder">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Reject Order</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="alternative_phone">Provide Reject Reason</label>
                                <textarea class="form-control" name="comment" aria-describedby="emailHelp" value=""
                                          required></textarea>
                                <small id="emailHelp" class="form-text text-muted">state why you have refused the
                                    offer.
                                </small>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">close</button>
                            <button type="submit" class="btn btn-success btn-sm">update order</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>

