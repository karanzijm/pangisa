<button style="border: none; outline: none; background: none;"><b>Categories</b></button>
<button class="btn btn-sm btn-primary" data-toggle="collapse" href="#addCat" role="button" aria-expanded="false" aria-controls="addCat"><b>Add new Category</b></button>
<button class="btn btn-sm btn-primary" data-toggle="collapse" href="#addSubCat" role="button" aria-expanded="false" aria-controls="addCat"><b>Add Sub Category</b></button>

<form action="<?=$data['categories_submit_link']?>" method="post" enctype="multipart/form-data" id="addCat" class="collapse">
    <br>

    <input type="text"  required name="name" class="form-control" placeholder="Provide A Category name"/>
    <input type="file"  required name="icon" class="form-control" accept="image/jpeg,image/png,image/gif"/>
    <button type="submit"   class="btn btn-sm btn-success" style="margin-top: 10px;">Save</button>
</form>

<form action="<?=$data['sub_categories_submit_link']?>" method="post" enctype="multipart/form-data" id="addSubCat" class="collapse">
    <br>

    <select name="category" required>
        <option value="">choose Category</option>
    <?php
    foreach ($data['categories'] as $cat){
        echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
    }
    ?>
    </select>

    <input type="text"  required name="name" class="form-control" placeholder="Sub Category name"/>
    <button type="submit"   class="btn btn-sm btn-success" style="margin-top: 10px;">Save</button>
</form>

<style>
    /* Box-sizing reset: //w3bits.com/?p=3225 */
    html {
        box-sizing: border-box;
    }

    *,
    *:before,
    *:after {
        box-sizing: inherit;
    }

    /* The category_list Container */
    .category_list {
        column-count: 4;
        -moz-column-gap: 1.3em;
        -webkit-column-gap: 1.3em;
        -moz-column-gap: 1.3em;
        column-gap: 1.0em;
        font-size:1.0em;
        width: 100%;
    }

    /* The category_list Brick */
    .column_star {
        display: inline-block;
        padding: 15px;
        border:1px solid #ccc;
        margin: 0 0 1.5em;
        width: 98%;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border-radius: 0px;
        -moz-border-radius: 0px;
        -webkit-border-radius: 0px;
    }

    /* category_list on large screens */
    @media only screen and (min-width: 1024px) {
        .category_list {
            column-count: 4;
        }
    }

    /* category_list on medium-sized screens */
    @media only screen and (max-width: 1023px) and (min-width: 768px) {
        .category_list {
            column-count: 3;
        }
    }

    /* category_list on small screens */
    @media only screen and (max-width: 767px) and (min-width: 540px) {
        .category_list {
            column-count: 2;
        }
    }
</style>

    <br><br>
    <div class="category_list ">
        <?php
            foreach ($data['categories'] as $cat){
                $sub_categories=$this->db->where(['category'=>$cat['id']])->get("sub_categories")->result_array();

                echo"<div class='column_star'>";
                    echo "<b>".$cat['name']." &nbsp; &nbsp;<a href='".base_url()."AppAdmin/categories/delete/".$cat["id"]."'><i class='fa fa-trash'></i> &nbsp;&nbsp;&nbsp;<a href='".base_url()."AppAdmin/categories/edit_category/".$cat["id"]."'><i class='fa fa-edit'></i> </a></b>";

                    foreach ($sub_categories as $sub_cat){
                        echo "<li>".$sub_cat['name']." &nbsp;&nbsp;&nbsp;<a href='".base_url()."AppAdmin/categories/delete_sub_category/".$sub_cat["id"]."'><i class='fa fa-trash'></i> </a></li>";
                    }

                echo"</div>";
            }
        ?>
    </div>
