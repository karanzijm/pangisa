<?php
$liquidation_requests = $data['liquidation_requests'];
?>

<!--background: /*#24879d;*/ rgba(36, 135, 157, 0.7) /*rgba(33, 150, 243, 0.6);*/;-->
<style>
    .pg-vendor-account-gl-tabs {
        padding: 10px;
        background: rgb(100, 111, 119);;
        /*background: rgba(36, 135, 157, 0.2);*/
        color: #ffffff;
        min-height: inherit;
    }

    .pg-vendor-account-gl-tabs h4 {
        font-weight: 800;
    }

    .pg-vendor-account-gl .row .col-sm-3 {
        padding: 3px 10px 3px 0px;
    }

    .pg-vendor-account-gl .row .col-sm-3:last-child {
        padding-right: 0px;
    }
</style>

<h3>Vendor Liquidation requests</h3>

<div class="container-fluid pg-vendor-account-gl">
    <div class="row">
        <div class="col-sm-12">
            <table class="table" cellspacing="0px" cellpadding="0px" width="100%">
                <tr>
                    <td>Account</td>
                    <td>Vendor Name</td>
                    <td>Amount</td>
                    <td>Current Bal</td>
                    <td>Exp Bal After</td>
                    <td>Mode</td>
                    <td>Receiving Acc</td>
                    <td>Ref#</td>
                    <td>Date</td>
                    <td>Status</td>
                    <td>Remmarks</td>
                    <td>Action</td>
                </tr>

                <?php
                foreach ($liquidation_requests as $transaction) {
                    $payment_link = $transaction['status'] == 0 ? '<button class="btn btn-sm btn-secondary update_liquidation_request_button"  data-id="' . $transaction['id'] . '">UPDATE</button>' : null;

                    echo "<tr>";
                    echo "<td>" . $transaction['vendor_account'] . "</td>";
                    echo "<td>" . $transaction['name'] . "</td>";
                    echo "<td>" . number_format($transaction['amount']) . "</td>";
                    echo "<td>" . number_format($transaction['current_balance']) . "</td>";
                    echo "<td>" . number_format($transaction['balance_after']) . "</td>";
                    echo "<td>" . ($transaction['mode_of_transaction']) . "</td>";
                    echo "<td>" . ($transaction['receiving_account']) . "</td>";
                    echo "<td>" . $transaction['reference_number'] . "</td>";
                    echo "<td>" . $transaction['date'] . "</td>";
                    echo "<td>" . ($transaction['status'] == 0 ? "Pending" : ($transaction['status'] == 1 ? "Approved" : "Rejected")) . "</td>";
                    echo "<td>" . $transaction['update_remmarks'] . "</td>";

                    echo "<td>";
                    if ($transaction['status'] == 0) {
                        echo $payment_link;
                    }
                    echo "</td>";

                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="update_liquidation_request_div" tabindex="-1" role="dialog"
     aria-labelledby="MobileMoneyPayments"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header alert alert-dark">
                <h5 class="modal-title" id="exampleModalLabel">Update Liquidation Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url() ?>AppAdmin/approve_liquidation_request/"
                      enctype="multipart/form-data" class="update_liquidation_request_form">
                    <label>Requested Amount</label>
                    <input class="form-control" readonly value="<?= number_format($transaction['amount']) ?>"/>

                    <label>Mode of Reception</label>
                    <input class="form-control" readonly value="<?= $transaction['mode_of_transaction'] ?>"/>

                    <label>Receiving Account</label>
                    <input class="form-control" readonly value="<?= $transaction['receiving_account'] ?>"/>

                    <label>Balance Adjustment</label>
                    <input class="form-control" readonly
                           value="From <?= number_format($transaction['current_balance']) . " to " . number_format($transaction['balance_after']) ?>"/>

                    <br>
                    <b>Approve or Reject Request</b><br>
                    <input type="radio" class="custom-radio status" value="1" name="status" required/> Approved &nbsp;&nbsp;&nbsp;
                    <input type="radio" class="custom-radio status" value="2" name="status" required/> Rejected

                    <br><br>
                    <input type="text" name="comments" placeholder="Approval/Rejection Remarks"
                           class="form-control"/>

                    <br>
                    <button type="submit" class="btn btn-sm btn-outline-success">SUBMIT</button>
                </form>

                <p class="trigger_liquidation_div_response">

                </p>

            </div>
            <div class="modal-footer ">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
    var liquidation_id = null;
    $('.update_liquidation_request_button').click(function (e) {
        liquidation_id = $(this).attr("data-id");
        $('#update_liquidation_request_div').modal("show");
    });

    //submit form
    $('.update_liquidation_request_form').submit(function (e) {
        e.preventDefault();
        $('.trigger_liquidation_div_response').text("Loading , Please wait ....");

        //values
        var inputs = $('.update_liquidation_request_form :input');
        var values = {};
        inputs.each(function () {
            values[this.name] = $(this).val();
        });

        values['status'] = $('input[name=status]:checked').val();
        var submit_link = $(this).attr('action') + liquidation_id;

        $.ajax({
            type: "POST",
            url: submit_link,
            data: values,
            beforeSend: function () {
                $('.trigger_liquidation_div_response').text("Sending Request to Server , please wait ....");
            },
            success: function (response) {
                $('.trigger_liquidation_div_response').text(response);
                location.reload(true);
            }, error: function (error) {
                $('.trigger_liquidation_div_response').text(error);
                location.reload(true);
            }

        });
    });
</script>
