<!--the body tag comes from user portal header-->
</body>
<style>
    .footer-area {
        padding: 100px 0px;
        background-color: #282b2e;
        font-size: 14px !important;
    }


    .footer-area h6 {
        color: #fff;
        margin-bottom: 25px;
        font-size: 18px;
        font-weight: 600;
    }

    .single-footer-widget ul li {
        color: #f9f9ff !important;
    }

    .single-footer-widget input {
        border: none;
        width: 80% !important;
        font-weight: 300;
        background: #f9f9ff;
        color: #ffffff;
        padding-left: 20px;
        border-radius: 0;
        font-size: 14px;

    }

    .single-footer-widget .click-btn {
        background-color: #fab700;
        color: #fff;
        border-radius: 0px 0 0 0px;
        padding: 9.5px;
        border: 0;
    }

</style>
<script type="text/javascript" src="<?= base_url() ?>resources/js/ckeditor.js"></script>
<script src="<?= base_url() ?>resources/js/pangisa.js"></script>

<footer class="footer-area">
    <div class="container-fluid side_padding">
        <div class="row">
            <div class="col-sm-3">
                <div class="single-footer-widget">
                    <h6>Quick links</h6>
                    <ul>
                        <li><a href='<?= base_url() ?>Index/'>Back Home</a></li>
                        <li><a href='<?= base_url() ?>Index/businessregistration/add'>Create Vendor Account</a></li>
                        <li><a href='<?= base_url() ?>Index/clientregistration/add'>Create Client Account</a></li>
                        <li><a href='<?= base_url() ?>Index/whypangisa/'>Why pangisa</a></li>
                        <li><a href='<?= base_url() ?>Index/how/'>How it works</a></li>
                        <li><a href='<?= base_url() ?>Index/faq/'>Rent out an item</a></li>
                        <li><a href='<?= base_url() ?>Index/Index/'>Latest Items</a></li>
                        <li><a href='<?= base_url() ?>Index/faq/'>Frequently Asked</a></li>
                        <li><a href='<?= base_url() ?>Index/terms_and_conditions/'>Terms And Conditions</a></li>
                        <li><a href='<?= base_url() ?>Index/faq/'>Our Offices and Contacts</a></li>
                    </ul>
                    <br>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="single-footer-widget">
                    <h6>Categories</h6>
                    <ul>
                        <?php
                        $cats = $this->db
                            ->select("ca.id, ca.name, ca.icon, (select count(*) from items where category=ca.id and status=1) as items")
                            ->from("categories ca")
                            ->order_by("items desc")
                            ->limit(10)
                            ->get()
                            ->result_array();

                        foreach ($cats as $cat) {
                            $linktodisplay = null;
                            if ($cat['items'] > 1) {
                                $linktodisplay = base_url() . "Index/category/" . $cat['id'];
                            } else {
                                $linktodisplay = '#';
                            }
                            echo "
              
                        <li><a href='" . $linktodisplay . "'> " . $cat['name'] . "&nbsp;&nbsp;(" . $cat['items'] . ")</a></li>";
                        }
                        ?>
                    </ul>
                    <br>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="single-footer-widget">
                    <h6>Latest Items</h6>
                    <ul>
                        <?php
                        $cats = $this->db->limit(10)->order_by("date_added", "rand()")->get("items")->result_array();

                        foreach ($cats as $cat) {
                            echo "
                            <li><a href='" . base_url() . 'Index/Items/view/' . $cat['id'] . "' >" . $cat['name'] . "</a></li>
                        ";
                        }
                        ?>
                    </ul>
                    <br>
                </div>
            </div>
            <div class="col-sm-3 social-widget">
                <div class="single-footer-widget">
                    <h6>Contact Us</h6>
                    <div class="footer-social d-flex align-items-center">
                        <ul>
                            <li><i class="fa fa-map-marker"></i> &nbsp; Plot 92, Kanjokya Street, Kamwokya</li>
                            <li><i class="fa fa-phone"></i> &nbsp; +256774669089</li>
                            <li><i class="fa fa-inbox"></i> &nbsp; info@pangisa.co.ug</li>

                            <li>
                                <br><br>
                                <a href="https://play.google.com/store/apps/details?id=com.pangisa" target="_blank">
                                    <img src="<?=base_url()?>/resources/images/iconfinder_google-play_317742.png" width="8%"/> &nbsp;
                                    <span><b style="font-size: 16pt;">Get it on google play </b></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

</footer>
