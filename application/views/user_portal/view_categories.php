<div class="side_padding">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
<!--                    <a href="--><?//= base_url() ?><!--">-->
                        <li class="breadcrumb-item">Home |&nbsp; </li>
<!--                    </a>-->
                    <a href="#">
                        <li class="breadcrumb-item">Items |&nbsp; </li>
                    </a>
                    <a href="#">
                        <li class="breadcrumb-item">Categories |&nbsp; </li>
                    </a>
                    <a href="<?= base_url() . "Index/category/" . $category ?>">
                        <li class="breadcrumb-item"><?= $data['category_name'] ?>  </li>
                    </a>

                    <?php if ($sub_category): ?>
                        <a href="<?= base_url() . "Index/category/" . $category . "/sbc/" . $sub_category ?>">
                            <li class="breadcrumb-item"> &nbsp;|&nbsp;<?= $data['sub_category_name'] ?></li>
                        </a>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container-fluids side_padding">
    <div class="row display_items_col_12" style="padding: 10px 0px 15px 0px !important;">
        <div class="col-sm-2 search_column">
            <b><?= $data['category_name'] ?></b>
            <hr>
            <ul>
                <?php
                $cats = $this->db
                    ->where(['ca.category' => $category])
                    //hide subcategories with no items
                    ->where('(select count(*) from items where sub_category=ca.id and status=1)>0')
                    ->select("ca.id, ca.name,  (select count(*) from items where sub_category=ca.id and status=1) as items")
                    ->from("sub_categories ca")
                    ->order_by("items desc")
                    ->limit(20)
                    ->get()
                    ->result_array();

                foreach ($cats as $cat) {
                     $link = $cat['items'] > 0 ? base_url() . "Index/category/" . $category . "/sbc/" . $cat['id'] : "#";


                    echo "
                  
                            <li><a href='" . $link . "'> " . $cat['name'] . "&nbsp;&nbsp;(" . $cat['items'] . ")</a></li>";
                }
                ?>
            </ul>

            <hr>
            <form method="post" action="<?= base_url() ?>Index/category/<?= $category ?>" autocomplete="nope"
                  class="view_categories_search_items">
                <div class="form-group">
                    <label for="name">Item Name</label>
                    <input type="text" class="form-control" name="name" aria-describedby="emailHelp" value="">
                </div>

                <div class="form-group">
                    <label for="name">Least Amount</label>
                    <input type="number" class="form-control" name="min_price" aria-describedby="emailHelp" value="">
                </div>

                <div class="form-group">
                    <label for="name">Highest Amount</label>
                    <input type="number" class="form-control" name="max_price" aria-describedby="emailHelp" value="">
                </div>

                <input class="btn btn-sm btn-success" type="submit" value="search"/>
            </form>
        </div>

        <div class="col-sm-10 display_category_results">

            <div class="category-page-masonry">
                <?php

                //if the subcategory has no items display message and return random items
                if(empty($items)){
                    echo '
                          <div class="no_results">No Results Found!</div>';


                    $random_items = $this->db
                        ->where(['i.category' => $category])
                        ->select("id, name, verified, color, category as cat, size, availability, DATE_FORMAT(date_added, '%M %D, %Y %H:%i:%s') as date, (select name from categories where id=i.category) as category, (select name from price_points where id=i.price_point) as price_point, price, front_view,brief_description")
                        ->from("items i")
                        ->order_by("id desc")
                        ->limit(10)
                        ->get()
                        ->result_array();
                    foreach ($random_items as $item){
                        echo '
                             <div class="vcol-sm-3 landing_page_display_categories categories-masonry-item">
                                <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                                  <div style="background: #f9jf9f9;">
                                   <img  src="' . base_url() . 'items/' . $item['front_view'] . '?tr=w-400,h-300,bl-30,q-10" alt="' . $item['name'] . '" width="100%">
                                  
                                   <div class="item_content">
                                        <i class=" fa fa-calendar"></i> <small class="btn-outline-success" style="color:#ff0000">' . $item['date'] . '</small> &nbsp;&nbsp;
                                        <i class="fa fa-info-circle"></i> <small class="btn-outline-success" style="color:' . ($item['availability'] == 1 ? 'green' : '#ff0000') . '">' . ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') . '</small>
                                        <b><h6>' . ($item['verified'] == 1 ? "<i class='fa fa-certificate' style='font-size: 15pt; color: #255;'></i>" : null) . " " . ucfirst($item['name'] . ' - ' . $item['color']) . '</h6></b>
                                        <p>' . ucfirst(htmlentities($item['brief_description'])) . '</p>
                                        <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; " class="pg-landing-page-lastest-items-row-money-label"> <i class="fa fab-money" style="color: #dcn134c;"></i> ' . (number_format($item['price']) > 100 ? "SH " . number_format($item['price']) . ' ' . strtoupper($item['price_point']) : "Negotiable") . '</button>
                                   </div>
                                </div>
                                </a>
                       </div>
                        ';
                    }




                }else{
                    foreach ($items as $item) {
                        echo '
                           <div class="vcol-sm-3 landing_page_display_categories categories-masonry-item">
                                <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                                  <div style="background: #f9jf9f9;">
                                   <img  src="' . base_url() . 'items/' . $item['front_view'] . '?tr=w-400,h-300,bl-30,q-10" alt="' . $item['name'] . '" width="100%">
                                  
                                   <div class="item_content">
                                        <i class=" fa fa-calendar"></i> <small class="btn-outline-success" style="color:#ff0000">' . $item['date'] . '</small> &nbsp;&nbsp;
                                        <i class="fa fa-info-circle"></i> <small class="btn-outline-success" style="color:' . ($item['availability'] == 1 ? 'green' : '#ff0000') . '">' . ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') . '</small>
                                        <b><h6>' . ($item['verified'] == 1 ? "<i class='fa fa-certificate' style='font-size: 15pt; color: #255;'></i>" : null) . " " . ucfirst($item['name'] . ' - ' . $item['color']) . '</h6></b>
                                        <p>' . ucfirst(htmlentities($item['brief_description'])) . '</p>
                                        <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; " class="pg-landing-page-lastest-items-row-money-label"> <i class="fa fab-money" style="color: #dcn134c;"></i> ' . (number_format($item['price']) > 100 ? "SH " . number_format($item['price']) . ' ' . strtoupper($item['price_point']) : "Negotiable") . '</button>
                                   </div>
                                </div>
                                </a>
                       </div>
                   ';
                    }
                }

                ?>

            </div>

            <div>
                <?= $pagination ?>
            </div>
        </div>
    </div>
</div>
