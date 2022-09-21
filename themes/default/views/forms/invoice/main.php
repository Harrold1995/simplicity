<div class="modal fade invoice-modal" id="invoice" tabindex="-1" role="dialog"  type="invoice" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width: 800px;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="invoice-entry" type=" memorized-transactions">
					
                        
                        <header class="modal-h"><p class="submit"><button type="button" id="exit">Exit</button></p></header>
                        <?php if(isset($invoices))
                            foreach($invoices as $invoice){?>
                                <div id="invoice" style="width: 800px;
                                    display: block;
                                    page-break-before: always;
                                    position: relative;
                                    border-color: #e6e6e6;
                                    border-style: solid;
                                    border-width: 2px;
                                    padding: 15px;">
                                    <div id="invoice-header" class="flex-container invoice-header">
                                            <div class= "invoice-title">INVOICE</div>
                                                <div></div>
                                                <div></div>
                                                <div></div>
                                                <div class="invoice-sender-info">
                                                        <span>DK Tecnnology NY INC</span> <br>
                                                        <span>email@email.com</span> <br>
                                                        <span>718-547-5454</span>    
                                                </div>
                                                <div class= "invoice-sender-name">
                                                        <span>37 Lee Ave</span>  <br>          
                                                        <span>Brooklyn NY </span> <br>
                                                        <span>11211</span> 
                                                </div> 
                                        </div>
                                            <!--<div id="invoiceDetails2">-->
                                                <div id="billedTo2" class="flex-container" style="padding-top: 30px; padding-bottom: 30px;">
                                                            <div id="billedTo-info2">
                                                                
                                                                <div class="FSX-small">Billed To</div>
                                                                        <span>
                                                                        <?php echo $invoice->name ?><br>
                                                                        <?php echo $invoice->address_line_1 . " " . $invoice->address_line_2 ?><br>
                                                                        <?php echo $invoice->cs ?><br>
                                                                        <?php echo $invoice->zip ?><br></span>
                                                            </div>
                                                            <div id="invoice-info">
                                                                    <div class="FSX-small">Invoice Number</div>
                                                                    <span>000000000</span><br><br><br>
                                                                    <div class="FSX-small">Date Of Issue</div>
                                                                    <span><?php echo date("Y-m-d")?></span>
                                                            </div>
                                                            <div></div>
                                                            <div id="amountTotal2">
                                                                <div class="FSX-small">Invoice Total</div>
                                                                <div class="amountNumber">$ <?php echo $invoice->Tbalance ?></div>
                                                            </div>
                                                </div>
                                                <div class="invoice-table">
                                                            <table>
                                                            <tr>
                                                                    <th>Description</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th>Date</th>
                                                                    <th>Type</th>
                                                                    <th>Amount</th>
                                                                    <th>Balance</th>
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
                                                                        <td><?php echo $trans["description"] ?></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td><?php echo $trans["transaction_date"] ?></td>
                                                                        <td><?php echo $trans["type"] ?></td>
                                                                        <td>$<?= isset($trans["amount"]) ? number_format($trans["amount"] , 2): '0.00' ?> </td>
                                                                        <td>$<?= isset($trans["balance"]) ? number_format($trans["balance"] , 2): '0.00' ?></td>
                                                                    </tr> 
                                                                <?php }?>                                                      
                                                            </table>
                                                        </div>
                                                    <div id="totalBottom" style="margin-left:85%; padding-top: 30px; padding-bottom: 30px;">
                                                        <div><span class="invoiceTableColor">Total</span>&nbsp; &nbsp;  <span>$<?= isset($invoice->Tbalance) ? number_format($invoice->Tbalance , 2): '0.00' ?></span></div>
                                                    </div>
                                        <!--  </div>-->
                                    
                                </div>
                                <pagebreak />
                           <?php }?>
               
                  </form>               
            </div>
        </div>
    </div>

</div>
</div>

<?php 
function sendEmail()
{
    $this->load->library('email');

        $subject = 'This is a test';
        $message = '<p>This message has been sent for testing purposes.</p>';

        // Get full html:
        $body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=' . strtolower(config_item('charset')) . '" />
            <title>' . html_escape($subject) . '</title>
            <style type="text/css">
                body {
                    font-family: Arial, Verdana, Helvetica, sans-serif;
                    font-size: 16px;
                }
            </style>
        </head>
        <body>
        ' . $message . '
        </body>
        </html>';
        // Also, for getting full html you may use the following internal method:
        //$body = $this->email->full_html($subject, $message);

        $result = $this->email
            ->from('rafael@simpli-city.com')
            ->reply_to('rafael@simpli-city.com')    // Optional, an account where a human being reads.
            ->to('info@thevertexlabs.com')
            ->subject($subject)
            ->message($body)
            ->send();

        var_dump($result);
        echo '<br />';
        echo $this->email->print_debugger();

        exit;
}
?>

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