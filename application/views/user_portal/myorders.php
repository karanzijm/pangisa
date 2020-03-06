<div class="side_padding">

    <table class="table table-hover  myorders_table" width="100%">
    <tr>
        <td>ORDER NUMBER</td>
        <td>Item</td>
        <td>Price</td>
        <td>Registration</td>
        <td>Place</td>
        <td>Period</td>
        <td>Date</td>
        <td>Status</td>
        <td>Reason</td>
        <td>Action</td>
    </tr>
    <?php
    $i=1;
        foreach ($orders as $order):
            echo "
                <tr>
                    <td> <a href='".base_url()."Index/items/view/".$order['item_id']."'>".$order['order_number']."</a></td>
                    <td> <a href='".base_url()."Index/items/view/".$order['item_id']."'>".$order['name']."</a></td>
                    <td>".number_format($order['price'])." ".$order['price_point'] ."</td>
                    <td>".$order['identification_number']."</td>
                    <td>".$order['place_of_use']."</td>
                    <td>".$order['number_of_days']."</td>
                    <td>".explode(" ",$order['date'],2)[0]."</td>
                    <td>".($order['approved']==0?"<i class='fa fa-hourglass '></i>":($order['approved']==1?"Approved":"Rejected"))."</td>
                    <td>".$order['comment']."</td>
                    <td>".($order['approved']==0 && $order['user_cancel']!=1?"<a href='".base_url().'Index/myorders/cancel/'.$order['id']."' alter='Cancel Order' ><i class='fa fa-trash-o'></i> </a>":null)." </td>
                     
                </tr>
            ";
        $i++;
        endforeach;
    ?>
    </table>
</div>
