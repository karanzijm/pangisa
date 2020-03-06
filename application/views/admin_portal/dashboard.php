<div class="container-fluid">
    <div class="row">
        <div class=" col-sm-3">
            <div class="col_content">
                <div class="row">
                    <div class="col-sm-4"><i class="fa fa-first-order"></i></div>
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
                    <div class="col-sm-4"><i class="fa fa-users"></i></div>
                    <div class="col-sm-8">
                        <b>PARTNERS</b>
                        <h2><?= $data['companies'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-sm-3">
            <div class="col_content">
                <div class="row">
                    <div class="col-sm-4"><i class="fa fa-user-o"></i></div>
                    <div class="col-sm-8">
                        <b>CLIENTS</b>
                        <h2><?= $data['clients'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-sm-3">
            <div class="col_content">
                <div class="row">
                    <div class="col-sm-4"><i class="fa fa-user-circle-o"></i></div>
                    <div class="col-sm-8">
                        <b>USERS</b>
                        <h2><?= $data['users'] ?></h2>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>


<div class="container-fluid">
    <div class="row">
        <p style="color: #1984ab; font-weight: bold; padding:10px;">
            PERFORMANCE SUMMARY - PARTNERS - ORDERS - ITEMS <?= date("Y") ?> <i class="fa  fa-arrow-down "></i>
        </p>
        <div class="col-sm-12 app_client_graph_div" id="graph"></div>

        <?php
        $info = [];

        for ($i = 1; $i <= 12; $i++) {
            $graph_orders = $this->db->query("select count(*)  as total from orders where  year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $companies = $this->db->query("select count(*)  as total from users where registration_type=5 and status=1 and year(date)=year(now()) and MONTH(date)=$i")->row()->total;
            $graph_items = $this->db->query("select count(*)  as total from items where status=1 and year(date_added)=year(now()) and MONTH(date_added)=$i")->row()->total;

            array_push($info,
                [
                    'month' => date('Y') . '-' . $i,
                    'orders' => $graph_orders,
                    'partners' => $companies,
                    'items' => $graph_items,
                ]
            );
        }

        ?>
        <script>
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            Morris.Line({
                element: 'graph',
                data: <?= json_encode($info); ?>,
                xkey: 'month',
                ykeys: ['orders', 'partners', 'items'],
                xLabels: ['MONTH'],
                xLabelFormat: function (x) {
                    return months[x.getMonth()];
                },
                labels: ['Orders', 'Partners', "Items for Rent"],
                pointSize: 2,
                hideHover: 'false',
                resize: true,
                fillOpacity: 0.4,
                pointFillColors: ['#fff'],
                pointStrokeColors: ['#dc134c'],
                lineColors: ['#1489ab', '#dc134c', '#FF0000', "#1d1d1d"],
                grid: 'false'

            });
        </script>
    </div>

    <div class="row">
        <div class="col-sm-6 dashboard_2_panels">
            <div>
                <p>
                <h3><i class="fa fa-th-list"></i> &nbsp;&nbsp;&nbsp; Items</h3></p>

                <?php
                $items = $this->db
                    ->select("i.name as name,(select name from users where id=i.added_by) as owner, ca.name as  category,date(i.date_added) as date")
                    ->from("items i")
                    ->join("categories ca", "ca.id=i.category")
                    ->limit(10, 0)
                    ->order_by("i.date_added", "desc")
                    ->get()
                    ->result_array();

                echo "<table class='table'>
                           <tr>
                               <td>Name</td>
                               <td>Owner</td>
                               <td>Category</td>
                               <td>Date</td>
                           </tr>
                       ";
                foreach ($items as $item):
                    echo "<tr>
                               <td>" . $item['name'] . "</td>
                               <td>" . $item['owner'] . "</td>
                               <td>" . $item['category'] . "</td>
                               <td>" . $item['date'] . "</td>
                           </tr>";
                endforeach;
                echo "</table>";

                ?>
            </div>
        </div>

        <div class="col-sm-6 dashboard_2_panels">
            <div>
                <p>
                <h3><i class="fa fa-th-list"></i> &nbsp;&nbsp;&nbsp;Orders</h3></p>
                <?php
                $sampleorders = $this->db
                    ->select("i.name as item, u.name as name, o.place_of_use, date(o.date) as date")
                    ->from("orders o")
                    ->join("items i", "i.id=o.item")
                    ->join("users u", "u.id=o.client")
                    ->limit(10, 0)
                    ->get()
                    ->result_array();

                echo "<table class='table table-hover'>
                       <tr>
                           <td>Client</td>
                           <td>Item</td>
                           <td>Place of use</td>
                           <td>Date</td>
                       </tr>
                       ";

                foreach ($sampleorders as $order):
                    echo "<tr>
                               <td>" . $order['name'] . "</td>
                               <td>" . $order['item'] . "</td>
                               <td>" . $order['place_of_use'] . "</td>
                               <td>" . $order['date'] . "</td>
                           </tr>";
                endforeach;
                echo "</table>";

                ?>
            </div>
        </div>
    </div>
</div>