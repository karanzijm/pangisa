<section class="transactions-search-field">
    <form enctype="multipart/form-data" action="<?= base_url() ?>AppAdmin/vendor_collections_account" method="post">
        <b>Vendor Account History</b> &nbsp;&nbsp;&nbsp;
        <select name="search_mode" required>
            <option value="">Choose Search Item</option>
            <option value="account_number">Vendor Account</option>
        </select>
        <input type="text" name="search_key" placeholder="Enter Search Key" style="width: 400px !important;"/>
        <button type="search" class="btn-sm btn-info" id="transactions-search-field-submit-button">Search</button>
    </form>
</section>

<table class="table lib_table table-hover">
    <tr>
        <td>#</td>
        <td>Vendor Account</td>
        <td>Name</td>
        <td>Amount Collected</td>
        <td>Total Amount Withdrawn</td>
        <td>Balance</td>
        <td>Bank Account</td>
        <td>Last withdraw</td>
        <td>Choice of Withdraw</td>
    </tr>

    <?php

    if (!$data['vendor_accounts']) {
        echo "<div class='alert alert-info'><b>your search returned no results</b></div>";
    }

    $i = 1;

    foreach ($data['vendor_accounts'] as $transation) {
        echo "<tr class='view_transaction_details' txid='" . $transation['vendor_id'] . "'>";
        echo "<td>" . $i . "</td>";
        echo "<td><a href='#'>" . $transation['account_number'] . "</a> </td>";
        echo "<td>" . $transation['vendor_name'] . "</td>";
        echo "<td>" . number_format($transation['total_amount_collected']) . "</td>";
        echo "<td>" . number_format($transation['total_amount_withdrawn']) . "</td>";
        echo "<td>" . number_format($transation['balance']) . "</td>";
        echo "<td>" . $transation['bank_account_number'] . "</td>";
        echo "<td>" . $transation['last_withdraw_date'] . "</td>";
        echo "<td>" . $transation['choice_of_collection'] . "</td>";
        echo "</tr>";
        $i++;
    }
    ?>
</table>

<div class="container-fluid"><?= $data['pagination'] ?></div>

<div class="modal fade" id="MobileMoneyPayments" tabindex="-1" role="dialog" aria-labelledby="MobileMoneyPayments"
     aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Transaction Details
                    <button class="btn btn-sm btn-info">View More</button>
                </h5>
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
                url: "<?=base_url()?>AppAdmin/vendor_collections_account_details/" + txid,
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
