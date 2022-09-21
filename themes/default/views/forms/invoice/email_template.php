<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    

    <style type="text/css">

.flex-container{
     display: flex; 
     flex-direction: row; 
     justify-content: space-around; 
}
.invoice-header{ 
     background-color:#efc100; 
     color: #ffffff;
     height: 90px; 
}

.invoice-title {
		/*display: inline;
		position: relative;*/
		font-size: 40px;
		top: 9px;
}

.invoice-sender-name, .invoice-sender-info {
		font-size: 14px;
		padding: 15px;

}
.FSX-small {
       font-size: x-small;
       color: #aebbce;
       padding-bottom: 10px;
}
.amountNumber{
       font-size: 10.1mm;
       color: #efc100;
}

#invoice{
    color: black !important;
}

.invoice-table {
    	/*width: 100%;*/
    	border-top: 2px solid #efc100; 
       border-collapse: collapse;
} 
.invoice-table th, .invoiceTableColor{
    color: #efc100;
}


</style>

</head>
<body>
<?php if(isset($invoice))
                            { ?>
                                <div id="invoice" style="width: 600px;">
                                    <div id="invoice-header" style="" class="flex-container invoice-header">
                                            <div class= "invoice-title" style="width: 60%; font-size: 40px; padding-top: 20px; padding-left: 40px;">INVOICE</div>
                                                <div class="invoice-sender-info" style="font-size: 14px; padding: 15px;">
                                                        <span><?php echo $invoice->eName ?></span> <br>
                                                        <span><?php echo $invoice->eEmail ?></span> <br>
                                                        <span><?php echo $invoice->ePhone ?></span>    
                                                </div>
                                                <div class= "invoice-sender-name" style="font-size: 14px; padding: 15px;">
                                                        <span><?php echo $invoice->eAddress ?></span>  <br>          
                                                        <span><?php echo $invoice->eCity ?>,&nbsp;<?php echo $invoice->eState ?></span> <br>
                                                        <span><?php echo $invoice->eZip ?></span> 
                                                </div> 
                                        </div>
                                            <!--<div id="invoiceDetails2">-->
                                                <div id="billedTo2" class="flex-container" style="padding-top: 30px; padding-bottom: 30px;">
                                                            <div id="billedTo-info2" style="width: 100%">
                                                                
                                                                <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding-bottom: 10px;">Billed To</div>
                                                                        <span>
                                                                        <?php echo $invoice->name ?><br>
                                                                        <?php echo $invoice->address_line_1 . " " . $invoice->address_line_2 ?><br>
                                                                        <?php echo $invoice->cs ?><br>
                                                                        <?php echo $invoice->zip ?><br></span>
                                                            </div>
                                                            <div id="invoice-info" style="width: 100%">
                                                                    <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding: 15px;">Statement Date</div>
                                                                    <span><?php echo date("m/d/Y")?></span><br><br><br>
                                                                    <!-- <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding: 15px;">Date Of Issue</div>
                                                                    <span>< ?php echo date("Y-m-d")?></span> -->
                                                            </div>
                                                            <div></div>
                                                            <div id="amountTotal2" style="width: 100%">
                                                                <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding: 15px; text-align: right">Invoice Total</div>
                                                                <div class="amountNumber" style="font-size: 10.1mm; color: #efc100; text-align: right">$ <?php echo number_format($invoice->Tbalance, 2) ?></div>
                                                            </div>
                                                </div>
                                                <div class="invoice-table" style="border-top: 2px solid #efc100; border-collapse: collapse;">
                                                            <table style="width: 100%">
                                                            <tr>
                                                                    <th style="color: #efc100;">Description</th>
                                                                    <th style="color: #efc100;"></th>
                                                                    <th style="color: #efc100;"></th>
                                                                    <th style="color: #efc100; margin-left:35%;">Date</th>
                                                                    <th style="color: #efc100; margin-left:50%;">Type</th>
                                                                    <th style="color: #efc100; margin-left:75%;">Amount</th>
                                                                    <th style="color: #efc100; margin-left:85%;">Balance</th>
                                                                </tr>
                                                                <?php if(isset($invoice->data))
                                                                $invoiceData = $invoice->data;?>
                                                                    <tr>
                                                                        <td style="color: #efc100; font-weight: bold;">Beginning Balance:</td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td>$<?= isset($invoiceData->beginningBalance) ? number_format($invoiceData->beginningBalance , 2): '0.00' ?></td>
                                                                    </tr> 
                                                                <?php foreach($invoiceData->trans as $trans){?>
                                                                    <tr>
                                                                        <td style="max-width: 150px"><?php echo $trans["description"] ?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td style="margin-left:35%;"><?php echo date("m/d/Y", strtotime($trans["transaction_date"])) ?></td>
                                                                        <td style="margin-left:50%;"><?php echo $trans["type"] ?></td>
                                                                        <td style="margin-left:75%;">$<?= isset($trans["amount"]) ? number_format($trans["amount"] , 2): '0.00' ?> </td>
                                                                        <td style="margin-left:85%;">$<?= isset($trans["balance"]) ? number_format($trans["balance"] , 2): '0.00' ?></td>
                                                                    </tr> 
                                                                <?php }?>                                                      
                                                            </table>
                                                        </div>
                                                    <div id="totalBottom" style="margin-left:85%; padding-top: 30px; padding-bottom: 30px;">
                                                        <div><span class="invoiceTableColor" style="color: #efc100;">Total</span>&nbsp; &nbsp;  <span>$<?= isset($invoice->Tbalance) ? number_format($invoice->Tbalance , 2): '0.00' ?></span></div>
                                                    </div>
                                        <!--  </div>-->
                                    
                                </div>
                                <pagebreak />
                           <?php }?>
</body>
</html>





