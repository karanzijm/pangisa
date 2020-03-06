<head>
    <title><?= $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/fontawesome/fontawesome.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/morris.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/selectize.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/bootstrap/bootstrap.css"/>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/jquery3_3_1.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/selectize.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/raphael.js"></script>
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>resources/images/icon.png"/>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/morris.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/bootstrap/bootstrap.js"></script>
    <script type="text/javascript"
            src="<?= base_url() ?>resources/js/datepicker/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>resources/js/datepicker/css/bootstrap-datepicker.css"/>
</head>

<style>
    body {
        padding: 0px;
        margin: 0px;
        height: 100vh;
        width: 100vw;
        font-family: "Calibri Light";
        background: #fff;
        color: #181818;
    }

    a {
        color: inherit;
        text-decoration: none;
        outline: none;
    }

    a:hover {
        text-decoration: none;
        color: inherit;
    }

    .side_menu {
        padding: 0px;
        background-image: url("<?= base_url() ?>resources/images/sidenavebackground.jpg");
        /*background-size: 200%;*/
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        color: #fff;
        border-right: 10px solid #f5f5f5;
        height: 100vh;
        max-height: 100%;
        overflow-y: hidden;
        will-change: transform;
        backface-visibility: hidden;
    }

    .side_menu_color_background {
        height: inherit;
        overflow-y: scroll;
        background: /*#24879d;*/ rgba(36, 135, 157, 0.7) /*rgba(33, 150, 243, 0.6);*/
    }

    .side_menu ul {
        padding: 0px;
        margin: 0px;
    }

    .side_menu ul li {
        padding: 13px 20px;
        margin: 0px;
        list-style: block;
        color: #fff;
        font-weight: 800;
    }

    .side_menu ul a:hover li {
        font-weight: 800;
        text-transform: capitalize;
        background: inherit;
    }

    .side_menu ul a:hover {
        text-decoration: none;
    }

    .side_menu_color_background::-webkit-scrollbar {
        width: 0px;
        background: #222;
    }

    .side_menu_color_background::-webkit-scrollbar-track {
        -webkit-box-shadow: none;
    }

    .side_menu_color_background::-webkit-scrollbar-thumb {
        background-color: #fff;
        height: 20px;
        outline: 1px solid slategrey;
    }

    /*-- profile section --*/
    .profile_section {
        padding: 13px 0px;
        width: auto;
        /*background: rgba(255,255,255,0.1);*/
        min-height: 100px;
        text-align: center;
        /*border-bottom: 10px solid #f5f5f5;*/
    }

    .profile_section p {
        color: #fff;
        margin-top: 10px;
        font-weight: 800;
    }

    .profile_section img {
        width: 70px;
        height: 80px;
        border-radius: 50%;
    }

    .main_content {
        padding: 10px !important;
        max-width: 99% !important;
        height: 100vh;
        max-height: 100%;
        overflow-y: scroll !important;
    }

    .main_content::-webkit-scrollbar {
        width: 4px;
        background: #fff;
    }

    .main_content::-webkit-scrollbar-track {
        -webkit-box-shadow: none;
    }

    .main_content::-webkit-scrollbar-thumb {
        background-color: #1984ab;
        height: 20px;
        height: 20px;
        outline: 1px solid slategrey;
    }


    .reportDisplayContent::-webkit-scrollbar {
        width: 4px;
    }


    .result_table tr:first-child {
        background: #255;
        font-size: 15px;
    }

    .result_table tr:first-child td {
        color: #fff;
    }

    .result_table tr td {
        padding: 8px;
        font-weight: normal;
        font-size: 14px;
    }

    .result_table tr:nth-child(2n+2) {
        background: #fff;
    }

    .form_table input[type="submit"], .form_table button {
        padding: 10px;
        min-width: 150px;
        background: #08578c;
        color: #fff;
        border: none;
        border-radius: 2px;
        outline: none;
    }

    .table tr:first-child {
        background: #303f49bf;
        color: #f9f9f9;
    }

    .table tr:nth-child(2n+2) {
        background: rgba(0, 0, 0, 0.02);
    }

    .form_table tr td {
        padding: 20px 30px 0px 0px;
        min-width: 100%;
        padding: 10px 30px 10px 0px;
    }

    .form_table tr td input, .form_table tr td textarea, .form_table tr td select {
        padding: 10px;
        min-width: 100%;
        max-width: 100%;
        magin-top: 10px;
        outline: none;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin: 0px;
    }

    .render_form_button {
        border-radius: 5px;
        padding: 6px;
        width: auto;
        border: none;
        background: #255;
        color: #fff;
    }

    .filter-table input, .filter-table select {
        padding: 5px;
        border-radius: 0px;
        outline: none;
    }

    .filter-table tr td {
        padding-left: 5px;
    }

    .container-fluid, .row, .col-sm-3, .col-sm-12, .col-sm-10, .col-sm-2, .col-sm-6, .row, .container, .container {
        padding: 0px;
        margin: 0px;
    }

    .col_content {
        padding: 0px;
        margin: 3px 5px 15px 0px;
        background: #cccccc2b;
    }

    .col_content .row {
        display: table;
        width: 100%;
    }

    .col_content .row div {
        padding: 20px;
    }

    .col_content .row .col-sm-4 {
        background: #1984ab21;
        display: table-cell;
        width: 35%;
        vertical-align: middle;
        text-align: center;
    }

    .col_content .row .col-sm-4 {
        display: table-cell;
        vertical-align: middle;
        text-align: left;
        font-size: 40pt;
    }

    .app_client_graph_div {
        min-height: 60vh;
        background: #f9f9f9;
        margin-right: 5px;
        margin-bottom: 20px;
    }

    .edit_account_table {
        padding: 0px 50px;
        max-width: 60%;
    }

    .dashboard_2_panels:first-child div {
        margin-right: 10px;
    }

    .dashboard_2_panels:last-child div {
        margin-left: 10px;
    }

    .dashboard_2_panels div {
        background: #f9f9f9;
        padding: 20px;
    }

    .build_form_form_group {
        margin: 0px 20px 0px 0px;
    }

    .table tr td {
        font-size: 14px !important;
        padding: 8px !important;
    }

    .table tr:first-child td {
        font-size: 15px !important;
        font-weight: 600;
        padding: 10px;
    }

    .order_details_table tr td {
        width: 25%;
        padding: 16px !important;
        font-size: 16px !important;
    }

    .order_details_table tr:first-child td {
        font-size: 15px !important;
        font-weight: normal;
        background: inherit;
        padding: 10px;
    }

    .order_details_table tr td:nth-child(3), .order_details_table tr td:nth-child(1) {
        font-weight: bold;
    }

    .order_details_images .col-sm-4 {
        margin: 0px;
        padding: 0px;
    }

    .order_details_images .col-sm-4 img {
        height: 30%;
        padding-right: 5px;
    }

    .pagination {
        padding: 0px;
        margin: 0px;
    }

    .pagination li {
        padding: 1px 5px;
        margin: 2px;
        border: 1px solid rgba(0, 0, 0, 0.3);
        color: #181818
    }

    .lib_table tr td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 14px !important;
    }

    .category_list button {
        display: inline;
        padding: 4px 12px;
        border: 1px solid rgba(0, 0, 0, 0.2);
        margin: 5px;
        border-radius: 5px;
        background: none;
    }

    .category_list button i {
        padding: 5px;
        border-radius: 50%;
        border: 1px solid rgba(0, 0, 0, 0.2);
        color: #ff0000;
    }

    .filter_search_item {
        padding: 6px 5px;
        border: 1px solid #1984ab;
        border-radius: 20px;
        font-size: 13px;
        width: auto;
    }

    .filter_search_item input, .filter_search_item button {
        outline: none;
        border: none;
        background: none;
        color: #1984ab;
        padding: 5px;
    }

    .filter_search_item input {
        width: auto;
        min-width: 170px;
        border-right: 1px solid #1984ab;
    }

    .filter_search_item input[type="date"] {
        width: auto;
    }

    .filter_search_item input[type="date"]:last-child {
        border: none !important;
    }

    .filter_search_item button {
        width: auto;
        width: auto;
        border-right: 1px solid #1984ab;
    }

    .display_order_details h5 {
        font-weight: bold;
        margin-bottom: 15px;
    }

    .result_table_light tr:first-child {
        background: none !important;
        color: inherit;
        font-weight: normal !important;
    }

    .result_table_light tr:first-child td {
        color: inherit;
        font-weight: normal !important;
    }

    .result_table_light tr td {
        min-width: 15%;
    }

    .result_table_light tr td:first-child, .result_table_light tr td:nth-child(3) {
        font-weight: bold;
    }

    .result_table_light_background tr:first-child td {
        color: #222222 !important;
        font-weight: 700 !important;
    }

    .display_order_details {
        padding: 30px !important;
        border: 0px solid #333;
        box-shadow: 2px 2px 100px rgba(0, 0, 0, 0.2);
    }

    .display_order_details ul {
        padding: 0px;
        margin: 0px;
    }

    .display_order_details ul li {
        display: inline-block;
        list-style: none;
    }


    /*reports pages*/
    .report_page_root_division ul {
        margin: 0px;
        padding: 0px;
    }

    .report_page_root_division ul li {
        list-style: none;
        padding: 25px;
        font-weight: 700;
        color: #2e0f35;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        text-transform: capitalize;
        kbox-shadow: 0px 1px 0px rgba(0, 0, 0, 0.2)
    }

    .report_page_root_division ul a:hover li {
        background:: #000 !important;
    }

    .report_page_root_division_side_menu {
        background: #f5f5f5;
        height: 100%;
    }

    .mtn-momo-payment-procedure section input, .mtn-momo-payment-procedure section select {
        border: none;
        outline: none;
        width: 70%;
        border-left: 1px dotted #ccc;
        margin-left: 10px;
        padding-left: 10px;
        font-weight: bold;
        color: #ff0000;
    }

    .transactions-search-field select, .transactions-search-field input {
        padding: 5px;
        height: 35px;
        margin: 0px;
    }
</style>


