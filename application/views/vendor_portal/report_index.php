<style>
    .main_content{padding:0px !important;}
    .display_order_details{box-shadow: 1px 2px 1px rgba(0,0,0,0.2);}
    .col-sm-6{padding:10px;}
</style>

<div class="container-fluid report_page_root_division">
    <div class="row">

        <div class="col-sm-2 report_page_root_division_side_menu">
            <ul>
                <a href="<?=base_url()?>AppClient/Reports/General"><li><i class="fa fa-file-excel-o"></i> &nbsp; General </li></a>
                <a href="<?=base_url()?>AppClient/Reports/transactions"><li><i class="fa fa-file-excel-o"></i> &nbsp; Transactions </li></a>
                <a href="<?=base_url()?>AppClient/Reports/items"><li><i class="fa fa-file-excel-o"></i> &nbsp; Items </li></a>
                <a href="<?=base_url()?>AppClient/Reports/payments"><li><i class="fa fa-file-excel-o"></i> &nbsp; Payments </li></a>
                <a href="<?=base_url()?>AppClient/Reports/orders"><li><i class="fa fa-file-excel-o"></i> &nbsp; Orders </li></a>
<!--                <a href="--><?//=base_url()?><!--AppClient/Reports/clients"><li><i class="fa fa-file-excel-o"></i> &nbsp; Client </li></a>-->
<!--                <a href="--><?//=base_url()?><!--AppClient/Reports/Activity"><li><i class="fa fa-file-excel-o"></i> &nbsp; Activity </li></a>-->
            </ul>
        </div>

        <div class="col-sm-10" style="padding:10px;">
            <?= form_open_multipart($data['submit_link']) ?>
                <table class="table-borderless table-primary">
                    <tr style="background: #fff; color: inherit;">
                        <td><b><?= $data['reportHeading']?></b> &nbsp; &nbsp; </td>
<!--                        <td>Start Date</td>-->
                        <td><?= form_input(["type"=>"text","name"=>"start_date", "placeholder"=>"Start date", "autocomplete"=>"off", "required"=>"required","class"=>"form-control date"]) ?></td>
<!--                        <td>End Date</td>-->
                        <td><?= form_input(["type"=>"text","name"=>"stop_date", "placeholder"=>"Start date", "autocomplete"=>"off", "required"=>"required","class"=>"form-control date"]) ?></td>
                        <td><input type="submit" name="submit" value="Submit" class="btn btn-success"/> </td>
                        <td><button class="btn btn-secondary"><i class="fa fa-file-excel-o"></i> &nbsp; EXCEL </button> </td>
                        <td><button class="btn btn-secondary"><i class="fa fa-file-pdf-o"></i> &nbsp; PDF </button> </td>
                    </tr>
                </table>
            </form>

            <hr>

            <div class="container-fluid">
                <div class="row reportDisplayContent" style="height: 89vh; overflow-y: scroll">

                    <?php if($data['reportType']=="payments"):?>
                        <div class="col-sm-12">
                            <div class="row table-responsive">
                                <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                    <tr>
                                        <td>ORDER NUMBER</td>
                                        <td>Client</td>
                                        <td>Item</td>
                                        <td>Item Number</td>
                                        <td>Amount</td>
                                        <td>Paid</td>
                                        <td>Balance</td>
                                        <td>Last Transaction Date</td>
                                    </tr>

                                    <?php
                                    foreach ($data['reportInformation']['paymentsResultsGeneral'] as $information){
                                        echo"<tr>
                                                    <td>".$information['order_number']."</td>
                                                    <td>".$information['client']."</td>
                                                    <td>".$information['item_name']."</td>
                                                    <td>".$information['item_number']."</td>
                                                    <td>".number_format($information['amount'])."</td>
                                                    <td>".number_format($information['total_paid'])."</td>
                                                    <td>".number_format($information['balance'])."</td>
                                                    <td>".date("F j, Y",strtotime($information['last_transaction_date']))."</td>
                                                </tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>

                    <?php endif; if($data['reportType']=="orders"):?>
                        <div class="col-sm-12">
                            <div class="row table-responsive">
                                <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                    <tr>
                                        <td>Number</td>
                                        <td>Item</td>
                                        <td>Order Amount</td>
                                        <td>Orders</td>
                                        <td>Successful</td>
                                        <td>Pending</td>
                                        <td>User Cancelled</td>
                                        <td>Date</td>
                                    </tr>

                                    <?php
                                    foreach ($data['reportInformation']['ordersResultsGeneral'] as $information){
                                        echo"<tr>
                                                    <td>".$information['order_number']."</td>
                                                    <td>".$information['item']."</td>
                                                    <td>".number_format($information['order_amount'])."</td>
                                                    <td>".number_format($information['orders'])."</td>
                                                    <td>".number_format($information['successful'])."</td>
                                                    <td>".number_format($information['pending'])."</td>
                                                    <td>".number_format($information['user_cancelled'])."</td>
                                                    <td>".date("F j, Y",strtotime($information['date']))."</td>
                                                </tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>

                    <?php endif;if($data['reportType']=="items"):?>
                        <div class="col-sm-12">
                            <div class="row table-responsive">
                                <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                    <tr>
                                        <td>Number</td>
                                        <td>Name</td>
                                        <td>Identification</td>
                                        <td>Price</td>
                                        <td>Orders</td>
                                        <td>Successful</td>
                                        <td>Pending</td>
                                        <td>User Cancelled</td>
                                        <td>Order Amount</td>
                                        <td>Approved</td>
                                        <td>Collected</td>
                                    </tr>

                                    <?php
                                    foreach ($data['reportInformation']['itemsResultsGeneral'] as $information){
                                        echo"<tr>
                                                    <td>".$information['item_number']."</td>
                                                    <td>".$information['name']."</td>
                                                    <td>".$information['identification_number']."</td>
                                                    <td>".number_format($information['price'])."</td>
                                                    <td>".number_format($information['orders'])."</td>
                                                    <td>".number_format($information['successful'])."</td>
                                                    <td>".number_format($information['pending'])."</td>
                                                    <td>".number_format($information['user_cancelled'])."</td>
                                                    <td>".number_format($information['order_amount'])."</td>
                                                    <td>".number_format($information['approved_amount'])."</td>
                                                    <td>".number_format($information['collected_amount'])."</td>
                                                </tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    <?php endif; if($data['reportType']=="transactions"):?>
                        <div class="col-sm-12">
                            <div class="container-fluid display_order_details">
                                <h5>Transaction Report</h5>
                                <div class="row table-responsive">
                                    <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                        <tr>
                                            <td>Date</td>
                                            <td>Total Transactions</td>
                                            <td>Successful Transactions</td>
                                            <td>Failed Transactions</td>
                                        </tr>

                                        <?php
                                            foreach ($data['reportInformation']['transactionResultsGeneral'] as $information){
                                                echo"<tr>
                                                    <td>".$information['transaction_date']."</td>
                                                    <td>".$information['total']."</td>
                                                    <td>".$information['successful']."</td>
                                                    <td>".$information['pending']."</td>
                                                </tr>";
                                            }
                                        ?>
                                    </table>
                                </div>
                            </div>

                            <div class="container-fluid display_order_details">
                                <h5>Channel Transaction Report</h5>
                                <div class="row table-responsive">
                                    <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                        <tr>
                                            <td>Channel</td>
                                            <td>Total Transactions</td>
                                            <td>Successful Transactions</td>
                                            <td>Failed Transactions</td>
                                            <td>Date</td>
                                        </tr>

                                        <?php
                                            foreach ($data['reportInformation']['transactionsByChannel'] as $information){
                                                echo"<tr>
                                                    <td>".$information['channel']."</td>
                                                    <td>".$information['total']."</td>
                                                    <td>".$information['successful']."</td>
                                                    <td>".$information['pending']."</td>
                                                    <td>".$information['transaction_date']."</td>
                                                </tr>";
                                            }
                                        ?>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                    <?php if($data['reportType']=="general"):?>
                        <div class="container-fluid display_order_details">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h3>Orders Report</h3>

                                    <div class="row table-responsive">
                                        <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                            <tr>
                                                <td>Date</td>
                                                <td>Total Orders</td>
                                                <td>Total Amount</td>
                                            </tr>

                                            <?php
                                            foreach ($data['reportInformation']['orders'] as $information){
                                                echo"<tr>
                                                    <td>".date("F j, Y",strtotime($information['date']))."</td>
                                                    <td>".$information['total']."</td>
                                                    <td>".$information['amount']."</td>
                                                    
                                                </tr>";
                                            }
                                            ?>
                                        </table>
                                    </div>

                                </div>

                                <div class="col-sm-6">

                                    <h3>Transaction Report</h3>

                                    <div class="row table-responsive">
                                        <table class="table result_table_light " width="100%" style="width: 100% !important;">
                                            <tr>
                                                <td>Date</td>
                                                <td>Total Orders</td>
                                                <td>Amount</td>
                                            </tr>

                                            <?php
                                            foreach ($data['reportInformation']['transactions'] as $information){
                                                echo"<tr>
                                                    <td>".date("F j, Y",strtotime($information['date']))."</td>
                                                    <td>".$information['total']."</td>
                                                    <td>".number_format($information['amount'])."</td>
                                                    
                                                </tr>";
                                            }
                                            ?>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

