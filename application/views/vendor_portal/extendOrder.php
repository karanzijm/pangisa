<?php
    $helper=new Ballistics();
?>

<h5>Order Extension Requests</h5>
<table class="table" style='width: 100% !important;'>
    <tr>
        <td>Order Number</td>
        <td>Days</td>
        <td>Extension</td>
        <td>Price</td>
        <td>Item</td>
        <td>Reason</td>
        <td>Start Date</td>
        <td>End Date</td>
        <td>Status</td>
        <td>Status Reason</td>
        <td>Date</td>
        <td>Action</td>
    </tr>

    <?php
        foreach ($data['extendedOrders'] as $extendedOrder):
            echo "<tr>
                <td>".$extendedOrder['order_number']."</td>
                <td>".$extendedOrder['number_of_days']."</td>
                <td>".$extendedOrder['ext_number_of_days']."</td>
                <td>".number_format($extendedOrder['price'])."</td>
                <td>".$extendedOrder['name']."</td>
                <td>".$extendedOrder['reason']."</td>
                <td>".$extendedOrder['start_date']."</td>
                <td>".$extendedOrder['stop_date']."</td>
                <td>".$helper->translateStatusCodes($extendedOrder['approved'])."</td>
                <td>".$extendedOrder['status_reason']."</td>
                <td>".$extendedOrder['date']."</td>
                <td>";
            if ($extendedOrder['approved'] == 2000):
                    echo"<a href='".base_url()."AppClient/ExtendItemOrderRequest/reject/".$extendedOrder['id']."'> <button class='btn btn-sm btn-outline-danger'><i class='fa fa-remove'></i> </button></a> 
                    <a href='".base_url()."AppClient/ExtendItemOrderRequest/accept/".$extendedOrder['id']."'> <button class='btn btn-sm btn-outline-success'><i class='fa fa-check'></i> </button></a> ";
                endif;
                    echo"</td>
            </tr>";
        endforeach;
    ?>
</table>

<?= $pagination ?>
