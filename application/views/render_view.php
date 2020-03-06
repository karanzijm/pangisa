<div style="background:#fff ; padding:0px; margin: 0px 0px 5px 0px;">
<table width="auto">
    <tr>
        <td valign='middle'>
            <span style='font-size: 20px; font-weight: 600'><?=($page_title?$page_title:null)?></span> &nbsp;&nbsp;&nbsp;
            <?=($form_header_title?"<a href='".$form_header_link."'> <button class='btn btn-sm btn-primary' >".$form_header_title."</button></a>":null)?>
            &nbsp;&nbsp;&nbsp;
        </td>

        <td style="padding-top:10px;" valign="middle"><?=$filter?></td>
    </tr>
</table>
</div>


<!--main content-->
<?=$information?>

<!--pagination-->
<div>
    <?=$pagination ?>
</div>


