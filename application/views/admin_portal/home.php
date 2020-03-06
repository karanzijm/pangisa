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

    select {
        padding: 8px;
        border-radius: 5px;
        outline: none;
    }

</style>

<body>
<div class="container-fluid">
    <div class="row admin_dashboard">
        <div class="side_menu col-sm-2">
            <div class="side_menu_color_background">
                <div class="profile_section">
                    <img src="<?= base_url() ?>resources/images/icon.png" width="100%"/>
                    <p><?= ucwords(strtolower($this->session->user['name'])) ?></p>
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

        <div class="col-sm-10 main_content">
            <?php
            if ($error) {
                echo "<p class='alert alert-danger'>$error</p>";
            }
            ?>
            <?php
            $this->load->view($view ? $view : "dashboard")
            ?>
            <div>
                <?= isset($pagination) ? $pagination : null; ?>
            </div>
        </div>
    </div>
</div>
</body>

<script type="text/javascript" src="<?= base_url() ?>resources/js/pangisa.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js"></script>
<script>

    var allEditors = document.querySelectorAll('textarea');
    for (var i = 0; i < allEditors.length; ++i) {
        ClassicEditor
            .create(allEditors[i])
            .catch(error = > {console.error(error);
    } )
        ;
    }


</script>
<script>
    var datePickerOptions = {
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        beforeShowYear: function (date) {
            if (date.getFullYear() == 2007) {
                return false;
            }
        },
    }

    $('.date').datepicker(datePickerOptions)
</script>
