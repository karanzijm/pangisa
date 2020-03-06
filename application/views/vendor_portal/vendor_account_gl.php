<?php
$accountGl = $data['vendor_account_gl'];
$transaction_history = $data['vendor_account_gl_transaction_history'];
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

<h3>Account Statement</h3>
<!--<br>-->

<div class="container-fluid pg-vendor-account-gl">
    <div class="row">
        <div class="col-sm-3">
            <div class="pg-vendor-account-gl-tabs">
                <h4>Total Collected</h4>
                <b><h5>Ugx <?= number_format($accountGl->total_amount_collected) ?></h5></b>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="pg-vendor-account-gl-tabs">
                <h4>Total Withdrawn <a href="#" class=" trigger_liquidation_button btn btn-danger btn-sm"><i
                                class="fa fa-cc-visa"></i> WITHDRAW </a></h4>
                <b><h6>Ugx <?= number_format($accountGl->total_amount_withdrawn) ?></h6></b>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="pg-vendor-account-gl-tabs">
                <h4>Balance</h4>
                <b><h5>Ugx <?= number_format($accountGl->balance) ?: 0 ?></h5></b>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="pg-vendor-account-gl-tabs">
                <h4>Last Withdraw Date</h4>
                <b><h5><?= $accountGl->last_withdraw_date ?: 'No Withdraw' ?></h5></b>
            </div>
        </div>
    </div>
</div>

<!--withdraw pop up-->
<div class="modal fade" id="trigger_liquidation_div" tabindex="-1" role="dialog" aria-labelledby="MobileMoneyPayments"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header alert alert-dark">
                <h5 class="modal-title" id="exampleModalLabel">Create new Withdraw Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="<?= base_url() ?>AppClient/create_liquidation_request"
                      enctype="multipart/form-data" class="liquidation-request-form">
                    <label>Enter Amount</label>
                    <input type="number" name="amount" class="form-control"
                           placeholder="Enter amount not bigger than your balance" required/>
                    <label>Mode of Reception</label>
                    <select name="mode_of_transaction" required class="form-control">
                        <option value="mobile_money">Mobile Money</option>
                        <option value="bank">EFT - Bank Deposit</option>
                        <option value="cheque">Bank Cheque</option>
                    </select>
                    <label>Liquidation Notes</label>
                    <input type="number" name="comments" placeholder="any notes added to liquidation request"
                           class="form-control" readonly/>


                    <label>Phone Number</label>
                    <input type="number" name="phone_number" value="<?php $accountGl->momo_collection_number ?>"
                           class="form-control" readonly/>

                    <label>Bank Account</label>
                    <input type="number" name="bank_account" value="<?php $accountGl->bank_account_number ?>"
                           class="form-control" readonly/>

                    <label>Bank Name</label>
                    <input type="number" name="bank_name" value="<?php $accountGl->bank ?>" class="form-control"
                           readonly/>

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


<br>
<div class="container-fluid pg-vendor-account-gl">
    <div class="row">
        <div class="col-sm-12">
            <table class="table" cellspacing="0px" cellpadding="0px" width="100%">
                <tr>
                    <td>Order_number</td>
                    <td>Amount</td>
                    <td>Balance before</td>
                    <td>Balance after</td>
                    <td>Transaction type</td>
                    <td>Transaction Channel</td>
                    <!--                    <td>Payer Account</td>-->
                    <!--                    <td>Channel</td>-->
                    <!--                    <td>Reference Number</td>-->
                    <!--                    <td>Ext Transaction Id</td>-->
                    <td>Transaction Id</td>
                    <!--                    <td>Reversed</td>-->
                    <td>Date</td>
                </tr>

                <?php
                foreach ($transaction_history as $transaction) {
                    echo "<tr>";
                    echo "<td>" . $transaction['order_number'] . "</td>";
                    echo "<td>" . number_format($transaction['amount']) . "</td>";
                    echo "<td>" . number_format($transaction['balance_before']) . "</td>";
                    echo "<td>" . number_format($transaction['balance_after']) . "</td>";
                    echo "<td>" . $transaction['transaction_type'] . "</td>";
                    echo "<td>" . $transaction['mode_of_transaction'] . "</td>";
//                        echo"<td>".$transaction['source_destination_momo_number']."</td>";
//                        echo"<td>".$transaction['payment_channel']."</td>";
//                        echo"<td>".$transaction['payment_reference_number']."</td>";
//                        echo"<td>".$transaction['ext_transaction_id']."</td>";
                    echo "<td>" . $transaction['pangisa_transaction_id'] . "</td>";
//                        echo"<td>".$transaction['reversed']."</td>";
                    echo "<td>" . $transaction['date'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

<script>
    $('.trigger_liquidation_button').click(function (e) {
        $('#trigger_liquidation_div').modal("show");
    });

    //submit form
    $('.liquidation-request-form').submit(function (e) {
        e.preventDefault();
        $('.trigger_liquidation_div_response').text("Loading , Please wait ....");

        //values
        var inputs = $('.liquidation-request-form :input');
        var values = {};
        inputs.each(function () {
            values[this.name] = $(this).val();
        });

        if (parseInt(values['amount']) < 500) {
            $('.trigger_liquidation_div_response').text("Zero and Negative numbers not allowed.");
        }

        var submit_link = $(this).attr('action');

        $.ajax({
            type: "POST",
            url: submit_link,
            data: values,
            beforeSend: function () {
                $('.trigger_liquidation_div_response').text("Sending Request to Server , please wait ....");
            },
            success: function (response) {
                $('.trigger_liquidation_div_response').text(response);
            }, error: function (error) {
                $('.trigger_liquidation_div_response').text(error);
            }

        });
    });
</script>
