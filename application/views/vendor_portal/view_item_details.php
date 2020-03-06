<div class="container-fluid display_order_details">
    <h5><?= $data->item_number?>  &nbsp;&nbsp; - <?= $data->name?> (<?= $data->identification_number?>) </h5>
    <ul>
        <li><a href="<?=base_url()?>AppClient/edit_item/<?=$data->id?>"><button class="btn btn-sm btn-outline-success">EDIT</button></a> </li>
        <li><a href="<?=base_url()?>AppClient/items/delete/<?=$data->id?>"><button class="btn btn-sm btn-danger">DELETE</button></a> </li>
        <li><a href="#"><button class="btn btn-sm btn-secondary" data-toggle="modal"  data-target="#reviewOrder">ADD EXPENSE</button></a> </li>
        <li><a href="#"><button class="btn btn-sm btn-success" data-toggle="modal"  data-target="#promoteItem">PROMOTE ITEM</button></a> </li>

    </ul>
</div>


<br>
<div class="container-fluid display_order_details">
    <h5>Item Performance Over Time - <?=date('Y')?></h5><br>
    <div class="row">

        <div class="col-sm-6 app_client_graph_div" id="graph"></div>
        <div class="col-sm-5 app_client_graph_div" id="bar_graph"></div>

        <?php
        $info=[];

        for($i=1; $i<=12; $i++){
            $graph_orders_approved=$this->db->query("select count(*)  as total from orders where item='".$data->id."' and approved=1  and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $graph_orders=$this->db->query("select count(*)  as total from orders where item='".$data->id."' and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $expense_for_graph=$this->db->query("select count(*)  as total from item_expenses where  item_id='".$data->id."' and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            array_push($info,
                [
                    'month'=>date('Y').'-'.$i,
                    'orders'=>$graph_orders,
                    'expenses'=>$expense_for_graph,
                    'approved_orders'=>$graph_orders_approved,
                ]
            );

        }


        ?>
        <script>
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            Morris.Line({
                element: 'graph',
                data: <?= json_encode( $info); ?>,
                xkey: 'month',
                ykeys: ['orders','expenses', 'approved_orders'],
                xLabels:['MONTH'],
                xLabelFormat: function (x) { return months[x.getMonth()]; },
                labels:['Orders',"Expenses","Approved Orders"],
                pointSize: 2,
                hideHover: 'false',
                resize: true,
                fillOpacity: 0.4,
                pointFillColors:['#fff'],
                pointStrokeColors: ['#dc134c'],
                lineColors:['#1489ab','#dc134c', '#FFFE46',"#1d1d1d"],
                grid:'false'

            });
        </script>

        <?php
        $info=[];

        for($i=1; $i<=12; $i++){
            $graph_orders_approved=$this->db->query("select sum(order_amount)  as total from orders where item='".$data->id."' and approved=1  and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $graph_orders=$this->db->query("select sum(order_amount)  as total from orders where item='".$data->id."' and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $expense_for_graph=$this->db->query("select sum(amount)  as total from item_expenses where  item_id='".$data->id."' and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            array_push($info,
                [
                    'month'=>date('Y').'-'.$i,
                    'orders'=>$graph_orders,
                    'expenses'=>$expense_for_graph,
                    'approved_orders'=>$graph_orders_approved,
                ]
            );

        }

        ?>

        <script>
            Morris.Bar({
                element: 'bar_graph',
                data: <?= json_encode( $info); ?>,
                xkey: 'month',
                ykeys: ['orders','expenses', 'approved_orders'],
                xLabels:['MONTH'],
                labels:['Orders',"Expenses","Approved Orders"],
                stacked:true,
                pointSize: 2,
                hideHover: 'false',
                resize: true,
                fillOpacity: 0.4,
                pointFillColors:['#fff'],
                pointStrokeColors: ['#dc134c'],
                lineColors:['#1489ab','#dc134c', '#FF0000',"#1d1d1d"],
                grid:'false'

            });
        </script>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <h5>Item Details</h5>

    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%">
        <tr><td>Item Number</td><td><?= $data->item_number?></td> <td>Status</td><td><?= ($data->status==0?"Pending Review":($data->status==1?"Approved":"Rejected"))?></td></tr>
        <tr><td>Item Name</td><td><?= $data->name?></td> <td>Pick Up Location</td><td><?= $data->pick_up_location?></td></tr>
        <tr><td>Price</td><td><?= number_format($data->price)?></td> <td>Year Of Make</td><td><?= $data->year_of_make?></td></tr>
        <tr><td>Brief Description</td><td colspan="3"><?= $data->brief_description?></td> </tr>
        <tr><td>Features</td><td colspan="3"><?= $data->features?></td> </tr>
        <tr><td>Admin Approval</td><td><?= $data->approved_by_admin==0?null:"Approved"?></td> <td>Availability</td><td><?= $data->availability==1?"Available":"Taken"?></td></tr>
        <tr><td>Orders</td><td><?= $data->orders?></td> <td>Views</td><td><?= $data->views?></td></tr>
        <tr><td>Category</td><td><?= $data->category?></td> <td>Sub category</td><td><?= $data->sub_category?></td></tr>
        <tr><td>Verified</td><td><?= $data->approved_by_admin==1?"Approved":"Pending"?></td> <td>Verification Date</td><td><?= $data->verification_date?></td></tr>
        <tr><td>Verification Score</td><td><?= ($data->verification_score/10)*100?>%</td> <td>Verified bY</td><td><?= $data->verified_by?></td></tr>
        <tr><td>Date Added</td><td><?= $data->date_added?></td> <td>Added by</td><td><?= $data->owner?></td></tr>
        <tr><td>Color</td><td><?= $data->color?></td> <td>Size</td><td><?= $data->size?></td></tr>
        </table>
    </div>
</div>

<br>
<div class="container-fluid display_order_details">
    <div class="row">
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->side_view?>"  width="100%" style="max-height: 200px;"/></div>
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->front_view?>" width="100%" style="max-height: 200px;"/></div>
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->rear_view?>"  width="100%" style="max-height: 200px;"/></div>
    </div>

</div>


<br>
<div class="container-fluid display_order_details">
    <h5>Expenses Incurred</h5><br>
    <div class="row">
        <?php
            $itemExpenses=$this->db->where("item_id",$data->id)->get("item_expenses")->result_array();
        ?>

        <table class="table result_table_light result_table_light_background table-hover myorders_table" width="100%">
            <tr>
                <td>Reason</td>
                <td>Amount</td>
                <td>Date</td>
            </tr>
            <?php
                $total=0;
                foreach ( $itemExpenses as $expense):
            ?>
                <tr>
                    <td><?= $expense['reason'] ?></td>
                    <td><?= number_format($expense['amount']) ?></td>
                    <td><?= explode(" ",$expense['date'],2)[0]?></td>
                </tr>

            <?php
                $total+=$expense['amount'];
                endforeach;
            ?>
            <tr>
                <td align="right">Total Expenses Incurred</td>
                <td> <?= number_format($total) ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="modal fade" id="reviewOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?= base_url()?>AppClient/items/addExpense/<?=$data->id ?>" id="acceptOrder">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><b>RECORD OPERATIONAL EXPENSE TO ITEM</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alternative_phone">Expense Reason</label>
                        <input type="text" name="reason" required class="form-control" />
                        <small id="emailHelp" class="form-text text-muted">Give a reason as to why you spending on this item.</small>
                    </div>

                    <div class="form-group">
                        <label for="alternative_phone">Expense Amount</label>
                        <input type="number" name="amount" required class="form-control" />
                        <small id="emailHelp" class="form-text text-muted">How much you spent on this item.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">close</button>
                    <button type="submit" class="btn btn-success btn-sm">SUBMIT</button>
                </div>
            </div>

        </form>
    </div>
</div>


<div class="modal fade" id="promoteItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?= base_url()?>AppClient/items/promote_item/<?=$data->id ?>" id="acceptOrder">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"><b>PROMOTE ITEM</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alternative_phone">What's your Budget?</label>
                        <input type="number" name="amount" required class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="alternative_phone">Start Date</label>
                        <input type="date" name="start_date" required class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="alternative_phone">End Date</label>
                        <input type="date" name="end_date" required class="form-control" />
                    </div>

                    <div class="form-group">
                        <label for="alternative_phone">Target Group</label>
                        <select name="target_group">
                            <option>Please choose target group</option>
                            <option value="first_timer">First Timer</option>
                            <option value="frequent_user">Frequent USers</option>
                            <option value="corporates">Corporates</option>
                            <option value="every_one">Every One</option>
                            <option value="bulk_purchases">Bulk Purchases</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">close</button>
                    <button type="submit" class="btn btn-success btn-sm">SUBMIT</button>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    $(".verify_item_score").change(function (e) {
        var score=$(this).val();
        $(".verify_item_score_display").html((score/10)*100 + "%");
    })
</script>