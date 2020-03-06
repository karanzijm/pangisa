<style>
    .kvendor_add_items table tr td .form-control {
        border: 0px !important;
        border-bottom: 1px solid #ccc !important;
        border-radius: 0px;
        outline: none !important;
    }
</style>


<div class="container-fluid display_order_details">
    <div class="row">
        <div class="col-sm-12">
            <h1>Edit Item (<?= $data->name ?>)</h1><br>
            <div class="table-responsive">
                <form action='<?= base_url() ?>AppClient/edit_item/<?= $data->id ?>/save' method='post'
                      enctype='multipart/form-data' class="vendor_add_items">
                    <table class="table table-borderless result_table_light myorders_table" width="100%">
                        <tr>
                            <td>Item Name</td>
                            <td><input class="form-control" value="<?= $data->name ?>" name="name"/></td>
                            <td>Identification Number</td>
                            <td><input type="text" class="form-control" value="<?= $data->identification_number ?>"
                                       name="identification_number"/></td>
                        </tr>

                        <tr>
                            <td>Item Category</td>
                            <td>
                                <?php
                                $categories = $this->db->get("categories")->result_array();
                                $chosen_category = AppClient::get_instance()->SearchAssociativeArrayByIndex("categories", $data->category);
                                ?>
                                <select class="categories" name="category">
                                    <option selected="selected"
                                            value="<?= $chosen_category['id'] ?>"><?= $chosen_category['name'] ?></option>
                                    <?php
                                    foreach ($categories as $cat) {
                                        echo "<option class='list-group-item' value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>Item Sub Category</td>
                            <td>
                                <section class="sub_categories" name="sub_category">

                                </section>
                            </td>
                        </tr>

                        <tr>
                            <td>Color</td>
                            <td><input class="form-control" name="color" value="<?= $data->color ?>"/></td>
                            <td>Size</td>
                            <td><input type="text" class="form-control" name="size" value="<?= $data->size ?>"/></td>
                        </tr>

                        <tr>
                            <td>Price</td>
                            <td><input type="text" value="<?= $data->price ?>" class="form-control" name="price"/><br>
                                <small>Remember, On top of this price, Pangisa shall add a a 10% margin</small>
                            </td>
                            <td>Pick Up Location</td>
                            <td><input type="text" value="<?= $data->pick_up_location ?>" class="form-control"
                                       name="pick_up_location"/></td>
                        </tr>

                        <tr>
                            <td>Description</td>
                            <td><textarea class="form-control"
                                          name="brief_description"><?= $data->brief_description ?></textarea><br>
                                <small>Good Description, Easy sales</small>
                            </td>
                            <td>Features</td>
                            <td><textarea class="form-control" name="features"><?= $data->features ?></textarea></td>
                        </tr>

                        <tr>
                            <td>Year of make</td>
                            <td><input type="number" value="<?= $data->year_of_make ?>" name="year_of_make"
                                       class="form-control" required/></td>
                            <td>Price Points</td>
                            <td>
                                <?php
                                $price_points = $this->db->get("price_points")->result_array();
                                $chosen_price_point = AppClient::get_instance()->SearchAssociativeArrayByIndex("price_points", $data->price_point);

                                if (count($chosen_price_point) > 0):
                                    ?>
                                    <select name="price_point" required>
                                        <option selected="selected"
                                                value="<?= $chosen_price_point['id'] ?>"><?= $chosen_price_point['name'] ?></option>
                                        <?php
                                        foreach ($categories as $cat) {
                                            echo "<option class='list-group-item' value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                <?php
                                endif;
                                ?>
                            </td>
                        </tr>

                        <tr>
                            <td>Front View</td>
                            <td>
                                <input type="file" name="front_view" class="form-control-file"
                                       accept='image/png, image/jpeg,image/jpg'
                                       onchange="previewFile(event,'front_view')"/>
                                <section style="width: 120px">
                                    <img src="<?= base_url() . "items/" . $data->front_view ?>" id="front_view"
                                         width="200px"/>
                                </section>
                            </td>
                            <td>Side View</td>
                            <td>
                                <input type="file" name="side_view" class="form-control-file"
                                       accept='image/png, image/jpeg,image/jpg'
                                       onchange="previewFile(event,'side_view')"/>
                                <section style="width: 120px">
                                    <img src="<?= base_url() . "items/" . $data->side_view ?>" id="side_view"
                                         width="200px"/>
                                </section>
                            </td>
                        </tr>

                        <tr>
                            <td>Rear View</td>
                            <td>
                                <input type="file" name="rear_view" class="form-control-file"
                                       accept='image/png, image/jpeg,image/jpg'
                                       onchange="previewFile(event,'rear_view')"/>
                                <section style="width: 120px">
                                    <img src="<?= base_url() . "items/" . $data->rear_view ?>" id="rear_view"
                                         width="200px"/>
                                </section>
                            </td>
                        </tr>

                        <tr>
                            <td><input type="submit" class="btn  btn-success" value="Update Item"/></td>
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
                url: "<?=base_url()?>AppClient/AjaxRetrieveSubCategories/",
                data: {"category": $(this).val()},
                method: "POST",
                success: function (response) {
                    $(".sub_categories").html(response)
                },
                error: function (error) {
                    console.log(error)
                }
            }
        )
    })
</script>

<script>
    var previewFile = function (event, display) {
        var output = document.getElementById(display);
        console.log(JSON.stringify(event.target))
        output.src = URL.createObjectURL(event.target.files[0]);
    };
</script>


