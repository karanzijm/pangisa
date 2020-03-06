<style>
    .kvendor_add_items table tr td .form-control{border:0px  !important; border-bottom: 1px solid #ccc !important;  border-radius: 0px; outline: none !important;}
</style>

<div class="container-fluid display_order_details">
    <div class="row">
        <div class="col-sm-12">
            <h1>Add New Item</h1><br>
            <div class="table-responsive">
                <form action='<?=base_url()?>AppClient/addItems/save' method='post' enctype='multipart/form-data' class="vendor_add_items">
                    <table class="table table-borderless result_table_light myorders_table" width="100%">
                        <tr>
                            <td>Item Name</td><td><input  class="form-control" name="name"/></td>
                            <td>Identification Number</td><td><input type="text" class="form-control" name="identification_number"/></td>
                        </tr>

                        <tr>
                            <td>Item Category</td>
                            <td>
                                <?php $categories= $this->db->get("categories")->result_array();?>
                                <select class="form-control categories" name="category" required>
                                    <option value="">Please Choose Category</option>
                                    <?php
                                        foreach ( $categories as $cat){
                                            echo "<option class='list-group-item' value='".$cat['id']."'>".$cat['name']."</option>";
                                        }
                                    ?>
                                </select>
                            </td>
                            <td>Item Sub Category</td>
                            <td>
                                <section class="sub_categories"   name="sub_category">

                                </section>
                            </td>
                        </tr>

                        <tr>
                            <td>Color</td><td><input  class="form-control" name="color"/></td>
                            <td>Size</td><td><input type="text" class="form-control" name="size"/></td>
                        </tr>

                        <tr>
                            <td>Price</td><td><input type="number"  class="form-control" name="price"/><br><small>Remember, On top of this price, Pangisa shall add a a 10% margin </small></td>
                            <td>Pick Up Location</td><td><input type="text" class="form-control" name="pick_up_location"/></td>
                        </tr>

                        <tr>
                            <td>Description</td><td><textarea  class="form-control" name="brief_description"></textarea><br><small>Good Description, Easy sales </small></td>
                            <td>Features</td><td><textarea class="form-control" name="features"></textarea></td>
                        </tr>

                        <tr>
                            <td>Year of make</td>
                            <td><input type="number" name="year_of_make" class="form-control"/></td>
                            <td>Choose Pricing Point</td>
                            <td>
                                <?php $categories = $this->db->order_by('id', 'asc')->get("price_points")->result_array(); ?>
                                <select class="form-control" name="price_point" required>
                                    <?php
                                    $i = 0;
                                    $selected = null;
                                    foreach ($categories as $cat) {
                                        if ($i == 0) {
                                            $selected = "selected='selected'";
                                        }

                                        echo "<option class='list-group-item' " . $selected . " value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                    }
                                    $i++;
                                    ?>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Front View</td><td><input type="file" name="front_view" class="form-control-file" accept='image/png, image/jpeg,image/jpg' required/></td>
                            <td>Rear View</td><td><input type="file" name="side_view" class="form-control-file" accept='image/png, image/jpeg,image/jpg' required/></td>
                        </tr>

                        <tr>
                            <td>Side View</td><td><input type="file" name="rear_view" class="form-control-file" accept='image/png, image/jpeg,image/jpg' required/></td>

                            <td>Is Item Negotiable?</td>
                            <td>
                                <select name="is_negotiable" required class="form-control">
                                    <option value="False" selected>Not Negotiable</option>
                                    <option value="True">Negotiable Price</option>
                                </select>

                            </td>
                        </tr>

                        <?php
                        if ($this->session->user['email'] == "saccount@pangisa.co.ug") {
                            ?>
                            <tr>
                                <td>Vendor Name</td>
                                <td><input type="text" name="vendor_name" class="form-control" required/></td>
                                <td>Phone Number</td>
                                <td><input type="number" name="vendor_phone_number" class="form-control" required/></td>
                            </tr>
                            <?php
                        }
                        ?>

                        <tr>
                            <td><input type="submit" class="btn  btn-success" value="Add Item"/></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(".categories").change(function () {
        $.ajax(
            {
                url:"<?=base_url()?>AppClient/AjaxRetrieveSubCategories/",
                data:{"category":$(this).val()},
                method:"POST",
                success:function (response) {
                    $(".sub_categories").html(response)
                },
                error:function (error) {
                    console.log(error)
                }
            }
            )
    })
</script>


