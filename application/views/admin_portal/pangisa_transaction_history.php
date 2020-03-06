<style>
    .display_amounts_admin_page .row .col-sm-3 div {
        margin: 20px;
        text-align: center;
        padding: 10px;
        font-size: 13pt;
        font-weight: 800;
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2)
    }
</style>

<section class="transactions-search-field">
    <form enctype="multipart/form-data" action="<?= base_url() ?>AppAdmin/transactions" method="post">
        <b>Plangisa Transactions History</b> &nbsp;&nbsp;&nbsp;
        <select name="search_mode" required>
            <option value="">Choose Search Item</option>
            <option value="order_number">Order Number</option>
            <option value="transactionid">Transaction Id</option>
            <option value="ext_transaction_id">External Transaction Id</option>
        </select>
        <input type="text" name="search_key" placeholder="Enter Search Key" style="width: 400px !important;"/>
        <button type="search" class="btn-sm btn-info" id="transactions-search-field-submit-button">Search</button>
    </form>
</section>


<div class="container-fluid display_amounts_admin_page">
    <div class="row">
        <div class="col-sm-3">
            <div>
                <b>Collected</b>
                <hr/>
                <?= number_format($data['main_account']->amount_collected) ?>
            </div>
        </div>

        <div class="col-sm-3">
            <div>
                <b>Amount Paid out</b>
                <hr/>
                <?= number_format($data['main_account']->amount_paid_out) ?>
            </div>
        </div>

        <div class="col-sm-3">
            <div>
                <b>Balance</b>
                <hr/>
                <?= number_format($data['main_account']->balance) ?>
            </div>
        </div>

        <div class="col-sm-3">
            <div>
                <b>Last transaction date</b>
                <hr/>
                <?= $data['main_account']->last_transaction_date ?>
            </div>
        </div>
    </div>
</div>


<table class="table lib_table table-hover">
    <tr>
        <td>#</td>
        <td>Reference</td>
        <td>Transaction type</td>
        <td>Transaction Id</td>
        <td>Balance Before</td>
        <td>Balance After</td>
        <td>Amount</td>
        <td>Channel Name</td>
        <td>Payment Date</td>
    </tr>

    <?php

    if (!$data['transactions']) {
        echo "<div class='alert alert-info'><b>your search returned no results</b></div>";
    }

    $i = 1;
    foreach ($data['transactions'] as $transation) {
        echo "<tr class='view_transaction_details' txid='" . $transation['id'] . "'>";
        echo "<td>" . $i . "</td>";
        echo "<td><a href='#'>" . $transation['order_number'] . "</a> </td>";
        echo "<td>" . $transation['transaction_type'] . "</td>";
        echo "<td>" . $transation['pangisa_transaction_id'] . "</td>";
        echo "<td>" . $transation['balance_before'] . "</td>";
        echo "<td>" . $transation['balance_after'] . "</td>";
        echo "<td>" . number_format($transation['amount']) . "</td>";
        echo "<td>" . $transation['payment_channel'] . "</td>";
        echo "<td>" . $transation['date'] . "</td>";
        echo "</tr>";
        $i++;
    }
    ?>
</table>

<div class="container-fluid"><?= $data['pagination'] ?></div>

<div class="modal fade" id="MobileMoneyPayments" tabindex="-1" role="dialog" aria-labelledby="MobileMoneyPayments"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body view_transaction_details_body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(".view_transaction_details").click(function () {
        $("#MobileMoneyPayments").modal("show");
        var txid = $(this).attr("txid");

        $.ajax(
            {
                type: "GET",
                url: "<?=base_url()?>AppAdmin/pangisa_escrow_details/" + txid,
                data: null,
                beforeSend: function () {
                    $(".view_transaction_details_body").html("<img src='<?= base_url() ?>resources/images/spinner.gif' width=30% />");
                },
                success: function (response) {
                    $(".view_transaction_details_body").html(response);
                }
            }
        );
    });
</script>
