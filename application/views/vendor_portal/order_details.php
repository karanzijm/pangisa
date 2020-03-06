<h2>Order Details - <?= $data['approved'] == 1 ? "Approved" : ($data['approved'] == 0 ? "Pending" : "Rejected") ?></h2>

<?php
if ($data['is_negotiable'] && $data['item_negotiation_completed'] == 0) {
    ?>
    <hr>
    <div class="container-fluid" style="background:#fff; padding:20px 0px;">
        <div class="row">
            <div class="col-sm-12">

                <span class="alert alert-info">Please Update Order specification as agreed with customer at the end of the negotiation</span><br><br>
                <form enctype="multipart/form-data" method="post"
                      action="<?= base_url() . "AppClient/updateOrderPricing/" . $data['id'] ?>">
                    <label>Order Quantity</label>
                    <input class="form-control" name="quantity" required type="number" value="<?= $data['quantity'] ?>"
                           style="width: 40%"/>

                    <label>Number of Days</label>
                    <input class="form-control" name="number_of_days" required type="number"
                           value="<?= $data['number_of_days'] ?>" style="width: 40%"/>

                    <label>Item Price per Day</label>
                    <input class="form-control" name="price" type="number" required style="width: 40%"
                           value="<?= $data['price'] ?>"/>

                    <br>
                    <input type="submit" class="btn btn-info" value="Confirm Order"/>
                </form>

            </div>
        </div>
    </div>
    <hr>

    <?php
}
?>

<div class="container-fluid" xmlns="http://www.w3.org/1999/html">

    <div class="row">
        <table class="table order_details_table">
            <tr>
                <td>Client Name</td>
                <td><?= $data['name'] ?></td>
                <td>Address</td>
                <td><?= $data['location'] ?></td>
            </tr>
            <tr>
                <td>Item Name</td>
                <td><?= $data['item_name'] ?></td>
                <td>Registration Number</td>
                <td><?= $data['identification_number'] ?></td>
            </tr>
            <tr>
                <td>Item Description</td>
                <td colspan="3"><?= strip_tags($data['brief_description']) ?></td>
            </tr>
            <tr>
                <td>Pick up location</td>
                <td><?= $data['pick_up_location'] ?></td>
            </tr>
            <tr>
                <td>Place of use</td>
                <td><?= $data['place_of_use'] ?></td>
                <td>Usage Description</td>
                <td><?= $data['usage_description'] ?></td>
            </tr>
            <tr>
                <td>Price</td>
                <td><?= $data['price'] . " " . $data['price_point'] ?></td>
                <td>Total Amount</td>
                <td><?= number_format($data['amount']) ?></td>
            </tr>

            <tr>
                <td>Number of Days</td>
                <td><?= $data['number_of_days'] ?></td>
                <td>Order Quantity</td>
                <td><?= number_format($data['quantity']) ?></td>
            </tr>
        </table>

        <br>

        <?php if ($data['approved'] == 0): ?>
            <!--            --><?php //if($data['is_negotiable'] && $data['item_negotiation_completed'==0])?>
            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModalCenter">Accept Order
            </button> &nbsp;&nbsp;&nbsp;
            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectOrder">Reject Order</button>
        <?php endif; ?>
    </div>

    <div class="row">

        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form method="post" action="<?= base_url() ?>AppClient/orders/approve/<?= $data['id'] ?>"
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
                                <input type="text" class="date form-control" name="pick_up_date"
                                       aria-describedby="emailHelp" value="" required/>
                                <small id="emailHelp" class="form-text text-muted">Please enter the date this item can
                                    be picked.
                                </small>
                            </div>

                            <div class="form-group">
                                <label for="comment">Any additional Comment</label>
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
                <form method="post" action="<?= base_url() ?>AppClient/orders/reject/<?= $data['id'] ?>"
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
                                <label for="comment">Provide Reject Reason</label>


                                <input name="comment" class="form-control" required/>
                                <small id="emailHelp" class="form-text text-muted">state why you have refused the
                                    offer.
                                </small>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">close</button>
                            <input type="submit" class="btn btn-success btn-sm" value="update order"/>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>



