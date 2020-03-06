<div class="container-fluid side_padding"
     style="background: #jf5f5f5; text-align: center; padding-top:30px; padding-bottom: 30px;">
    <div class="row">
        <form method="post" action="<?= base_url() ?>Index/searchItems/" class="landing_page_strip_search">

            <ul>
                <li><input type="text" class="inputSearchItem" name="name" placeholder="Item Name"></li>
                <li><input type="number" name="min_price" placeholder="Lowest Price"></li>
                <li><input type="number" name="max_price" placeholder="highest Price"></li>
                <li><input type="submit" value="SEARCH"/></li>
            </ul>
        </form>

        <span class="loading_view"></span>
    </div>

    <div class="search_response"></div>

    <br><br><br><br>
    <h3><?= "Your search returned " . count($items) . " Items" ?></h3><br>

</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">

        </div>
        <div class="col-sm-12">
            <div class="container-fluids side_padding">

                <div class="row display_items_col_12" id="view_items">
                    <div class="col-sm-12">
<!--                        <div class="row">-->
<!--                            --><?php
//                            foreach ($items as $itemsearch) {
//                                echo '
//                                       <div class="col-sm-3 landing_page_display_categories">
//                                            <a href="' . base_url() . "Index/items/view/" . $itemsearch['id'] . '">
//                                              <div style="background: #fk9f9f9;">
//                                               <img  src="' . base_url() . 'items/' . $itemsearch['front_view'] . '" alt="' . $itemsearch['name'] . '" width="100%">
//                                               <div >
//                                                    <small>' . $itemsearch['category'] . '</small>
//                                                    <b><h6>' . ucfirst($itemsearch['name'] . ' - ' . $itemsearch['color']) . '</h6></b>
//                                                    <p>' . ucfirst($itemsearch['brief_description']) . '</p>
//                                                    <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; "> <i class="fa fab-money" style="color: #dcn134c;"></i> SH ' . number_format($itemsearch['price']) . ' ' . strtoupper($itemsearch['price_point']) . '</button>
//                                                    <br><bR>
//                                               </div>
//                                            </div>
//                                            </a>
//                                   </div>
//                               ';
//                            }
//                            ?>
<!---->
<!--                        </div>-->

                        <div class="yrow masonry">
                            <?php
                            foreach ($items as $item) {
                                echo '
                           <div class="vcol-sm-3 landing_page_display_categories masonry-item">
                                <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                                  <div style="background: #f9jf9f9;">
                                   <img src="' . base_url() . 'resources/images/img_placeholder.png" class="lazy_loading_img" data-src="' . base_url() . 'items/' . $item['front_view'] . '?tr=w-400,h-300,bl-30,q-10" alt="' . $item['name'] . '" width="100%">
                                  
                                   <div class="item_content">
                                        <i class=" fa fa-calendar"></i> <small class="btn-outline-success" style="color:#ff0000">' . $item['date'] . '</small> &nbsp;&nbsp;
                                        <i class="fa fa-info-circle"></i> <small class="btn-outline-success" style="color:' . ($item['availability'] == 1 ? 'green' : '#ff0000') . '">' . ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') . '</small>
                                        <b><h6>' . ($item['verified'] == 1 ? "<i class='fa fa-certificate' style='font-size: 15pt; color: #255;'></i>" : null) . " " . ucfirst($item['name'] . ' - ' . $item['color']) . '</h6></b>
                                        <p>' . ucfirst(htmlentities($item['brief_description'])) . '</p>
                                        <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; "> <i class="fa fab-money" style="color: #dcn134c;"></i> SH ' . number_format($item['price']) . ' ' . strtoupper($item['price_point']) . '</button>
                                        
                                   </div>
                                </div>
                                </a>
                       </div>
                   ';
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(".inputSearchItem").keyup(function () {
        var item_name = $(this).val();
        $(".loading_view").html("Loading, please wait");

        $.ajax(
            {
                type: 'POST',
                url: '<?=base_url()?>/Index/AjaxSearchItemByName/',
                data: {"item_name": item_name, "min_price": null, "max_price": null},
                success: function (response) {
                    $(".search_response").html(response);
                    $(".loading_view").html("");
                }
            }
        );

    });
</script>