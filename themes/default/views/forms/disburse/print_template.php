
    <div  class="page-break investor"> 
        <div style="text-align: center; color: black; border-bottom:0 solid #e3e3e3;">
                <h2 style="border-top: 1px solid #e3e3e3; padding:10px; margin-bottom: 5px;">Investor Report</h2>
                <h3 ><?php if(isset($first_name)) echo $first_name; if(isset($last_name)) echo " ".$last_name; ?></h3>
                
                <?php $share= '<br><br>';
                      $shareAmt= 0; 
                      foreach($capitals as $capital) { ?>
                        <br>
                        <h5><?php if(isset($capital['property'])) echo $capital['property'];?></h5>

                        <table class = "investor" style = "width:500px; margin:auto;">
                            <thead>
                                <tr>
                                    <th>Cash Balance - <?php if(isset($capital['as_of'])) echo $capital['as_of'];?>:</th>
                                    <th><?php if(isset($capital['bank'])) echo '$'.number_format($capital['bank'], 2,'.', ',');?></th>
                                </tr>
                       
                            </thead>
                            <tbody>
                                <tr><td>Reserve for Mortgage payments: </td><td><?php if(isset($capital['reserves'])){echo '$'.number_format($capital['reserves'], 2,'.', ',');} else{echo number_format(0, 2,'.', ',');}  ?></td></tr>
                                <tr><td>Security Deposits owed to tenants: </td><td> <?php if(isset($capital['sd'])) echo '$'.number_format($capital['sd'], 2,'.', ',') ;?></td></tr>
                                <tr><td>Prepaid Rent: </td><td> <?php if(isset($capital['lmr'])) echo '$'.number_format($capital['lmr'], 2,'.', ',');?></td></tr>
                                <tr><td>Accounts Payable: </td><td> <?php if(isset($capital['payables'])) echo '$'.number_format($capital['payables'], 2,'.', ',');?></td></tr>
                                
                            </tbody>
                            <tfoot>
                            <tr><th>Cash available to distribute: </th><th><?php if(isset($capital['cc_amt'])) echo '$'.number_format(removeComma($capital['cc_amt']), 2,'.', ',') ;?></th></tr>
                            </tfoot>
                        </table>
                        <br>
                       

                <?php 
                  $share = $share.'<tr><td>Your Share for '.$capital['property'].':</td><td> '. '$'.number_format((floatval(removeComma($capital['cc_amt']))/100) * floatval($capital['percent']), 2,'.', ',') .' ('.$capital['percent']."%)".'</td></tr>'; 
                  $shareAmt += (floatval(removeComma($capital['cc_amt']))/100) * floatval($capital['percent']); 
                }?> 
                   <table class = "investor" style = "width:500px; margin:auto;">
                      <?php echo $share; ?>
                      <tr><th>Total Distribution</th><th>$<?php echo number_format($shareAmt, 2,'.', ',') ; ?></th></tr>
                   </table>

    
        </div>
    </div>