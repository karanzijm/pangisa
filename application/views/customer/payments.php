<div class="container-fluid">
    <div class="row">
        <?php echo form_open_multipart(base_url()."AppAdmin/payments/"); ?>
        <SPAN><b>Payments</b></SPAN> &nbsp;&nbsp;&nbsp;
        <span class="filter_search_item">
                <input type="text"  name="filter_search_item" placeholder="Order number"/>
                start date <input type="date"  name="startdate" />
                end date <input type="date"  name="enddate" />
            </span> &nbsp;
        <button type="submit" class="btn btn-sm btn-success" ><i class="fa fa-search" aria-hidden="true"></i> </button>
        </form>
    </div>

    <div class="row">
        <table class='table lib_table' style='width: 100% !important;'>
            <tr>
                <td>#</td>
                <td>Order Number</td>
                <td>Item</td>
                <td>Identification</td>
                <td>Amount</td>
                <td>Total Paid</td>
                <td>Balance</td>
                <td>Last Transaction Date</td>
                <td>Action</td>
            </tr>

            <?php
            $i=1;
            foreach ($data as $payment):
                $payment_link = $payment['balance'] > 0 ? '<button class="btn btn-sm btn-secondary trigger_payment_button"  data-id="' . $payment['id'] . '">PAY</button>' : null;

                echo "<tr>";
                echo "<td>".$i."</td>";
                echo "<td>".$payment['order_number']."</td>";
                echo "<td>".$payment['item']."</td>";
                echo "<td>".$payment['identification_number']."</td>";
                echo "<td>".number_format($payment['amount'])."</td>";
                echo "<td>".number_format($payment['total_paid'])."</td>";
                echo "<td>".number_format($payment['balance'])."</td>";
                echo "<td>".$payment['last_transaction_date']."</td>";
                echo "<td>" . $payment_link . "</td>";
                $i++;
            endforeach;

            ?>
        </table>
    </div>
</div>

<div class="container-fluid"><?= $pagination ?></div>

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
                            <option selected='selected' value="MTN_MOMO">MTN Mobile Money</option>
                            <option value="rave">Airtel Money,Visa</option>
                            <option value="CENTE_AGENT">CENTE AGENT</option>
                        </select>
                    </section>

                    <br>
                    <section class="form-control">Phone Number <input type="number" class="payment_amount_phone"
                                                                      placeholder="Enter telephone number"/></section>

                    <br>
                    <section class="form-control">Amount <input type="number" class="payment_amount_input" readonly
                                                                placeholder="Enter Amount"/>
                    </section>

                </div>

                <p class="mtn-momo-payment-response">

                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cancel</button>
                <!--                <button type="submit" class="btn btn-primary btn-sm proceed_with_payment"><i class="fa fa-money"></i>&nbsp;&nbsp;&nbsp;VERIFY</button>-->
                <button type="submit" class="btn btn-success btn-sm initiate_live_payment"><i class="fa fa-money"></i>
                    &nbsp;&nbsp;&nbsp;PROCEED TO PAY
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

                            $('.payment_amount_input').val(paymentRecord.balance);

                        } else {
                            $('.initiate_live_payment').hide();
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
    //$(".get_payment_details").click(function () {
    //    var dataObject = {
    //        payment_id: id_Store.attr("item_id"),
    //        mode_of_payment: $(".mode-of-payment").val(),
    //        phone: $(".payment_amount_phone").val(),
    //        amount: $(".payment_amount_input").val(),
    //
    //    }
    //
    //    var responseDiv = $(".mtn-momo-payment-response");
    //    var payment_id = id_Store.attr("item_id")
    //
    //    $.ajax(
    //        {
    //            type: "POST",
    //            url: "<?//=base_url()?>//PaymentsManagement/getPaymentDetails/" + payment_id,
    //            data: dataObject,
    //            beforeSend: function () {
    //                responseDiv.css("margin-top", "10px")
    //                responseDiv.addClass("alert alert-info")
    //                responseDiv.text("Loading, please wait")
    //            },
    //            success: function (response) {
    //                responseDiv.hide();
    //                var data = JSON.parse(response)
    //
    //                if (parseInt(data.resultCode) == 0) {
    //                    $(".proceed_with_payment").hide();
    //                    var paymentRecord = data.data;
    //                    var paymentDetails = $(".payment_details_display")
    //
    //                    responseDiv.css("margin-top", "10px")
    //                    responseDiv.addClass("alert alert-success")
    //                    responseDiv.text(data.message)
    //                    responseDiv.show();
    //                    $(".trigger_payment").show();
    //
    //                } else {
    //                    responseDiv.css("margin-top", "10px")
    //                    responseDiv.addClass("alert alert-danger")
    //                    responseDiv.text(data.message)
    //                    responseDiv.show();
    //                }
    //            }
    //        });
    //
    //});

    //mode of payment
    var mode_of_payment = $(".mode-of-payment").val();
    //tigger payment
    $(".initiate_live_payment").click(function () {
        var dataObject = {
            payment_id: id_Store.attr("item_id"),
            mode_of_payment: mode_of_payment,
            phone: $(".payment_amount_phone").val(),
            amount: $(".payment_amount_input").val(),
        }

        var responseDiv = $(".mtn-momo-payment-response");
        var payment_id = id_Store.attr("item_id")
        responseDiv.text("loading ....... ")

        $.ajax(
            {
                type: "POST",
                url: "<?=base_url()?>PaymentsManagement/triggerPayment/" + payment_id,
                data: dataObject,
                beforeSend: function () {
                    responseDiv.css("margin-top", "10px")
                    responseDiv.addClass("alert alert-info")
                    responseDiv.text("Loading, please wait ...... ")
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
                        $(".trigger_payment").hide();

                        //this should open only if payment is Rave payment
                        if (mode_of_payment == 'rave') {
                            window.open(paymentRecord, '_blank', "toolbar=no,scrollbars=no,resizable=no,top=200,left=300,width=400,height=600");
                        }

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
