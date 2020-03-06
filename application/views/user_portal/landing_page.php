<style>
    .pg-landing-page-lastest-items-masonry {
        display: flex;
        flex-direction: row;
        padding: 0px;
        height: auto;
        flex-wrap: wrap;
    }


    .pg-landing-page-lastest-items-masonry-item {
        /*display: flex;*/
        width: 19%;
        max-width: 19%;
        -webkit-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2);
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2);
        /*flex: 1 1 auto;*/
        margin: 5px;
        border-radius: 5px;
        padding: 0px;
        height: auto;
    }

    .item_content {
        padding: 10px;
    }
    .pg-landing-page-lastest-items-row-wrapper .item_content p {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .pg-landing-page-lastest-items-row-wrapper {
        padding: 0px;
        margin: 0px;
        width: inherit;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border-radius: 4px !important;
        background: #ffffff;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        min-height: 340px;
        max-height: 340px;
    }

    .pg-landing-page-lastest-items-row-image-div {
        height: 20vh;
        max-height: 20vh;
        text-align: center;
        background: #212121;
    }

    .pg-landing-page-lastest-items-row-image-div img {
        height: inherit;
        max-height: inherit;
        width: auto;
        max-width: 100%;
        -webkit-animation-duration: 0s;
        -moz-animation-duration: 0s;
        -o-animation-duration: 0s;
        animation-duration: 0s;
    }

    .pg-landing-page-lastest-items-row-money-label {
        background: #FFA000;
        border: none;
        padding: 4px 10px;
        color: #fff;
        font-size: 12px;
        border-radius: 3px;
        /*position: absolute;*/
        /*bottom:0px;*/
    }


    .masonry-item:last-child {
        margin-right: 0px !important;
    }

    @media only screen and (max-width: 768px) {
        .pg-landing-page-lastest-items-row-wrapper {
            padding: 0px;
            margin: 5px;
            /*-webkit-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);*/
            /*box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);*/
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            border-radius: 4px !important;
            background: #ffffff;
            -moz-border-radius: 4px;
            -webkit-border-radius: 4px;
            max-height: 100vh;
        }

        .pg-landing-page-lastest-items-row-image-div {
            height: 20vh;
            max-height: 20vh;
            text-align: center;
            background: #212121;
        }

        .pg-landing-page-lastest-items-row-image-div img {
            height: inherit;
            max-height: inherit;
            width: auto;
            max-width: 100%;
            -webkit-animation-duration: 0s;
            -moz-animation-duration: 0s;
            -o-animation-duration: 0s;
            animation-duration: 0s;
        }

        .pg-landing-page-lastest-items-masonry-item {
            width: 47%;
            max-width: 47%;
        }
    }

</style>

<div class="container-fluid side_padding landing_page_slider">
    <div class="row">
        <div class="col-sm-3">
            <ul class="ilist-group">
                <a href="#">
                    <li class="list-group-item" style="color: #06274f; font-weight: bold;">ALL CATEGORIES</li>
                </a>
                <?php
                $cats = $this->db
                    ->select("ca.id, ca.name, ca.icon, (select count(*) from items where category=ca.id) as items")
                    ->from("categories ca")
                    ->order_by("items desc")
                    ->limit(9)
                    ->get()
                    ->result_array();

                foreach ($cats as $ct) {
                    $href = $ct['items'] > 0 ? base_url() . "Index/category/" . $ct['id'] : "#";
                    echo "<a href='" . $href . "' class='list-group-item'><li><i class='fa fa-check-circle'></i> &nbsp;" . ucfirst(strtolower($ct['name'])) . " </li></a>";
                }
                ?>
            </ul>
        </div>

        <!-- image for landing slider       -->
        <div class="col-sm-7">

        </div>

        <!--  features      -->
        <div class="col-sm-2 quick-custom-features">
            <ul class="list-group">
                <a href="#">
                    <li class="list-group-item"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Rent an Item</li>
                </a>
                <a href="#">
                    <li class="list-group-item"><i class="fa fa-sign-in"></i>&nbsp;&nbsp; Register Free Account</li>
                </a>
                <a href="#">
                    <li class="list-group-item"><i class="fa fa-thumbs-up"></i>&nbsp;&nbsp; Monitor Growth trends</li>
                </a>
                <a href="#">
                    <li class="list-group-item"><i class="fa fa-check"></i>&nbsp;&nbsp; Verify Items</li>
                </a>
                <a href="#">
                    <li class="list-group-item"><i class="fa fa-credit-card"></i>&nbsp;&nbsp; Secure Payments</li>
                </a>

            </ul>
        </div>

    </div>
</div>

<div class="container-fluid side_padding display_categories_index_page">
    <div class="row">
        <h3 style="color: #062747">Most searched categories</h3><br><br>
    </div>
    <div class="categories-masonry">
        <?php

        foreach ($categories as $cat) {
            $img = $cat['icon'] ? "<img src='" . base_url() . "category_icons/" . $cat['icon'] . "' width='100%' height='inherit'/>" : null;
            ?>
            <div class='categories-masonry-item'>
                <div class='container-fluid'>
                    <div class='row'>
                        <div class='col-sm-4'>
                            <?= $img ?>
                        </div>

                        <div class='col-sm-8'>
                            <?php
                            echo "<u><a href='" . base_url() . "Index/category/" . $cat['id'] . "'><b>  " . $cat['name'] . "</b></a></u>";
                            $subcats = $this->db->where("category", $cat['id'])->limit(5)->get("sub_categories")->result_array();

                            echo "<ul>";
                            foreach ($subcats as $subcat) {
                                $this->db->where(['sub_category' => $subcat['id']]);
                                $count_of_items_per_sucategory = $this->db->count_all_results("items");
                                $link = $count_of_items_per_sucategory > 0 ? base_url() . "Index/category/" . $cat['id'] . "/" . $subcat['id'] : "#";

                                echo "<a href='" . $link . "'><li>" . $subcat['name'] . "</li></a>";
                            }
                            echo "</ul>";
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        }
        ?>
    </div>
</div>

<!--rent your desired item-->
<div class="container-fluid side_padding">
    <div class="rent-desired-item">
        <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">

            <ol class="carousel-indicators">
                <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            </ol>

            <div class="carousel-inner">

                <?php
                $i = 0;
                foreach ($items as $item) {
                    echo '
                           <div class="carousel-item pg-landing-page-rent-desired ' . ($i == 0 ? "active" : null) . '">
                                <div class="container-fluid">
                                    <div class="row">
                                            <div class="col-sm-5" style="padding: 40px;">
                                                    <b><h1>' . ucfirst($item['name']) . '</h1></b>
                                                    <p class="cut_line_description">' . ucfirst($item['brief_description']) . '</p>
                                                    <p>Size : ' . ucfirst($item['size']) . '<br>  
                                                    Category : ' . $item['category'] . '<br>
                                                    Identification : ' . $item['identification_number'] . '<br>
                                                    Color : ' . ucfirst($item['color']) . '</p>
                                                    <h6 >' . ($item['is_negotiable'] ? 'Negotiable' : 'UGX ' . number_format($item['price']) . " " . $item['price_point']) . '</h6> </p>
                                                    <br>
                                                    <a href=' . base_url() . 'Index/orderItem/' . $item['id'] . '>                                                
                                                        <button class="btn btn-sm btn-success">ORDER THIS ITEM</button>
                                                    </a>
                                               </div>
                                               
                                            <div class="col-sm-7">
                                               <img  src="' . base_url() . 'items/' . $item['front_view'] . '" alt="' . $item['name'] . '"> 
                                            </div>
                                    </div>
                                </div>
                           </div>
                   ';
                    $i++;
                }
                ?>

            </div>

            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>
</div>
<!--</div>-->

<!--view sample items-->
<br><br>
<div class="container-fluids side_padding" style="display: block;" id="latestItems">
    <div class="row">
        <h4>Latest Items</h4><br><br><br>
    </div>
    <div class="row pg-landing-page-lastest-items-masonry">
        <?php
        foreach ($items_many as $item) {
            echo '
               <div class="pg-landing-page-lastest-items-masonry-item " style="background: #ffffff">
                    <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                      <div class="pg-landing-page-lastest-items-row-wrapper">
                          <div class="pg-landing-page-lastest-items-row-image-div" >
                           <img src="' . base_url() . 'resources/images/img_placeholder.png" class="lazy_loading_img" data-src="' . base_url() . 'items/' . $item['front_view'] . '" alt="' . $item['name'] . '" width="100%">
                          </div>
                          
                           <div class="item_content">
                                <i class=" fa fa-calendar"></i> <small class="btn-outline-success" style="color:#ff0000">' . $item['date'] . '</small> &nbsp;&nbsp;
                                <i class="fa fa-info-circle"></i> <small class="btn-outline-success" style="color:' . ($item['availability'] == 1 ? 'green' : '#ff0000') . '">' . ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') . '</small>
                                <b><h6>' . ($item['verified'] == 1 ? "<i class='fa fa-certificate' style='font-size: 15pt; color: #255;'></i>" : null) . " " . ucfirst($item['name'] . ' - ' . $item['color']) . '</h6></b>
                                <p>' . ucfirst(htmlentities($item['brief_description'])) . '</p>
                                <button class="pg-landing-page-lastest-items-row-money-label"> <i class="fa fab-money" style="color: #dcn134c;"></i> ' . (number_format($item['price']) > 100 ? "SH " . number_format($item['price']) . ' ' . strtoupper($item['price_point']) : "Negotiable") . '</button>
                           </div>
                    </div>
                    </a>
           </div>
                   ';
        }
        ?>

    </div>
</div>
<br>

