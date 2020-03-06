<div class="container-fluid display_order_details">
    <h5><?= $data->item_number?>  &nbsp;&nbsp; - <?= $data->name?> (<?= $data->identification_number?>) </h5>
    <ul>
        <?php if($data->status==0):?>
        <li><a href="<?=base_url()?>AppAdmin/items/approve/<?=$data->id?>"><button class="btn btn-sm btn-outline-success">Approve Item</button></a> </li>
        <?php endif; if($data->status==1 && $data->verified==0):?>
            <li><a href="#"><button class="btn btn-sm btn-outline-success"  data-toggle="modal"  data-target="#reviewOrder">Verify Item</button></a> </li>
        <?php endif ?>
    </ul>
</div>
<br>

<div class="container-fluid display_order_details">
    <h5>Item Details</h5>

    <div class="row">
        <table class="table result_table_light table-hover myorders_table" width="100%"">
        <tr><td>Item Number</td><td><?= $data->item_number?></td> <td>Status</td><td><?= ($data->status==0?"Pending Review":($data->status==1?"Approved":"Rejected"))?></td></tr>
        <tr><td>Item Name</td><td><?= $data->name?></td> <td>Pick Up Location</td><td><?= $data->pick_up_location?></td></tr>
        <tr><td>Price</td><td><?= $data->price?></td> <td>Year Of Make</td><td><?= $data->year_of_make?></td></tr>
        <tr><td>Brief Description</td><td colspan="3"><?= $data->brief_description?></td> </tr>
        <tr><td>Features</td><td colspan="3"><?= $data->features?></td> </tr>
        <tr><td>Admin Approval</td><td><?= $data->approved_by_admin?></td> <td>Availability</td><td><?= $data->availability?></td></tr>
        <tr><td>Orders</td><td><?= $data->orders?></td> <td>Views</td><td><?= $data->views?></td></tr>
        <tr><td>Category</td><td><?= $data->category?></td> <td>Sub category</td><td><?= $data->sub_category?></td></tr>
        <tr><td>Verified</td><td><?= $data->approved_by_admin?></td> <td>Verification Date</td><td><?= $data->verification_date?></td></tr>
        <tr><td>Verification Score</td><td><?= ($data->verification_score/10)*100?>%</td> <td>Verified bY</td><td><?= $data->verified_by?></td></tr>
        <tr><td>Date Added</td><td><?= $data->date_added?></td> <td>Added by</td><td><?= $data->owner?></td></tr>
        <tr><td>Color</td><td><?= $data->color?></td> <td>Size</td><td><?= $data->size?></td></tr>
        </table>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->side_view?>"  width="100%"/></div>
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->front_view?>" width="100%"/></div>
        <div class="col-sm-4"><img src="<?=base_url()?>/items/<?=$data->rear_view?>"  width="100%"/></div>
    </div>

</div>


<div class="modal fade" id="reviewOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="<?= base_url()?>AppAdmin/items/verify/<?=$data->id ?>" id="acceptOrder">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">VERIFY THIS ITEM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="alternative_phone"></label>
                        <input type="range" name="score" required class="form-control verify_item_score" min="0" max="10" value="2"  style="outline: none;"/>
                        <small id="emailHelp" class="form-text text-muted">Give a score from the Verification.</small>
                        <p class="alert alert-info verify_item_score_display"></p>
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