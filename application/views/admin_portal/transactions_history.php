<section class="transactions-search-field">
    <form enctype="multipart/form-data" action="<?= base_url() ?>AppAdmin/transactions" method="post">
        <b>Transactions History</b> &nbsp;&nbsp;&nbsp;
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


<table class="table lib_table table-hover">
    <tr>
        <td>#</td>
        <td>Reference</td>
        <td>Client</td>
        <td>Transaction Id</td>
        <td>Amount Payable</td>
        <td>Amount</td>
        <td>Channel Name</td>
        <td>Payment Date</td>
        <td>status</td>
    </tr>

    <?php

    if (!$data['transactions']) {
        echo "<div class='alert alert-info'><b>your search returned no results</b></div>";
    }

    $i = 1;
    foreach ($data['transactions'] as $transation) {
        $alert_display = $transation['status'] == 1 ? "alert-success" : ($transation['status'] == 2?"alert-danger":"alert-primary");
        echo "<tr class='view_transaction_details' txid='" . $transation['id'] . "'>";
        echo "<td>" . $i . "</td>";
        echo "<td><a href='#'>" . $transation['order_number'] . "</a> </td>";
        echo "<td>" . $transation['client'] . "</td>";
        echo "<td>" . $transation['transactionid'] . "</td>";
        echo "<td>" . number_format($transation['amountpayable']) . "</td>";
        echo "<td>" . number_format($transation['amount']) . "</td>";
        echo "<td>" . $transation['channel_name'] . "</td>";
        echo "<td>" . $transation['payment_date'] . "</td>";
        echo "<td> <span class='alert $alert_display'>" . ($transation['status'] == 1 ?"Cleared":($transation['status']==2?"Failed" : "Pending")) . "</span></td>";
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
                url: "<?=base_url()?>AppAdmin/view_transaction_details/" + txid,
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
