<h2>Collections </h2>

<div class="container-fluid" xmlns="http://www.w3.org/1999/html">
    <div class="row">

        <table class="table">
            <tr>
                <td>#</td>
                <td>Reference Number</td>
                <td>Amount</td>
                <td>Reason</td>
                <td>Order Amount</td>
                <td>Balance</td>
                <td>Net Amount</td>
                <td>Paid</td>
                <td>Date</td>
                <td>Date Paid</td>
                <td>Action</td>
            </tr>

            <?php $i = 1;
            $total = 0;
            $balance = 0;
            foreach ($data as $datum): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $datum['reference_number'] ?></td>
                    <td><?= number_format($datum['amount']) ?></td>
                    <td><?= $datum['reason'] ?></td>
                    <td><?= $datum['order_amount'] ?></td>
                    <td><?= number_format($datum['balance']) ?></td>
                    <td><?= $datum['net_amount'] ?></td>
                    <td><?= $datum['paid'] == 0 ? "PENDING" : "PAID" ?></td>
                    <td><?= $datum['date'] ?></td>
                    <td><?= $datum['last_transaction_date'] ?></td>
                    <!--                    <td>--><?php //if ($datum['paid'] == 0) { ?>
                    <!--                            <button class="btn btn-sm btn-secondary trigger_payment_button"-->
                    <!--                                    data-id="--><?//= $datum['id'] ?><!--">PAY-->
                    <!--                            </button> --><?php //} ?><!--</td>-->
                </tr>
                <?php
                $total += $datum['amount'];
                $balance += $datum['balance'];
                $i++;
            endforeach;
            ?>

            <tr>
                <td colspan="3" align="right">Total Amount : <?= number_format($total) ?></td>
                <td colspan="3" align="right"> Balance : <?= number_format($total) ?></td>
            </tr>

        </table>
    </div>
</div>

<div class="modal fade" id="MobileMoneyPayments" tabindex="-1" role="dialog" aria-labelledby="MobileMoneyPayments"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="content_ids_store text-hide" payment_id="" amount="" balance="" payment_channel=""
                      previous_view="" next_view=""></span>
                <h5 class="modal-title" id="exampleModalLabel">Pay this Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <section class="payment_details_display">

                </section>

                <div class="mtn-momo-payment-procedure">
                    <section class="form-control">Mode of Payment
                        <select class="mode-of-payment">
                            <option value="rave" selected="selected">Rave (MoMo + Visa + Barter)</option>
                            <option value="MTN_MOMO">MTN Mobile Money</option>
                            <option value="CENTE_AGENT">CENTE AGENT</option>
                        </select>
                    </section>

                    <br>
                    <section class="form-control">Phone Number <input type="number" class="payment_amount_phone"
                                                                      placeholder="Enter telephone number"/></section>

                    <br>
                    <section class="form-control">Amount <input type="number" class="payment_amount_input"
                                                                placeholder="Enter Amount"/>
                    </section>

                </div>

                <p class="mtn-momo-payment-response">

                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <!--                <button type="submit" class="btn btn-primary btn-sm get_payment_details"><i class="fa fa-money"></i>&nbsp;&nbsp;VERIFY PAYMENT DETAILS</button>-->
                <button type="submit" class="btn btn-success btn-sm initiate_live_payment"><i class="fa fa-money"></i>
                    &nbsp;&nbsp;PROCEED To PAY
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var id_Store = $(".content_ids_store");
    $(".trigger_payment").hide();

    //this will automatically get the payment details and display them
    $(".trigger_payment_button").click(function (e) {
        $(".initiate_live_payment").show();
        var item_id = $(this).attr("data-id");
        if (typeof item_id !== 'undefined') {
            id_Store.attr("item_id", item_id);
            $("#MobileMoneyPayments").modal("show");

            var responseDiv = $(".mtn-momo-payment-response");

            $.ajax(
                {
                    type: "POST",
                    url: "<?=base_url()?>PaymentsManagement/getPaymentDetails/" + item_id,
                    data: null,
                    beforeSend: function () {
                        responseDiv.css("margin-top", "10px")
                        responseDiv.addClass("alert alert-info")
                        responseDiv.text("Loading, please wait")
                        console.log("Going to <?=base_url()?>PaymentsManagement/getPaymentDetails/" + item_id)
                    },
                    success: function (response) {
                        responseDiv.hide();
                        var data = JSON.parse(response)

                        if (parseInt(data.resultCode) == 0) {
                            var paymentRecord = data.data;
                            var paymentDetails = $(".payment_details_display")
                            $(".payment_amount_input").val(paymentRecord.balance)

                            paymentDetails.html("")

                            paymentDetails.append("<p>Reason : " + paymentRecord.reason + "<br>" +
                                "Amount : " + paymentRecord.amount + "<br>" +
                                "Balance : " + paymentRecord.balance.toLocaleString() + "<br>" +
                                "Reference Number : " + paymentRecord.reference_number + "</p>")

                        } else {
                            responseDiv.css("margin-top", "10px")
                            responseDiv.addClass("alert alert-danger")
                            responseDiv.text(data.message)
                            responseDiv.show()
                        }
                    },
                    error: function (error) {
                        var data = JSON.stringify(error);
                        console.log(data)
                    }
                }
            )
        }
    });

    //verify mobile number just, being eliminated
    $(".get_payment_details").click(function () {
        var dataObject = {
            payment_id: id_Store.attr("item_id"),
            mode_of_payment: $(".mode-of-payment").val(),
            phone: $(".payment_amount_phone").val(),
            amount: $(".payment_amount_input").val(),

        }

        var responseDiv = $(".mtn-momo-payment-response");
        var payment_id = id_Store.attr("item_id")

        $.ajax(
            {
                type: "POST",
                url: "<?=base_url()?>PaymentsManagement/getPaymentDetails/" + payment_id,
                data: dataObject,
                beforeSend: function () {
                    responseDiv.css("margin-top", "10px")
                    responseDiv.addClass("alert alert-info")
                    responseDiv.text("Loading, please wait")
                },
                success: function (response) {
                    responseDiv.hide();
                    var data = JSON.parse(response)

                    if (parseInt(data.resultCode) == 0) {
                        $(".proceed_with_payment").hide();
                        var paymentRecord = data.data;
                        var paymentDetails = $(".payment_details_display")

                        responseDiv.css("margin-top", "10px")
                        responseDiv.addClass("alert alert-success")
                        responseDiv.text(data.message)
                        responseDiv.show();
                        $(".trigger_payment").show();

                    } else {
                        responseDiv.css("margin-top", "10px")
                        responseDiv.addClass("alert alert-danger")
                        responseDiv.text(data.message)
                        responseDiv.show();
                    }
                }
            });

    });

    //tigger payment
    $(".initiate_live_payment").click(function () {
        var dataObject = {
            payment_id: id_Store.attr("item_id"),
            mode_of_payment: $(".mode-of-payment").val(),
            phone: $(".payment_amount_phone").val(),
            amount: $(".payment_amount_input").val(),
        }

        var responseDiv = $(".mtn-momo-payment-response");
        var payment_id = id_Store.attr("item_id")

        $.ajax(
            {
                type: "POST",
                url: "<?=base_url()?>PaymentsManagement/triggerPayment/" + payment_id,
                data: dataObject,
                beforeSend: function () {
                    responseDiv.css("margin-top", "10px")
                    responseDiv.addClass("alert alert-info")
                    responseDiv.text("Loading, please wait")
                },
                success: function (response) {
                    console.log(response);
                    responseDiv.hide();

                    var data = JSON.parse(response)

                    if (parseInt(data.resultCode) == 0) {
                        $(".proceed_with_payment").hide();
                        var paymentRecord = data.data;
                        var paymentDetails = $(".payment_details_display")

                        responseDiv.css("margin-top", "10px")
                        responseDiv.addClass("alert alert-success")
                        responseDiv.text(data.message)
                        responseDiv.show();
                        $(".trigger_payment").hide();

                        //this should open only if payment is Rave payment
                        window.open(paymentRecord, '_blank', "toolbar=no,scrollbars=no,resizable=no,top=200,left=300,width=400,height=600");

                        //hide the modal
                        $("#MobileMoneyPayments").modal("hide");
                    } else {
                        responseDiv.css("margin-top", "10px")
                        responseDiv.addClass("alert alert-danger")
                        responseDiv.text(data.message)
                        responseDiv.show();

                        $(".proceed_with_payment").hide();
                    }
                }
            });

    });

</script>
