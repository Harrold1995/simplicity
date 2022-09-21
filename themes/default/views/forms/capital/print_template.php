

    <div  class="page-break investor"> 
        <div style="text-align: center; color: black; border-bottom:0 solid #e3e3e3;">
                <h2 style="border-top: 1px solid #e3e3e3; padding:10px; margin-bottom: 5px;">Notice of Capital Call</h2>
                <h3 ><?php if(isset($first_name)) echo $first_name; if(isset($last_name)) echo " ".$last_name; ?></h3>
                <div>
                    <p>Date of notice: <?php if(isset($as_of)) echo $as_of;?></p>
                    <p>Due date: <?php if(isset($due)) echo $due;?></p>
                </div>
                
                <?php $share= '<br><br>';
                      $shareAmt= 0; 
                      foreach($capitals as $capital) { ?>
                      
                      <div class='property-container'>
                      <h5><?php if(isset($capital['property'])) echo $capital['property'];?></h5>
                      <br>
                      

                        <table class = "investor" style = "width:500px; margin:auto;">
                            <thead>
                            <tr><th>Current Capital Call: </th><th><?php if(isset($capital['cc_amt'])) echo '$'.number_format(removeComma($capital['cc_amt']), 2,'.', ',') ;?></th></tr>
                            <tr><th>Your Share: </td><td> <?php if(isset($capital['sd'])) echo '%'.$capital['percent'] ;?></th></tr>
                            </thead>
                            <tbody>
                                
                                
                                
                                
                            </tbody>
                            <tfoot>
                            <tr><th>Contribution Amount: </th><th><?php if(isset($capital['cc_amt'])) echo '$'.number_format((floatval(removeComma($capital['cc_amt']))/100) * floatval($capital['percent']), 2,'.', ',') ;?></th></tr>
                            </tfoot>
                        </table>
                        <h5>Please Wire Funds to:</h5>
                        <table  class = ' investor' style = "width:500px; margin:auto;">
                            <tbody>
                                <tr><td>Account #: </td><td> <?php if(isset($capital['sd'])) echo '%'.$capital['percent'] ;?></td></tr>
                                <tr><td>Routing #: </td><td> <?php if(isset($capital['sd'])) echo '%'.$capital['percent'] ;?></td></tr>
                                <tr><td>Beneficiary: </td><td> <?php if(isset($capital['sd'])) echo '%'.$capital['percent'] ;?></td></tr>
                                <tr><td>Bank Name: </td><td> <?php if(isset($capital['sd'])) echo '%'.$capital['percent'] ;?></td></tr>     
                            </tbody>

                        </table>
                      <br>
                      </div>
                      <br> <br>
                        


                       
                     
                <?php 
                  
                  $share = $share.'<tr><td>Your Share for '.$capital['property'].':</td><td> '. '$'.number_format((floatval(removeComma($capital['cc_amt']))/100) * floatval($capital['percent']), 2,'.', ',') .' ('.$capital['percent']."%)".'</td></tr>'; 
                  $shareAmt += (floatval(removeComma($capital['cc_amt']))/100) * floatval($capital['percent']); 
                }?>  
                    <h5>Summary</h5>
                   <table class = "investor" style = "width:500px; margin:auto;">
                      <?php echo $share; ?>
                      <tr><th>Total Contribution</th><th>$<?php echo number_format($shareAmt, 2,'.', ',') ; ?></th></tr>
                   </table>

    
        </div>
    </div>