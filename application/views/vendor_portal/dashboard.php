<div class="container-fluid">
    <div class="row">
        <div class=" col-sm-3">
            <div class="col_content">
                <div class="row">
                    <div class="col-sm-4"><i class="fa fa-first-order"></i> </div>
                    <div class="col-sm-8">
                        <b>ORDERS</b>
                        <h2><?= $data['total_orders'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-sm-3">
            <div class="col_content">
                <div class="row">
                    <div class="col-sm-4"><i class="fa fa-first-order"></i> </div>
                    <div class="col-sm-8">
                        <b>ITEMS</b>
                        <h2><?= $data['total_items'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<div class="container-fluid">
    <div class="row">
        <p style="color: #1984ab; font-weight: bold; padding:10px;">
            PERFORMANCE SUMMARY - PARTNERS - ORDERS - ITEMS <?=date("Y")?> <i class="fa  fa-arrow-down "></i>
        </p>
        <div class="col-sm-12 app_client_graph_div" id="graph"></div>

        <?php
        $info=[];

        for($i=1; $i<=12; $i++){
            $graph_orders=$this->db->query("select count(*)  as total from orders where owner='".$this->session->user['id']."' and  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $graph_items=$this->db->query("select count(*)  as total from items where  added_by='".$this->session->user['id']."' and  year(date_added)=year(now()) and MONTH(date_added)=$i")->row()->total;

            array_push($info,
                [
                    'month'=>date('Y').'-'.$i,
                    'orders'=>$graph_orders,
                    'items'=>$graph_items,
                ]
            );

        }


        ?>
        <script>
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            Morris.Line({
                element: 'graph',
                data: <?= json_encode( $info); ?>,
                xkey: 'month',
                ykeys: ['orders','items'],
                xLabels:['MONTH'],
                xLabelFormat: function (x) { return months[x.getMonth()]; },
                labels:['Orders',"Items for Rent"],
                pointSize: 2,
                hideHover: 'false',
                resize: true,
                fillOpacity: 0.4,
                pointFillColors:['#fff'],
                pointStrokeColors: ['#dc134c'],
                lineColors:['#1489ab','#dc134c', '#FF0000',"#1d1d1d"],
                grid:'false'

            });
        </script>
    </div>



