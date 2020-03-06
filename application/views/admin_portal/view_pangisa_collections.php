<!--<h2>Collections </h2>-->

<div class="container-fluid">
    <div class="row">
        <?php echo form_open_multipart(base_url()."AppAdmin/payments_to_pangisa/"); ?>
        <SPAN><b>Collections</b></SPAN> &nbsp;&nbsp;&nbsp;
        <span class="filter_search_item">
                <input type="text"  name="reference_number" placeholder="Reference number"/>
<!--                <input type="text"  name="vendor_name" placeholder="Vendor name"/>-->

                Status <select name="status">
                    <option value=""></option>
                    <option value="1">Cleared</option>
                    <option value="0">Un Cleared</option>
                </select> &nbsp;&nbsp;

                start date <input type="date"  class="date" name="startdate" />
                end date <input type="date" class="date" name="enddate" />
            </span> &nbsp;
        <button type="submit" class="btn btn-sm btn-success" ><i class="fa fa-search" aria-hidden="true"></i> </button>
        </form>
    </div>


    <div class="row">

        <table class="table">
            <tr>
                <td>#</td>
                <td>Reference Number</td>
                <td>Vendor</td>
                <td>Amount</td>
                <td>Reason</td>
                <td>Order Amount</td>
                <td>Balance</td>
                <td>Net Amount</td>
                <td>Paid</td>
                <td>Date</td>
                <td>Date Paid</td>
            </tr>

            <?php $i = 1;
            $total = 0;
            $balance = 0;
            foreach ($data as $datum): ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $datum['reference_number'] ?></td>
                    <td><?= $datum['vendor'] ?></td>
                    <td><?= number_format($datum['amount']) ?></td>
                    <td><?= $datum['reason'] ?></td>
                    <td><?= $datum['order_amount'] ?></td>
                    <td><?= number_format($datum['balance']) ?></td>
                    <td><?= $datum['net_amount'] ?></td>
                    <td><?= $datum['paid'] == 0 ? "PENDING" : "PAID" ?></td>
                    <td><?= $datum['date'] ?></td>
                    <td><?= $datum['last_transaction_date'] ?></td>
                </tr>
                <?php
                $total += $datum['amount'];
                $balance += $datum['balance'];
                $i++;
            endforeach;
            ?>

            <tr>
                <td colspan="3" align="right">Total Amount : <?= number_format($total) ?></td>
                <td colspan="3" align="right"> Balance : <?= number_format($total) ?></td>
            </tr>

        </table>
    </div>
</div>
