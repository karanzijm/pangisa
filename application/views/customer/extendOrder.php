<h3>Would you like to Extend Your order period?</h3> <br>

<div class="form_for_extend_order">
    <div class="form_for_extend_order_inner">
            <form action="<?=base_url()?>/Customer/ExtendItemOrderRequest/save">
                <input class="form-control search_order_number" name="orderNumber" placeholder="Please Enter Order Number" autofocus/>
                <span class="loading_view"></span><br>
                <section class="search_response">
                    <ul ></ul>
                </section>
            </form>
    </div>
</div>

<?php if($data['orderToExtend']<>null): ?>

    <hr>
    <div>
        <form action="<?=base_url()?>/Customer/ExtendItemOrderRequest/<?=$data['orderToExtend']->order_number?>/save" method="post">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-4">
                        <label>Order Number</label>
                        <input type="text" value="<?=$data['orderToExtend']->order_number?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Item Name</label>
                        <input type="text" value="<?=$data['orderToExtend']->name?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Hiring Price</label>
                        <input type="hidden" value="<?=$data['orderToExtend']->price?>" class="hidden_amount" />
                        <input type="text" value="<?=$data['orderToExtend']->price?> <?=$data['orderToExtend']->price_point?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Expected Return Date</label>
                        <input type="text" name="return_date" value="<?=$data['orderToExtend']->return_date?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Place of use</label>
                        <input type="text" value="<?=$data['orderToExtend']->place_of_use?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Order Date</label>
                        <input type="text" value="<?=$data['orderToExtend']->date?>" readonly class="form-control"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Extra Days Required</label>
                        <input type="number" value="" name="number_of_days"  class="form-control extra_days_to_be_billed"/>
                    </div>

                    <div class="col-sm-4">
                        <label>Reason for extending order</label>
                        <textarea   class="form-control" name='reason' placeholder="Please be as clear and honest as possible"></textarea>
                    </div>

                    <div class="col-sm-4">
                        <label>Extra Amount to Be Billed</label>
                        <input type="text" value=""  class="form-control amount_to_billed" readonly/>
                    </div>

                    <div class="col-sm-4">
                        <br>
                        <input type="submit" value="Request Extension"  class="btn btn-sm btn-success"/>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <hr>
<?php endif; ?>

<table class="table" style='width: 100% !important;'>
    <tr>
        <td>Order Number</td>
        <td>Initial Days</td>
        <td>Extend Days</td>
        <td>Price</td>
        <td>Item</td>
        <td>Reason</td>
        <td>Start Date</td>
        <td>End Date</td>
        <td>Status</td>
        <td>Status Reason</td>
        <td>Date</td>
    </tr>

    <?php
        foreach ($data['extendedOrders'] as $extendedOrder):
            echo "<tr>
                <td>".$extendedOrder['order_number']."</td>
                <td>".$extendedOrder['number_of_days']."</td>
                <td>".$extendedOrder['ext_number_of_days']."</td>
                <td>".number_format($extendedOrder['price'])." ".$extendedOrder['price_point']."</td>
                <td>".$extendedOrder['name']."</td>
                <td>".$extendedOrder['reason']."</td>
                <td>".$extendedOrder['start_date']."</td>
                <td>".$extendedOrder['stop_date']."</td>
                <td>".$extendedOrder['approved']."</td>
                <td>".$extendedOrder['status_reason']."</td>
                <td>".$extendedOrder['date']."</td>
            </tr>";
        endforeach;
    ?>
</table>

<script>
    var orderNumber=null
    var ResponseObject=null
    var countObjects=null
    var OrderResponseSingleOject=null

    $(".search_order_number").keyup(function () {
        orderNumber=$(this).val();
        $(".loading_view").html("Searching for Order Number");

        $.ajax(
            {
                type:'POST',
                url:'<?=base_url()?>/Customer/AjaxSearchItemOrderNumber/',
                data:{"orderNumber": orderNumber},
                success:function (response) {
                    ResponseObject=JSON.parse(response);
                    countObjects=ResponseObject.length;

                    $(".search_response ul").html(null)

                    for(var i=0; i<countObjects; i++){
                        OrderResponseSingleOject=ResponseObject[i]
                        $(".search_response ul").append("<li class='suggestedOrderNumbers'> <a class='btn-link AjaxSearchItemOrderNumberLinkClicked' href='<?=base_url()?>Customer/ExtendItemOrderRequest/" + OrderResponseSingleOject['order_number'] + "/extend/'>" + OrderResponseSingleOject['order_number'] + "</a></li>");
                    }

                    $(".loading_view").html("");
                }
            }
        );

    } );

    $(".AjaxSearchItemOrderNumberLinkClicked").click(function(e){
        e.preventDefault();
        alert("cliec")
    });

    $('.extra_days_to_be_billed').keyup(function () {
        var days=$(this).val();
        var amount=days*$('.hidden_amount').val();
        $('.amount_to_billed').val("You will be billed an extra "+amount.toLocaleString())
    });

</script>

