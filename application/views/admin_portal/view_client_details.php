<div class="container-fluid display_order_details">
    <ul>
        <li><b><?= $data['client']->name ?></b></li>
        <?php if ($data['client']->status == 1): ?>
            <a href="<?= base_url() ?>AppAdmin/clients/suspend/<?= $data['client']->id ?>"
            <li>
                <button class="btn btn-sm btn-danger">Suspend</button>
            </li>
        <?php elseif ($data['client']->status == 0): ?>
            <a href="<?= base_url() ?>AppAdmin/clients/unsuspend/<?= $data['client']->id ?>"
            <li>
                <button class="btn btn-sm btn-secondary">Un Suspend</button>
            </li>
        <?php endif; ?>

    </ul>
</div>
<br>
<div class="container-fluid display_order_details">
    <h5>Client Details </h5>

    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%">
        <tr>
            <td>Client Name</td>
            <td><?= $data['client']->name ?></td>
            <td>Status</td>
            <td><?= ($data['client']->status == 0 ? "Suspended" : "Active") ?></td>
        </tr>
        <tr>
            <td>Client Email</td>
            <td><?= $data['client']->email ?></td>
            <td>Phone</td>
            <td><?= $data['client']->phone ?></td>
        </tr>
        <tr>
            <td>Date Added</td>
            <td><?= $data['client']->date ?></td>
            <td>Date Modified</td>
            <td><?= $data['client']->date_modified ?></td>
        </tr>
        <tr>
            <td>Location</td>
            <td><?= $data['client']->location ?></td>
            <td>Date Modified</td>
            <td><?= $data['client']->date_modified ?></td>
        </tr>
        <tr>
            <td>Password last Modified</td>
            <td><?= $data['client']->password_last_modified ?></td>
            <td>Last Login</td>
            <td><?= $data['client']->last_login ?></td>
        </tr>
        <tr>
            <td>Account Approved</td>
            <td><?= ($data['client']->approved == 1 ? "Approved" : "NOT APPROVED") ?></td>
            <td>Force Edit Password</td>
            <td><?= $data['client']->user_should_edit_password ?></td>
        </tr>
        </table>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <h5>Payment Balances</h5>
    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%">

            <tr>
                <td>Order Number</td>
                <td>Item</td>
                <td>Vendor</td>
                <td>Amount Paid</td>
                <td>Balance</td>
                <td>Last Transaction Date</td>
            </tr>
            <?php
            foreach ($data['payment_balances'] as $paymentbalance):
                ?>
                <tr>
                    <td><?= $payment_balances['order_number'] ?></td>
                    <td><?= $payment_balances['name'] ?></td>
                    <td><?= $payment_balances['owner_name'] ?></td>
                    <td><?= $payment_balances['total_paid'] ?></td>
                    <td><?= $payment_balances['balance'] ?></td>
                    <td><?= $payment_balances['last_transaction_date'] ?></td>
                </tr>

            <?php endforeach; ?>
        </table>
    </div>
</div>


