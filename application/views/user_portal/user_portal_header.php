<head>
    <title><?= isset($data['og_description']) ? $data['og_description']['title'] . "- Pangisa " : $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/fontawesome/fontawesome.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/selectize.css"/>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>resources/css/bootstrap/bootstrap.css"/>
    <link rel="shortcut icon" type="image/png" href="<?= base_url() ?>resources/images/icon.png"/>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/jquery3_3_1.js"></script>
    <link rel="manifest" href="<?= base_url() ?>resources/js/manifest.json"/>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/selectize.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/raphael.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/morris.js"></script>
    <script type="text/javascript" src="<?= base_url() ?>resources/js/bootstrap/bootstrap.js"></script>

    <script type="text/javascript"
            src="<?= base_url() ?>resources/js/datepicker/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>resources/js/datepicker/css/bootstrap-datepicker.css"/>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet">
    <link href="<?= base_url() ?>" rel="canonical">
    <link href="<?= base_url() ?>" rel="home">
    <link href="<?= base_url() ?>" rel="alternate" hreflang="x-default">
    <meta name="theme-color" content="#290930">

    <?php if (isset($data['og_description'])) {
        $og = $data['og_description'] ?>
        <meta name="author" content="akankwasa brian +256778693362">
        <meta name="description" content="<?= $og['description'] ?>">
        <meta name="keywords"
              content="akankwasa brian, Uganda, Items for sale, rent , rent , Pangisa, Pangisa Uganda , Pangisa (U) limited, items for hires, cars, rent a car,Pangisa,pagnisa,pagisa,rental,car rental,auto hires,vehicle,vehicles,vehicle hire,equipment equipment rental,equipment hire,uganda car hire,uganda equipment,hire company,uganda hire companies,emergency,vacation hires,vacation rentals,corporate rentals ,corporate hires,construction equipment ,earth moving equipment,">

        <meta property="og:title" content="<?= $og['title'] ?> @ Pangisa"/>
        <meta property="og:url" content="<?= $og['url'] ?>"/>
        <meta property="og:description" content="<?= $og['description'] ?>">
        <meta property="og:image" content="<?= $og['image'] ?>">
        <meta property="og:type" content="article"/>
        <meta property="og:locale" content="en_GB"/>
    <?php } else { ?>
        <meta name="author" content="akankwasa brian +256778693362">
        <meta name="description"
              content="Pangisa is an online market place that links equipment owners with potential hirers and clients.The platform that bridges the hire gap between the asset owner and the potential hirer. Find equipment closest to you that will fill your need hustle free. Rent that asset, vehicle, equipment at a click of a button! We having new and amazing products to choose from.">
        <meta name="keywords"
              content="akankwasa brian, Uganda, Items for sale, rent , rent , Pangisa, Pangisa Uganda , Pangisa (U) limited, items for hires, cars, rent a car,Pangisa,pagnisa,pagisa,rental,car rental,auto hires,vehicle,vehicles,vehicle hire,equipment equipment rental,equipment hire,uganda car hire,uganda equipment,hire company,uganda hire companies,emergency,vacation hires,vacation rentals,corporate rentals ,corporate hires,construction equipment ,earth moving equipment,">

        <meta property="og:title" content="Pangisa"/>
        <meta property="og:url" content="<?= base_url() ?>/Index/"/>
        <meta property="og:description"
              content="Pangisa is an online market place that links equipment owners with potential hirers and clients.The platform that bridges the hire gap between the asset owner and the potential hirer. Find equipment closest to you that will fill your need hustle free. Rent that asset, vehicle, equipment at a click of a button! We having new and amazing products to choose from.">
        <meta property="og:image" content="<?= base_url() ?>resources/images/icon.png">
        <meta property="og:type" content="article"/>
        <meta property="og:locale" content="en_GB"/>
    <?php } ?>
</head>

<style>
    body {
        padding: 0px !important;
        margin: 0px !important;
        -webkit-font-smoothing: antialiased;
        max-width: 1700px;
        /*font-size: 13px;*/
        /*line-height: 1.5em;*/
        /*color: #062c33;*/
       font-family: 'Work Sans', Arial, sans-serif !important;
        color: #666;
        font-size: 14px;
        /*font-family: 'Work Sans', Arial, sans-serif;*/
        line-height: 1.6;
    }

    body::-webkit-scrollbar {
        width: 2px
    }

    a {
        color: inherit;
        text-decoration: none;
        outline: none;
    }

    a:hover {
        color: inherit;
        text-decoration: none;
    }

    li {
        list-style: none;
    }

    ul {
        padding: 0px;
        margin: 0px;
    }

    /*background: #04091e*/
    /*header maneu css*/
    .main-menu-container {
        background: #ffffff;
        color: #333333;
    }

    .header_menu {
        padding: 0px;
        margin: 0px;
        background: inherit !important;
        color: #333 !important;
    }

    .header_menu .navbar-collapse ul li .fa {
        color: #dc134c !important;
        font-size: 13pt;
    }

    .light_header_menu div ul li a {
        padding: 20px 15px 20px 0px;
        display: inline-block;
        color: #fff !important;
    }

    .header_menu div ul li a {
        padding: 10px 15px 15px 0px;
        display: inline-block;
        color: #333333 !important;
    }


    /*related itesm*/
    .display_items_you_might_like {
        /*background: #ffffff !important;*/
        padding: 0px 0px !important;
    }

    .view_item_details_page_display_related {
        background: #ffffff !important;
        margin: 0px 5px;
    }

    .view_item_details_page_display_related_desc {
        padding: 10px;
    }

    .view_item_details_page_display_related div p {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    .view_item_details_page_display_related_image {
        height: 20vh !important;
        min-height: 20vh !important;
        max-height: 20vh !important;
        overflow-y: hidden;
        background: #222;
        text-align: center;
        overflow: hidden;
    }

    .view_item_details_page_display_related_image img {
        /*height: 100%;*/
        min-height: 100%;
        max-width: 100%;
    }

    .breadcrumbs {
        background: #f9f9f9;
    }

    .breadcrumbs li {
        display: inline-block;
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


    /*landing page rent desired slider*/
    .carousel-inner {
        padding: 0px !important;
        margin: 0px !important;
    }

    .pg-landing-page-rent-desired {
        height: 60%;
        min-height: 60%;
        max-height: 70%;
        overflow-y: hidden;
    }

    .pg-landing-page-rent-desired .container-fluid {
        height: 100%;
    }

    .pg-landing-page-rent-desired .container-fluid .row {
        height: 100%;
    }

    .pg-landing-page-rent-desired .container-fluid .row div {
        height: inherit;

    }

    .pg-landing-page-rent-desired .container-fluid .row .col-sm-7 {
        padding: 0px !important;
    }

    .pg-landing-page-rent-desired img {
        width: 100%;
        height: auto;
        min-height: 80%;
    }


    .nav nav-tabs .nav-item a .fa {
        color: #ff0000 !important;
    }

    .header_menu div ul li:first-child a {
        padding-left: 0px !important;
    }

    .header_menu div ul li:hover a {
        font-weight: 500;
    }

    .header_menu ul a:hover .<?= $active_link ?> {
        background: #fff;
        color: #743434;
    }

    .header_menu_display_login_links {
        float: right;
    }

    .register_dropdown {
        background: #fff !important;
    }

    .register_dropdown ul {
        background: #fff;
        margin: 0px !important;
        padding: 0px !important;
    }

    .register_dropdown ul a {
        background: #fff;
    }

    .register_dropdown ul a li {
        color: #222 !important;
        padding: 5px !important;
    }


    .footer {
        background: #525C65;
        color: #fff;
    }

    .footer .row {
        padding: 30px 0px;
    }

    .footer .row .col-sm-3 {
        padding: 10px 30px !important;
    }

    .footer .row .col-sm-3 ul li {
        padding: 5px 0px !important;
    }

    .render_page {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    .render_page form table tr td {
        padding: 20px 30px 0px 0px;
        min-width: 100%;
    }

    .render_page form table tr td input {
        padding: 10px;
        min-width: 100%;
        magin-top: 10px;
        outline: none;
        border-radius: 30px;
        border: 1px solid #ccc;
        margin-top: 5px;
    }

    .render_page form table tr td input[type="submit"] {
        background: #743434;
        color: #fff;
        margin-top: 10px;
        padding: 5px 3px;
        width: 100px;
        border-radius: 5%;
    }

    .error_message {
        background: #f9f9f9;
        color: red;
        font-weight: bold;
        padding: 20px;
    }

    .container-fluid, .row, .col-sm-3, .col-sm-5, col-sm-7, .col-sm-12, .col-sm-10, .col-sm-2, .col-sm-6, .col-sm-8, .col-sm-4, .row, .container, .container, .col-sm-9 {
        padding: 0px;
        margin: 0px;
    }

    .col_content {
        padding: 15px;
        margin: 3px 5px 15px 0px;
        background: #cccccc2b;
    }

    .landing_page_display_categories div {
        margin: 0px;
    }

    .landing_page_display_categories div div {
        padding: 10px;
    }

    .landing_page_display_categories div div p {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
    }

    .landing_page_display_categories div img {
        width: 100%;
    }

    .categories-masonry {
        -webkit-column-count: 3;
        column-count: 3;
        -webkit-column-gap: 1.3em;
        column-gap: 1.3em;
        font-size: 1.0em;
        width: 100%;
    }

    .category-page-masonry {
        -webkit-column-count: 4;
        column-count: 4;
        -webkit-column-gap: 1.3em;
        column-gap: 1.3em;
        font-size: 1.0em;
        width: 100%;
    }

    .categories-masonry-item .row {
        padding: 0px !important;
    }

    .categories-masonry-item .col-sm-8 {
        padding: 10px !important;
    }

    .categories-masonry-item {
        display: inline-block;
        padding: 0px;
        border: 0px solid #ccc;
        margin: 0 0 1.1em;
        width: 100%;
        -webkit-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.3);
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        border-radius: 4px !important;
        background: #ffffff;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
    }


    .masonry {
        -webkit-column-count: 5;
        column-count: 5;
        -webkit-column-gap: 1.3em;
        column-gap: 1.3em;
        font-size: .99em;
        -ms-flex-direction: row;
        -webkit-column-;
    }

    .masonry-item {
        display: inline-block;
        vertical-align: top;
        background: #f5f5f58c;
        /*padding: 15px;*/
        margin: 0 0 1.5em;
        width: 100%;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        -webkit-box-sizing: border-box;
        -webkit-box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.18);
        box-shadow: 0px 1px 1px 0px rgba(0, 0, 0, 0.18);
        border-radius: 3px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        float: right;
    }

    .masonry-item:hover {
        background: #ccc;
    }


    .landing_page_strip_search {
        width: 100%;
    }

    .landing_page_strip_search li {
        display: inline-block;
    }

    .landing_page_strip_search ul {
        width: 100%;
        margin: 0px;
    }

    .landing_page_strip_search ul li {
        width: 29.6%;
        margin: 0px;
    }

    .landing_page_strip_search ul li:last-child {
        width: 10%;
    }

    .landing_page_strip_search ul li input {
        padding: 9px !important;
        margin: 0px;
        width: 100%;
        outline: none;
        border: 1px solid #255a6ff7;
    }

    .landing_page_strip_search ul li input[type="submit"] {
        background: #255a6ff7;
        color: #fff;
    }

    .rent-desired-item {
        background: #fff;
    }

    .landing_page_recomended div {
        padding: 10px;
    }

    .landing_page_recomended div div p {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }

    #view_items {
        background: #fff;
        padding: 0px;
    }

    /*register client account css*/
    .register-client-account {
        margin: 20px 0px;
        background: #ffffff;
        padding: 20px;
        border-radius: 10px;
        -webkit-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2);
        box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.2);
    }

    .register-client-account input {
        border: none !important;
        border-radius: 0px !important;
        border-bottom: 1px solid #ccc !important;
        outline: none !important;
    }

    #view_items .col-sm-2 ul {
        padding: 10px;
    }

    .side_padding {
        padding: 0px 6%;
    }

    /*.side_padding .row{padding:30px;}*/
    .build_form_form_group {
        margin: 10px 20px 15px 0px !important;
    }

    .build_form_form_group input::-webkit-input-placeholder {
        font-size: 12px;
        color: #1984ab;
    }

    .build_form_form_group input:-ms-input-placeholder {
        font-size: 12px;
        color: #1984ab;
    }

    .build_form_form_group input::-ms-input-placeholder {
        font-size: 12px;
        color: #1984ab;
    }

    .build_form_form_group input::placeholder {
        font-size: 12px;
        color: #1984ab;
    }

    .tab-content {
        padding: 10px !important;
        background: #fff;
    }

    .landing_page_slider .row {
        padding: 10px;
        background: #fff;
        margin: 10px 0px;
    }

    .landing_page_slider .row .col-sm-3 {
        padding-right: 10px;
    }

    .landing_page_slider .row .col-sm-3 ul .list-group-item, .landing_page_slider .row .col-sm-3 .list-group-item {
        border: 0px !important;
        border-bottom: 1px solid #ccc !important;
        border-radius: 0px;
    }

    .landing_page_slider .row .col-sm-7 {
        height: inherit;
        background: #ccc;
        background: url("<?=base_url().'resources/images/car_hire_backround.jpg'?>");
        background-size: cover;
        background-repeat: no-repeat;
        text-align: center;
        color: #ffffff;
        min-height: 400px;
    }

    .landing_page_slider .quick-custom-features {
        padding-left: 10px;
    }

    .landing_page_slider .quick-custom-features .list-group .list-group-item {
        margin-bottom: 10px;
    }

    .landing_page_slider .row .col-sm-12 div {
        position: absolute;
        top: 40%;
        left: 10%;
        height: inherit;
        -webkit-transform: translateY(-50%);
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
        height: inherit;
    }

    /*lib table*/
    .lib_table tr td {
        font-size: 13px;
    }


    .landing_page_image_background {
        background: url("<?=base_url().'resources/images/wall1.jpg'?>");
        height: 600px;
        background-size: cover;
        text-align: center;
        background-repeat: no-repeat;
        width: 100vw;
        color: #fff;
        display: table-cell;
        vertical-align: middle;
    }

    .landing_page_display_categories {
        padding-top: 00px;
        padding-bottom: 30px;
    }

    .display_categories ul a li {
        padding: 10px 15px 10px 0px;
        text-decoration: none;
    }

    .display_categories div {
        border: 1px solid #CCCCCCb2;
    }

    /*display categories page*/
    .display_items_col_12 {
        padding: 30px !important;
        margin: 20px 0px !important;
    }

    .display_items_col_12 .display_category_results {
        padding: 30px !important;
    }

    .display_items_col_12 .search_column {
        padding: 10px;
    }

    .display_items_col_12 .search_column b {
        font-size: 13pt;
    }

    .display_items_col_12 .search_column ul li {
        padding: 4px 0px;
    }

    .breadcrumb_custom {
        background: #fff;
        padding-bottom: 10px;
        padding-top: 10px;
        color:  #000;
        font-weight: 800;
    }

    .view_categories_search_items input[type='text'], .view_categories_search_items input[type='number'] {
        border: none;
        border-radius: 0px;
        border-bottom: 1px solid #CCCCCC;
        outline: none !important;
    }

    .landing_categories_search_items {
        text-align: left;
        padding: 0px;
        background: rgba(0, 0, 0, 0.4);
        border: none;
        border-radius: 0px;
        border-bottom: 1px solid #CCCCCC;
        outline: none !important;
    }

    .view_categories .col-sm-10 div {
        margin-left: 10px;
    }

    .display_item_details_page .col-sm-5 {
        padding: 0px 20px;
    }

    .display_item_details_page .carousel-item {
        text-align: center;
    }

    .display_item_details_page img {
        height: 7vh;
    }

    .navbar-brand, .nnavbar-toggler-icon {
        color: #222 !important;
    }

    .dropdown-menu {
        background: #fff !important;
        font-size: 13px;
        padding: 5px;
    }

    .dropdown-menu a {
        padding: 3px !important;
        border-bottom: 1px solid #ccc;
    }

    .dropdown-menu a li {
        color: #000 !important;
        font-size: 13px;
        padding: 5px !important;
    }

    .dropdown-menu a:hover {
        background: inherit !important;
        color: inherit !important;
        font-weight: 600;
    }

    .comment_input {
        padding: 5px;
        outline: none;
        width: 100%;
        border: 1px solid #ccc;
        margin: 10px 0px;
    }

    .banner-title {
        font-size: 20px;
        line-height: 1;
        color: #212121;
        font-weight: 600;
        position: relative;
        margin-bottom: 1px;
    }

    .price_display {
        background: #1984ab;
        text-align: center;
        font-size: 16px;
        width: auto;
        line-height: 20px;
        color: #fff;
        padding: 6px 5px 5px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    #order, #location, #features, #ItemDescription {
        padding: 20px 20px 20px 0px !important;
    }

    .order_item_input input, .order_item_input textarea {
        padding: 15px;
        width: 100%;
        margin-top: 10px;
    }

    .order_item_input input[type="checkbox"] {
        padding: 0px;
        width: auto;
        margin-top: 10px;
    }

    .myorders_table tr:first-child td {
        font-size: 13px !important;
        background: #f9f9f9;
        font-weight: bold;
    }

    .myorders_table tr td {
        font-size: 12px !important;
    }

    .reccomemded_for_you .row .col-sm-2 ul {
        padding: 0px 10px;
    }

    .reccomemded_for_you .row .col-sm-2 ul li {
        padding: 5px 0px;
    }

    .header_simple_link li {
        display: inline-block;
        padding: 5px 20px;
    }


    .clientregistration {
        /*height: 100%; font-family: 'Montserrat', sans-serif;*/
        display: -ms-flexbox;
        display: -webkit-box;
        display: flex;
        min-width: 200px;
        -ms-flex-align: center;
        -ms-flex-pack: center;
        -webkit-box-align: center;
        align-items: center;
        /*-webkit-box-pack: center;*/
        -webkit-box-pack: center;
        justify-content: center;
        padding-top: 40px;
        padding-bottom: 40px;
        /*background-color: #f5mf5f5;*/
    }

    .nav-item:hover {
        color: #dc134c;
    }

    .nav-link {
        font-weight: 600 !important;
    }


    .callaction-area {
        background-image: url("<?=base_url()?>resources/images/wall2.png");
        background-size: cover;
        text-align: center;
        color: #fff;
        padding: 120px 0;
        background-color: rgba(4, 9, 30, 0.75);
    }


    .text-white {
        color: #fff !important;
    }

    .callaction-btn {
        background-color: #fab700;
        color: #fff;
        border: 1px solid transparent;
        padding: 10px 40px;
        font-size: 14px;
        font-weight: 600;
        -webkit-transition: all 0.3s ease 0s;
        -o-transition: all 0.3s ease 0s;
        transition: all 0.3s ease 0s;
    }

    .text-uppercase {
        text-transform: uppercase !important;
    }

    .cut_line_description {
        max-height: 4.5em;
        line-height: 1.5em;
        display: block; /* or inline-block */
        -o-text-overflow: ellipsis;
        text-overflow: ellipsis;
        word-wrap: break-word;
        overflow: hidden;
    }


    .faq_introductory_section .row .col-sm-3 div {
        padding: 20px;
        border: 1px solid #ccc;
        text-align: center;
        margin: 20px 20px 20px 0px;
    }

    .faq_introductory_section .row .col-sm-3:last-child div {
        margin-right: 0px;
    }

    .accordion .card-header:after {
        font-family: 'FontAwesome';
        content: "\f068";
        float: right;
    }

    .accordion .card-header.collapsed:after {
        content: "\f067";
    }

    .dropdown-item li {
        color: #000 !important;
    }

    .display_categories_index_page {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    .display_categories_index_page .row, .display_categories_index_page .categories-masonry {
        background: inherit;
        padding: 10px;
    }

    .display_categories_index_page .row .col-sm-3 div {
        padding: 20px;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        min-height: 170px;
    }

    .display_categories_index_page .row .col-sm-3:nth-child(1) div {
        border-top: 1px solid #ccc;
        border-left: 1px solid #ccc;
    }

    .display_categories_index_page .row .col-sm-3:nth-child(2) div {
        border-top: 1px solid #ccc;
    }

    .display_categories_index_page .row .col-sm-3:nth-child(3) div {
        border-top: 1px solid #ccc;
    }

    .display_categories_index_page .row .col-sm-3:nth-child(4) div {
        border-top: 1px solid #ccc;
    }

    .display_categories_index_page .row .col-sm-3:nth-child(5) div {
        border-left: 1px solid #ccc;
    }


    .landing_page_search_table {
        background: #f5f5f5;
        text-align: center;
    }

    #carouselExampleControls {
        height: auto;
        max-height: 90%;
        overflow-y: hidden;
    }

    .class_item_details_display {
        padding-top: 20px;
        padding-bottom: 20px;
        height: 70%;
        max-height: 70%;
        overflow-y: hidden;
    }

    .pg-view_item_details_slider {
        background: #333;
        height: inherit !important;
        text-align: center;
    }

    .class_item_details_display .pg-view_item_details_slider img {
        height: 100%;
        width: auto;
    }


    .class_item_details_display_info div {
        padding: 15px;
        border: 1px solid #eeeeee;
    }

    .class_item_details_display_info div table tr td {
        font-weight: normal;
        font-size: 13px;
    }

    .terms_and_conditions h5 {
        margin-top: 20px;
    }

    .no_results {
        color:red;
        padding: auto;
    }

    .terms_and_conditions {
        padding-top: 20px;
        padding-bottom: 40px;
    }

    .search-placement-header {
        border: 2px solid #2e0f35;
        background: #fff;
        border-radius: 15px !important;
        padding: 1px 10px;
        width: 45%;
    }

    .search-placement-header tr td select, .search-placement-header tr td input {
        font-size: 12px;
        border: 1px solid #ffffff;
        outline: none;
        padding: 4px;
        background: #ffffff;
    }

    /*icon*/
    .navbar-brand-icon-pangiasa {
        height: 50px;
    }


    @media only screen and (max-width: 768px) {
        .container-fluid, .row, .col-sm-3, .col-sm-5, col-sm-7, .col-sm-12, .col-sm-10, .col-sm-2, .col-sm-6, .col-sm-8, .col-sm-4, .row, .container, .container, .col-sm-9 {
            padding: 0px;
            margin: 0px;
        }

        .navbar-brand-icon-pangiasa {
            height: 30px;
        }

        /*the view items details page*/
        .view_item_details_page_display_related_image {
            height: 50vh !important;
            min-height: 50vh !important;
            max-height: 50vh !important;
            overflow-y: hidden;
            background: #222;
            text-align: center;
            overflow: hidden;
        }

        .rent-desired-item .carousel-item .container-fluid .row div {
            padding: 10px;
        }

        #carouselExampleControls {
            height: auto;
        }

        .pg-landing-page-rent-desired {
            height: 60%;
            min-height: 60%;
            overflow-y: visible;
        }

        .side_padding {
            padding: 10px;
        }

        .search-placement-header .inputSearchItemCategory {
            width: 100% !important;
        }

        .search-placement-header {
            border: 1px solid #2e0f35;
            background: #fff;
            border-radius: 8px !important;
            padding: 1px 10px;
            width: 70%;
        }

        .landing_page_slider .row .col-sm-9 div {
            position: relative;
            vertical-align: middle;
            top: 70%;
            left: 0%;
            height: inherit;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            min-height: 300px !important;
        }

        .navbar-toggler {
            background: #f5fafd !important;
            padding: 10px;
            color: #1d2124;
        }

        .navbar-toggler .icon-bar {
            color: red !important;
        }

        .header_simple_link, .landing_page_slider .row .col-sm-3 {
            display: none;
        }

        .search_response ul li {
            display: block;
        }

        .landing_page_display_categories img {
            width: 100%;
            height: auto !important;
        }

        .header_menu div ul li a {
            padding: 10px 15px 10px 10px;
            display: inline-block;
            color: #333 !important;
        }

        .header_menu_display_login_links {
            float: left;
        }

        #carouselExampleControls {
            height: auto;
            overflow-y: hidden;
        }

        .masonry, .category-page-masonry, .categories-masonry {
            -webkit-column-count: 2;
            column-count: 2;
        }

        .masonry-item {
            flex: auto;
            height: auto;
            min-width: 40vw;
            max-width: 40vw;
            margin: 0 8px 8px 0; /* Some gutter */
        }

        .categories-masonry-item {
        }

        .footer-area {
            background: #fff;
        }

        .single-footer-widget ul li {
            color: #333333 !important;
        }

        .display_items_col_12 {
            padding: 0px !important;
            margin: 0px 0px !important;
        }
    }

    /*tablets*/
    @media only screen and (min-width: 768px) {
        .masonry, .category-page-masonry {
            -webkit-column-count: 3;
            column-count: 3;
        }
    }

    /*medium devices*/
    @media only screen and (min-width: 992px) {
        .masonry, .category-page-masonry {
            -webkit-column-count: 4;
            column-count: 4;
        }
    }

    /*large screens*/
    @media only screen and (min-width: 1200px) {
        .masonry {
            -webkit-column-count: 5;
            column-count: 5;
        }
    }
</style>

<body>
<div>
    <!--main menu container-->
    <div class="main-menu-container">
        <nav class="navbar navbar-expand-lg  header_menu side_padding">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <img src="<?= base_url() ?>resources/images/toggle-menu-icon.png" width="15px"/>
            </button>

            <a class="navbar-brand" href="<?= base_url() ?>Index/">
                <img src="<?= base_url() ?>resources/images/icon.png" class="navbar-brand-icon-pangiasa"/>
            </a>

            <div class="search-placement-header">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="10%">
                            <select class="inputSearchItemCategory">
                                <option value="">ALL</option>
                                <?php
                                $cats = $this->db->get("categories")->result_array();

                                foreach ($cats as $item) {
                                    echo '<option class="dropdown-item" value="' . $item['id'] . '">' . $item['name'] . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td width="90%">
                            <input type="search" style="width: 100%;" class="inputSearchItem"
                                   placeholder="Ad no, item name"/>
                        </td>
                    </tr>
                </table>
            </div> &nbsp;&nbsp;&nbsp;

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                if (isset($this->session->user)) {
                    $dashboard_link = null;
                    $user_type = $this->session->user['user_type'];

                    if ($user_type == "vendor") {
                        $dashboard_link = (base_url() . "AppClient");
                    } elseif ($user_type == "sys") {
                        $dashboard_link = (base_url() . "AppAdmin");
                    } elseif ($user_type == "client") {
                        $dashboard_link = (base_url() . "Customer/Orders");
                    }

                    ?>
                    <ul class="nav navbar-nav navbar-right header_menu_display_login_links">
                        <li class="nav-item">
                            <i class="fa fa-user-circle"></i>
                            <a class="nav-link" href="<?= $dashboard_link ?>">  <?= $this->session->user['name'] ?></a>&nbsp;&nbsp;
                        </li>

                        <li class="nav-item">
                            <i class="fa fa-dashboard" style="color: #fff;"></i>
                            <a class="nav-link" href="<?= $dashboard_link ?>">DASHBOARD</a> &nbsp;&nbsp;
                        </li>


                        <li class="nav-item">
                            <i class="fa fa-power-off"></i>
                            <a class="nav-link" href="<?= base_url() ?>Index/logout">LOGOUT</a>
                        </li>
                    </ul>
                    <?php
                }
                ?>

                <?php if (!$this->session->user['id']) { ?>
                    <div class="nav navbar-right">
                        <ul class="navbar-nav mr-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="<?= base_url() ?>Index/login"><i class="fa fa-sign-in"></i>
                                    &nbsp;LOGIN</a>
                                &nbsp;&nbsp;
                            </li>


                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-user"></i>&nbsp;REGISTER
                                </a>

                                <div class="dropdown-menu register_dropdown" aria-labelledby="navbarDropdown">
                                    <ul>
                                        <a class="nav-link" href="<?= base_url() ?>Index/businessregistration/add">
                                            <li>VENDOR</li>
                                        </a>

                                        <a class="nav-link" href="<?= base_url() ?>Index/clientregistration/add">
                                            <li> CLIENT</li>
                                        </a>
                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link" href="<?= base_url() ?>Index/login"><i class="fa fa-dashboard"></i>&nbsp;DASHBOARD</a>
                                &nbsp;&nbsp;
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link" href="<?= base_url() ?>Index/login"><i
                                            class="fa fa-shopping-cart"></i>&nbsp;CART</a>
                                &nbsp;&nbsp;
                            </li>

                        </ul>
                    </div>
                <?php } ?>

            </div>

        </nav>

        <nav class="navbar navbar-expand-lg navbar-light bg-light light_header_menu side_padding"
             style="color:#fff !important; background: #525C65 !important;">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="<?= base_url() ?>Index/">HOME <span
                                    class="sr-only">(current)</span></a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            CATEGORIES
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <ul>
                                <?php
                                $cats = $this->db->get("categories")->result_array();

                                foreach ($cats as $item) {
                                    echo '<a class="dropdown-item" href="' . base_url() . 'Index/category/' . $item['id'] . '"><li>' . $item['name'] . '</li></a>';
                                }
                                ?>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/#latestItems">LATEST ITEMS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/how">HOW IT WORKS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/why">WHY PANGISA</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/why">OUR OFFICES & CONTACTS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/faq">FAQ</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>Index/terms_and_conditions">TERMS & CONDITIONS</a>
                    </li>

                </ul>


            </div>
        </nav>

    </div>

    <!--search response-->
    <div class="container-fluid llside_padding landing_page_search_table">
        <div class="row">
            <span class="loading_view"></span>
        </div>

        <span class="search_response"></span>
    </div>

    <?= ($error <> null ? "<div class='error_message side_padding'><p class='alert alert-danger'>" . $error . "</p></div>" : null); ?>

    <div>
        <?php ($view ? $this->load->view($view,$data) : null) ?>
    </div>
</div>
<!--</body>-->

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

    $('.datepicker').datepicker(datePickerOptions)
</script>

<script>
    inAdvance = 300;
    // $(this).attr("src", "images/card-front.jpg");
    document.onreadystatechange = function (ev) {
        if (document.readyState === "complete") {
            window.onscroll = function () {
                var images = document.getElementsByClassName('lazy_loading_img'); //$('.lazy_loading_img');
                for (var i = 0; i <= images.length; i++) {
                    var image = images[i];

                    if (image !== undefined && typeof image !== "undefined" && image !== null) {
                        var original_source = image.src.toLocaleString();
                        var actual_source = image.dataset.src;
                        var in_advance_distance = window.innerHeight + window.pageYOffset + inAdvance;

                        if (image.offsetTop < in_advance_distance) {
                            if (original_source.localeCompare(actual_source) !== 0) {
                                image.src = image.dataset.src
                            }
                        }
                    }
                }
            }
        }
    }


</script>

<script>
    $(".inputSearchItem").keyup(function () {
        var item_name = $(this).val();
        var item_cat = $(".inputSearchItemCategory").val();

        if (item_name == null || item_name.length < 3) {
            $(".search_response").html("");
            return;
        }

        $(".loading_view").html("Loading, please wait");

        $.ajax(
            {
                type: 'POST',
                url: '<?=base_url()?>/Index/AjaxSearchItemByName/',
                data: {
                    "item_name": item_name,
                    "min_price": null,
                    "max_price": null,
                    "item_category": item_cat ? item_cat : null
                },
                success: function (response) {
                    $(".search_response").html(response);
                    $(".loading_view").html("");
                }
            }
        );

    });
</script>
