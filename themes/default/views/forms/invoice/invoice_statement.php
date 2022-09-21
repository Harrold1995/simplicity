<?php if(isset($invoices))
                        $invoiceNumber = 1;
                            foreach($invoices as $invoice){
                                if ($this->site->settings->use_management_for_statement == 1){
                                    $invoice->eName = $this->site->settings->company_name;
                                    $invoice->eAddress = $this->site->settings->company_address;
                                    $invoice->eCity = $this->site->settings->company_city;
                                    $invoice->eState = $this->site->settings->company_state;
                                    $invoice->eZip = $this->site->settings->company_zip;
                                    $invoice->eEmail = $this->site->settings->company_email;
                                    $invoice->ePhone = $this->site->settings->company_phone;
                                }  
                                ?>
                               <div invoice-id="<?= $info->id?>" invoice-profile-id="<?= $invoice->profile_id?>" class="page-break statements"> 
                                <div id="invoice" invoice-profile-id="<?= $invoice->profile_id?>" style="padding-top: 15px; width: 775px; display: block; page-break-after: always; position: relative; font-family: HelveticaNeue,'Helvetica Neue',helvetica,Arial,sans-serif;">                                
                                    <div id="invoice-header" style=" background-color: #ffffff; color: #828080; height: 190px; " class="flex-container invoice-header">
                                            <div class="invoice-sender-info" style="font-size: 14px; padding: 15px; width: 60%; float:left;">
                                            <?php if ($this->site->settings->use_management_for_statement == 1 && $this->site->settings->company_logo !=""){
                                                echo '<img src="'.base_url().'uploads/images/'.$this->site->settings->company_logo.'" alt="Logo" width="150" height="150"><br>';
                                            }  ?>
                                                        <span style ="font-size: 20px;" ><?php echo $invoice->eName ?></span> <br>
 
                                                    <?php if($invoice->eAddress !=''){ ?>
                                                        <span><?php echo $invoice->eAddress ?></span>  <br>          
                                                        <span><?php echo $invoice->eCity ?>,&nbsp;<?php echo $invoice->eState ?></span> <br>
                                                        <span><?php echo $invoice->eZip ?></span> <br>
                                                    <?php } ?>  
                                                        <span><?php echo $invoice->eEmail ?></span> <br>
                                                        <span><?php ($invoice->ePhone != 0 ? $invoice->ePhone : ''); ?></span>   
                                            </div>
                                            <div style="width: 35%; float:right">
                                                 <div class= "invoice-title" style="color: #9da3a6;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight: 700;margin-bottom: 8px;font-size: 30px; padding-top: 20px; padding-left: 30px; text-align: right">STATEMENT</div>
                                                 <div id="invoice-info" style="width: 100%">
                                                                    <div class="" style="color: black; padding: 15px; text-align: left; font-size:15px;">Statement Date: <span style=" "><?php echo date("m/d/Y")?></span></div>
                                                                    <br>
                                                                    
                                                                    <div class="" style="color: black; padding-right: 15px; padding-left: 15px; text-align: left; font-size:15px;">Property: <span style=" "><?php echo $invoice->property ?></span></div>
                                                                    <div class="" style="color: black; padding-right: 15px;   padding-left: 15px; text-align: left; font-size:15px;">Unit: <span style=" "><?php echo trim($invoice->unit) ?></span></div>
                                                                    <!-- <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding: 15px;">Date Of Issue</div>
                                                                    <span>< ?php echo date("Y-m-d")?></span> -->
                                                  </div>
                                            </div>
                                            
                                                 
                                        </div>
                                            <!--<div id="invoiceDetails2">-->
                                                <div id="billedTo2" class="flex-container" style="padding-top: 30px; padding-bottom: 30px; padding-left: 25px; height:220px ">
                                                            <div id="billedTo-info2" style="padding-top: 25px; width: 60%; float:left">
                                                                
                                                                <div class="FSX-small" style="font-size: x-small;color: #aebbce; padding-bottom: 10px;">Billed To</div>
                                                                        <span>
                                                                        <?php echo $invoice->name ?><br>
                                                                        <?php echo $invoice->address_line_1 . " " . $invoice->address_line_2 ?><br>
                                                                        <?php echo $invoice->cs ?><br>
                                                                        <?php echo $invoice->zip ?><br></span>
                                                            </div>

                                                            <div id="amountTotal2" style=" width: 35%;  margin-top: 15px; border: 1px solid #ddd; border-radius: 5px; padding: 15px; float:right">
                                                                <div class="" style="color: #aebbce; padding: 15px; text-align: right">Amount Due</div>
                                                                <div class="amountNumber" style="font-size: 10.1mm; color: #efc100; text-align: right">$ <?php echo number_format($invoice->Tbalance, 2) ?></div>
                                                            </div>
                                                </div>
                                                <div class="invoice-table" style="border-top: 2px solid #efc100; border-collapse: collapse;">
                                                            <table style="width: 100%; ">
                                                                <thead>
                                                                    
                                                                    <tr style="padding:3px; font-family:HelveticaNeue,'Helvetica Neue',helvetica,Arial,sans-serif">
                                                                                <th style="color: #efc100;">Description</th>
                                                                                <th style="color: #efc100; margin-left:35%;">Date</th>
                                                                                <th style="color: #efc100; margin-left:50%;">Type</th>
                                                                                <th style="color: #efc100; margin-left:75%;">Amount</th>
                                                                                <th style="color: #efc100; margin-left:85%;">Balance</th>
                                                                        </tr>
                                                                </thead>
                                                                <tbody style="display: table-row-group; vertical-align: middle; border-color: inherit;">
                                                                    
                                                                        <?php if(isset($invoice->data))
                                                                        $invoiceData = $invoice->data;?>
                                                                            <tr style="padding:3px; font-family:HelveticaNeue,'Helvetica Neue',helvetica,Arial,sans-serif">
                                                                                <td style="color: #efc100; font-weight: bold; border-bottom: 1px solid #e3e3e3;">Beginning Balance:</td>
                                                                                <td style =" border-bottom: 1px solid #e3e3e3;"></td>
                                                                                <td style =" border-bottom: 1px solid #e3e3e3;"></td>
                                                                                <td style =" border-bottom: 1px solid #e3e3e3;"></td>
                                                                                <td style =" border-bottom: 1px solid #e3e3e3;">$<?= isset($invoiceData->beginningBalance) ? number_format($invoiceData->beginningBalance , 2): '0.00' ?></td>
                                                                            </tr> 
                                                                        <?php foreach($invoiceData->trans as $trans){?>
                                                                            <tr style="padding:3px; font-family:HelveticaNeue,'Helvetica Neue',helvetica,Arial,sans-serif">
                                                                                <td style="max-width: 150px;  border-bottom: 1px solid #e3e3e3; text-overflow: ellipsis;white-space: nowrap;overflow: hidden;"><?php echo $trans["description"] ?></td>
                                                                                <td style="margin-left:35%;  border-bottom: 1px solid #e3e3e3;"><?php echo date("m/d/Y", strtotime($trans["transaction_date"])) ?></td>
                                                                                <td style="margin-left:50%;  border-bottom: 1px solid #e3e3e3;"><?php echo $trans["type"] ?></td>
                                                                                <td style="margin-left:75%;  border-bottom: 1px solid #e3e3e3;">$<?= isset($trans["amount"]) ? number_format($trans["amount"] , 2): '0.00' ?> </td>
                                                                                <td style="margin-left:85%;  border-bottom: 1px solid #e3e3e3;">$<?= isset($trans["balance"]) ? number_format($trans["balance"] , 2): '0.00' ?></td>
                                                                            </tr> 
                                                                        <?php }?>  
                                                                </tbody>                                                    
                                                            </table>
                                                        </div>
                                                     <!--<div id="totalBottom" style="margin-left:85%; padding-top: 30px; padding-bottom: 30px;">
                                                        <div><span class="invoiceTableColor" style="color: #efc100;">Total</span>&nbsp; &nbsp;  <span>$<?= isset($invoice->Tbalance) ? number_format($invoice->Tbalance , 2): '0.00' ?></span></div>
                                                    </div>
                                         </div>-->
                                    
                                </div style="display: block; page-break-after: always; position: relative;">
                            </div>
                           <?php $invoiceNumber++; }?>