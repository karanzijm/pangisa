<!--display title-->
<span style='font-size: 20px; font-weight: 600'><?=($page_title?$page_title:null)?></span> &nbsp;&nbsp;&nbsp;<br><br>

<!--display errors -->
<?= ($error?"<p class='display_error'>".json_encode($error)."</p>":null) ?>

<!--main content-->
<?=$information?>

<!--pagination-->
<div>
    <?=$pagination ?>
</div>