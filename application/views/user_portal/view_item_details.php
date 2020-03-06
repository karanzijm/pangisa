<style>

    .class_item_details_display {
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .pg_view_item_view_details_carousel_inner {
        height: 60% !important;
        min-height: 60% !important;
        background: #333;
        text-align: center;
    }

    .pg_view_item_view_details_carousel_inner .carousel-item {
        height: 80vh;
        min-height: 80vh !important;
    }

    .pg_view_item_view_details_carousel_inner .carousel-item img {
        height: 80vh !important;
    }


    @media only screen and (max-width: 768px) {
        .pg_view_item_view_details_carousel_inner .carousel-item {
            height: inherit;
            min-height: 60vh !important;
            overflow-y: scroll;
        }

        .pg_view_item_view_details_carousel_inner .carousel-item img {
            width: 100% !important;
            height: auto;
        }
    }

</style>


<div id="fb-root"></div>
<script>
    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

<div class="container-fluid side_padding class_item_details_display">
    <div class="row">
        <div class="col-sm-8 ">
            <div id="pg-view-items-details-carousel-slider" class="carousel slide" data-ride="carousel">

                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
                </ol>

                <div class="carousel-inner pg_view_item_view_details_carousel_inner">
                    <div class="carousel-item active">
                        <img class="" src="<?= base_url() ?>items/<?= $item['front_view'] ?>"
                             alt="Front View">
                    </div>
                    <div class="carousel-item">
                        <img class="" src="<?= base_url() ?>items/<?= $item['side_view'] ?>"
                             alt="side View">
                    </div>
                    <div class="carousel-item">
                        <img class="" src="<?= base_url() ?>items/<?= $item['rear_view'] ?>"
                             alt="Rear View">
                    </div>
                </div>

                <a class="carousel-control-prev" href="#pg-view-items-details-carousel-slider" role="button"
                   data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#pg-view-items-details-carousel-slider" role="button"
                   data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>

        <div class="col-sm-4 class_item_details_display_info">
            <div>
                <p class="banner-title">
                    <?= $item['name'] . " " . ($item['verified'] == 1 ? ' &nbsp;&nbsp;&nbsp;<i class="fa fa-certificate"></i>' : null) ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="fb-share-button"
                          data-href="<?= isset($data['og_description']) ? $data['og_description']['url'] : 'https://pangisa.co.ug'; ?>"
                          data-layout="button"></span>
                </p>
                <small>Product by <?= $item['owner'] ?></small>

                <hr>
                <table class="table-sm" width="100%">
                    <tr>
                        <td>Name</td>
                        <td><?= $item['name'] ?></td>
                    </tr>
                    <tr>
                        <td>Color</td>
                        <td><?= $item['color'] ?></td>
                    </tr>
                    <tr>
                        <td>Size</td>
                        <td><?= $item['size'] ?></td>
                    </tr>
                    <tr>
                        <td>Year of Make</td>
                        <td><?= $item['year_of_make'] ?></td>
                    </tr>
                    <tr>
                        <td>Category</td>
                        <td><?= $item['category_name'] ?></td>
                    </tr>
                    <tr>
                        <td>Sub Category</td>
                        <td><?= $item['sub_category_name'] ?></td>
                    </tr>
                    <tr>
                        <td>AD Number</td>
                        <td><?= $item['item_number'] ?></td>
                    </tr>
                    <tr>
                        <td>Registration Number</td>
                        <td><?= $item['identification_number'] ?></td>
                    </tr>
                    <tr>
                        <td>Price</td>
                        <td>
                            <button class="btn btn-sm btn-danger">
                                UGX <?= number_format($item['price']) . " &nbsp;" . $item['price_point'] . ($item['is_negotiable'] == 'True' ? " ( Negotiable )" : null) ?></button>
                        </td>
                    </tr>
                    <tr>
                        <td>Availability</td>
                        <td>
                            <small class="btn-outline-success"
                                   style="color:'<?= ($item['availability'] == 1 ? 'green' : '#ff0000') ?>'"><?= ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') ?></small>
                        </td>
                    </tr>
                    <tr>
                        <td>Date Added</td>
                        <td><?= $item['date_added'] ?></td>
                    </tr>
                    <tr>
                        <td>Pick up Locaton</td>
                        <td><?= $item['pick_up_location'] ?></td>
                    </tr>
                </table>

                <!--                <form method="post" action="--><? //= base_url() ?><!--Index/items/comment/-->
                <? //= $id ?><!--">-->
                <!--                    <input type="text" class="comment_input" name="name" value=" " placeholder="Name" required>-->
                <!--                    <input type="email" class="comment_input" name="email" value=" " placeholder="Email" required>-->
                <!--                    <textarea type="text" rows="5" class="comment_input" name="comment" value=" " placeholder="Comment"-->
                <!--                              required></textarea>-->
                <!--                    <input type="submit" class="btn  btn-sm btn-info" value="Comment">-->
                <!--                </form>-->

            </div>

        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <?php
        if (!$this->session->user['id'] || $this->session->user['user_type'] <> "client"):
            ?>
            <div class="modal fade exampleModalLongSimpleLogin" id="exampleModalLong" tabindex="-1" role="dialog"
                 aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <?php $this->load->view("user_portal/login_simple"); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
</div>

<!--item details and features-->
<br>
<div class="container-fluid side_padding">
    <div class="row display_item_details_page">
        <div class="col-sm-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#ItemDescription" data-toggle="tab"><i
                                class="fa fa-deviantart"></i> Item Description</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#features" data-toggle="tab"><i class="fa fa-list"></i> Features</a>
                </li>

                <li class="nav-item">

                    <a class="nav-link" href="#comments" data-toggle="tab"><i class="fa fa-sticky-note"></i> Reviews</a>
                </li>

            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id='ItemDescription'
                     style="padding:40px 20px; background: #fff;"><?= $item['brief_description'] ?></div>
                <div class="tab-pane" id='features'><?= $item['features'] ?></div>
                <div class="tab-pane" id='comments' style="padding: 10px 0px; background: #f9f9f9">
                    <?php
                    foreach ($comments as $comment) {
                        echo "<div style='padding: 20px; background: #fff; margin-bottom: 10px;'>";
                        echo "<b>" . $comment['email'] . "</b> &nbsp;&nbsp;&nbsp;<small>" . explode(" ", $comment['date'])[0] . "</small> <bR>" . $comment['comment'] . "<br><br>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>

            <br>

            <?php if (!$this->session->user['id'] /*|| $this->session->user['user_type'] <> "client"*/) {
                $this->session->set_userdata('nav_url', base_url() . 'Index/orderItem/' . $item['id']);
                ?>
                <a href="#" data-toggle="modal" data-target="#exampleModalLong">
                    <button class="btn btn-sm btn-info" type="button" data-toggle="collapse"
                            data-target="#place_order" aria-expanded="false"
                            aria-controls="collapseExample"
                            data-toggle="tooltip" data-placement="top"
                            title="Remember: You need to login first to create an order"
                    >
                        ORDER THIS ITEM &nbsp; <i class="fa fa-shopping-bag"></i>
                    </button>
                </a>

            <?php } else { ?>
                <a href="<?= base_url() ?>Index/orderItem/<?= $item['id'] ?>">
                    <button class="btn btn-sm btn-info" type="button" data-toggle="collapse"
                            data-target="#place_order" aria-expanded="false" aria-controls="collapseExample">
                        ORDER THIS ITEM &nbsp; <i class="fa fa-shopping-bag"></i>
                    </button>
                </a>
            <?php } ?>


        </div>

        <div class="col-sm-3">

        </div>

    </div>
</div>

<br><br>
<div class="container-fluid side_padding ">
    <div>
        <h3>Other Items you Might Like</h3>
    </div>

    <div class="row display_items_you_might_like">

        <?php
        foreach ($related as $item) {
            echo '
                   <div class="col-sm-2" >
                        <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                            <div class="view_item_details_page_display_related">
                               
                               <div class="view_item_details_page_display_related_image">
                                <img  src="' . base_url() . 'items/' . $item['front_view'] . '" alt="' . $item['name'] . '"/>
                               </div>
                               
                               <div class="view_item_details_page_display_related_desc">
                                    <b><h6>' . ucfirst($item['name']) . '</h6></b>
                                    <p>' . ucfirst($item['brief_description']) . '</p>
                                    <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; "> <i class="fa fab-money" style="color: #dcn134c;"></i> UGX ' . number_format($item['price']) . ' </button>
                                    <br><bR>
                               </div>
                            </div>
                        </a>
                   </div>
               ';
        }
        ?>

    </div>
</div>
<br><br>










