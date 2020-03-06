<style>
    .admin_dashboard .col-sm-2 {
        -ms-flex: 0 0 13.666667%;
        flex: 0 0 13.666667%;
        max-width: 13.666667%;
    }

    .admin_dashboard .col-sm-10 {
        -ms-flex: 0 0 86.333333%;
        flex: 0 0 86.333333%;
        max-width: 86.333333%;
    }
</style>

<body>
<div class="container-fluid">
    <div class="row admin_dashboard">
        <div class="side_menu col-sm-2">
            <div class="side_menu_color_background">
                <div class="profile_section">
                    <img src="<?= base_url() ?>resources/images/logo.png" width="50%"/>
                    <p><?= $this->session->user['name'] ?></p>
                </div>
                <ul>
                    <?php
                    foreach ($menu as $menu):
                        echo "<a href='" . $menu['link'] . "'>
                            <li> <i class='" . $menu['icon'] . "' style='color:" . $menu['icon_color'] . "'></i> &nbsp; &nbsp;" . ucfirst($menu['name']) . "</li></a>";
                    endforeach;
                    ?>
                </ul>
                <br><bR>
            </div>
        </div>

        <div class="main_content col-sm-10">
            <?php
            $this->load->view($view);
            ?>
        </div>
    </div>
</div>
</body>

<script type="text/javascript" src="<?= base_url() ?>resources/js/pangisa.js"></script>
