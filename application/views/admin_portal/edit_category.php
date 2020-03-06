

<form action="<?=$data['edit_cat_link']?>" method="post" enctype="multipart/form-data" >
    <br>
    <input type="text"   name="name" class="form-control" value="<?=$data['category']->name?>" placeholder="Provide A Category name"/>
    <input type="file"   name="icon" class="form-control"  accept="image/jpeg,image/png,image/gif"/>
    <img src="<?=base_url()."category_icons/".$data['category']->icon?>" /><br>
    <button type="submit"   class="btn btn-sm btn-success" style="margin-top: 10px;">Save</button>
</form>