<style>
    body {
        font: 1em/1.67 Arial, Sans-serif;
        margin: 20px;
        background: #fff;
    }


    .masonry {
        column-count: 6;
        margin: 1.5em 0;
        padding: 0;
        -moz-column-gap: 1.5em;
        -webkit-column-gap: 1.5em;
        column-gap: 1.5em;
        font-size: .85em;
    }

    .item {
        display: inline-block;
        background: #f5f5f5;
        padding: 15px;
        margin: 0 0 1.5em;
        width: 100%;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.18);
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
    }

    /*smart phone*/
    @media only screen and (max-width: 768px) {
        .masonry {
            -moz-column-count: 2;
            -webkit-column-count: 2;
            column-count: 2;
        }
    }

    /*tablets*/
    @media only screen and (min-width: 768px) {
        .masonry {
            -moz-column-count: 3;
            -webkit-column-count: 3;
            column-count: 3;
        }
    }

    /*medium devices*/
    @media only screen and (min-width: 992px) {
        .masonry {
            -moz-column-count: 4;
            -webkit-column-count: 4;
            column-count: 4;
        }
    }

    /*large screens*/
    @media only screen and (min-width: 1200px) {
        .masonry {
            -moz-column-count: 6;
            -webkit-column-count: 6;
            column-count: 6;
        }
    }





</style>

<div class="masonry">
    <?php
    foreach ($data['items_many'] as $item) {
        echo '
                           <div class="item">
                                <a href="' . base_url() . "Index/items/view/" . $item['id'] . '">
                                  <div >
                                   <img  src="' . base_url() . 'items/' . $item['front_view'] . '?tr=w-400,h-300,bl-30,q-10" alt="' . $item['name'] . '" width="100%">
                                  <br><br>
                                   <div>
                                        <i class=" fa fa-calendar"></i> <small class="btn-outline-success" style="color:#ff0000">' . $item['date'] . '</small> &nbsp;&nbsp;
                                        <i class="fa fa-info-circle"></i> <small class="btn-outline-success" style="color:' . ($item['availability'] == 1 ? 'green' : '#ff0000') . '">' . ($item['availability'] == 1 ? 'AVAILABLE' : 'Not available') . '</small>
                                        <b><h6>' . ($item['verified'] == 1 ? "<i class='fa fa-certificate' style='font-size: 15pt; color: #255;'></i>" : null) . " " . ucfirst($item['name'] . ' - ' . $item['color']) . '</h6></b>
                                        <p>' . ucfirst(htmlentities($item['brief_description'])) . '</p>
                                        <button style="background: #FFA000; border:none; padding:4px 10px;  color: #fff; font-size: 12px;  border-radius: 3px; "> <i class="fa fab-money" style="color: #dcn134c;"></i> SH ' . number_format($item['price']) . ' ' . strtoupper($item['price_point']) . '</button>
                                        <br><bR>
                                   </div>
                                </div>
                                </a>
                       </div>
                   ';
    }
    ?>

</div>