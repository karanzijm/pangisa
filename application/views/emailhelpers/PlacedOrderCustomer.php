
<div style="padding:0px; margin:0px; font-family: Calibri; background: #f5f5f5;">
    <section style="background: #fff;">
        <img src="<?= base_url() ?>resources/images/icon.png" width="15%"/>
    </section><br>

    <section style="padding:0px; background: #fff;">
        Dear <?= $userObject->name ?> <br>

        <p>You have placed an order with Pangisa. Your order details are as follows</p>

        <section>
            <table cellpadding="10px" cellspacing="0px">
                <tr><td>Order Number</td><td><?= $orderObject->order_number?></td></tr>
                <tr><td>Item Name</td><td><?= $item->name?></td></tr>
                <tr><td>Registration Number</td><td><?= $item->identification_number?></td></tr>
                <tr><td>Place of Use</td><td><?= $orderObject->place_of_use?></td></tr>
                <tr><td>Number of Days</td><td><?= $orderObject->number_of_days?></td></tr>
                <tr><td>Usage Description</td><td><?= $orderObject->usage_description?></td></tr>
                <tr><td>Order Amount</td><td><?= $item->price*$orderObject->number_of_days?></td></tr>
                <tr><td>Order Date</td><td><?= $orderObject->date?></td></tr>
            </table>
            <br><br>
            <img src="<?=base_url()?>/items/<?=$item->front_view?>" width="150">
            <img src="<?= base_url() ?>/items/<?= $item->side_view ?>" width="150">
            <img src="<?= base_url() ?>/items/<?= $item->rear_view ?>" width="150">

            <br><br>
            Once your order has been approved, you will be able to start payment which can be on mobile money or Sasula. Remember to login and check the order status
        </section><br><br>


    </section>

</div>