var printChecks = function () {
}
printChecks.prototype = {
            //checks print template
            checkPrint: function(result, modal){
            //function checkprint(result){
                var data = result;
                var newPrintDiv = "<div style ='margin-top: -20px;'>";
                for(var i =0; i < data.length; i++){
            
                    newPrintDiv += `<div  class="page-break">    
                                <div id="check">
                                    <div id="name" >
                                        <strong>` + data[i].eName+`</strong>`;
                                        newPrintDiv  += `<br>`;
                                        if(data[i].eAddress){newPrintDiv  += data[i].eAddress;}
                                        newPrintDiv  += `<br>`;
                                        if(data[i].eCity && data[i].eState){
                                            newPrintDiv  += data[i].eCity +`, ` + data[i].eState;
                                        }else{
                                            if(data[i].eCity){newPrintDiv  += data[i].eCity;}
                                            if(data[i].eState){newPrintDiv  += data[i].eState;}
                                        }
                                        if(data[i].Ezip){newPrintDiv  += " " +data[i].Ezip;} 
                                        newPrintDiv  += `<br>
                                    </div>
                                    <div id="bankName" >
                                        <strong>` + data[i].bank_name;
                                        newPrintDiv  += `</strong><br>` + data[i].bank_address;
                                        newPrintDiv  += `</div>
            
                                    <div id="check_info">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <th>Check Date__</th> 
                                                    <th>Check No.</th>
                                                </tr>
                                                <tr>
                                                    <td id = cDate>` + printChecks.formatDate(data[i].date); 
                                                    newPrintDiv  += `</td>
                                                    <td id="cNum"> &nbsp; &nbsp; &nbsp; `;
                                                    newPrintDiv  += data[i].next_check_num;
                                                    newPrintDiv  += `</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        <table style="padding-top: 45px;">
                                            <tbody>
                                                <tr>
                                                    <th>Amount</th>`;
                                newPrintDiv  += `</tr>
                                                <tr>
                                                    <td id="Amount_num">` + currency(data[i].credit).format();
                                            
                                                    newPrintDiv  += `</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>


                                    <div id="textAmount">`;
                                        var textNumber = printChecks.textNum(data[i].credit);
                                        newPrintDiv  +=  textNumber;
                                        newPrintDiv  +=  `</div>
                                        <div style ="padding-left: 90px; padding-top: 95px;">
                                        <small>Pay To The Order Of</small><br>
                                        <span style ="font-weight: bold;">`+ data[i].profile;
                                        newPrintDiv  += `</span><br>
                                        <span id="payToAddress" style ='min-height: 95px'>`;
                                        if(data[i].address_line_1){newPrintDiv  += data[i].address_line_1;};
                                        if(data[i].address_line_2){newPrintDiv  += " " + data[i].address_line_2;};
                                        //+ data[i].address_line_1 + " " + data[i].address_line_2;   
                                        newPrintDiv  += `</span><br><span>`;
                                        if(data[i].city && data[i].state){
                                            newPrintDiv  += data[i].city +`, ` + data[i].state;
                                        }else{
                                            if(data[i].city){newPrintDiv  += data[i].city;}
                                            if(data[i].state){newPrintDiv  += data[i].state;}
                                        }
                                        if(data[i].zip){newPrintDiv  += " " +data[i].zip;}
                                        //newPrintDiv  += ` ` + data[i].zip;
                                        newPrintDiv  += `</span></div>
                                   <div id="bottum">
                                   
                                     
                                    <div style="padding-top: 30px;position: absolute;left: 1px;top: 90px;height: 20px;width: 400px;text-overflow: ellipsis;overflow: hidden;"><b>Memo: </b>`; 
                                    
                                    if( data[i].memo ) {
                                        newPrintDiv  += data[i].memo
                                    } 
                                    
                                    newPrintDiv  += `</div>
                                    <div id="signature">
            
                                        <div id="authsig"><small>Authorized Signature</small></div>
                                    </div>
            
                                    
                                
                                <div id="accountNumber" style=" font-family: micr37;" >
                                    <span id ="checkNumber">C` + data[i].next_check_num+'C';
                                        newPrintDiv  += `</span>
                                        <span id ="routingNum">A` + data[i].routing + `A`;
                                        newPrintDiv  += `</span> <span id ="acctNum">`  + data[i].account_number + `C`;
                                        newPrintDiv  += `</span> 
                                </div>
                            </div>

                            </div>
                            <div id="stub" style="border-bottom: 1px dashed gray;"  >
            
                                <table align="center";  class="stub"><thead><tr><th>Name </th><th> Memo </th></tr></thead>`;
                                    newPrintDiv  +=  "<tr><td>" + data[i].eName + "</td><td> "; 
                                    if( data[i].memo ) {
                                        newPrintDiv  += data[i].memo
                                    } 		
                                newPrintDiv  += `</td></tr></table>
                            </div>
                            <div id="stub">
            
                                <table style="text-align:center";  class="stub"> <thead><tr><th>Account </th><th> Property </th><th> Description</th><th style='text-align:right;'>Amount</th></tr></thead>`;
                                if(data[i].details){
                                    var details = data[i].details;
                                    for(var j = 0; j < details.length; j++){
                                        newPrintDiv  +=  "<tr><td>" + details[j].account + " </td><td>" + details[j].property + " </td><td>" + details[j].description + "</td><td style='text-align:right;'>"+ details[j].debit +"</td></tr>";	
                                    }
                                }
                                newPrintDiv  += `
                                </table>
                            </div>
                        </div>`;
            
                                
                }
                            newPrintDiv += `</div>`;
                            $('#Checkarea').empty();
                            $('#Checkarea').append(newPrintDiv);
                            function pad(n,a) { return ("000000" + n).slice(-a); }
                            var body2 = $('body').find(modal); 				
                        checkprint2();
                        //console.log(printChecks.textNum('150.52'));
                        
                function checkprint2(){			
            
                    $('#Checkarea').addClass('print-section');
                        $(modal).hide();
                        window.print();
                        $('#Checkarea').empty();
                    }
           // }
        },
        //pay bills print template
        billsPrint: function(result, modal, printSection){
            //function checkprint(result){
                //console.log("billprint");
                //console.log(printSection);

                var data = result;
                var newPrintDiv = "<div style ='margin-top: -20px;'>";
                for(var i =0; i < data.length; i++){
            
                    newPrintDiv += `<div  class="page-break">    
                                <div id="check">
                                <div id="name" >
                                    <strong>` + data[i].eName+`</strong>`;
                                    newPrintDiv  += `<br>`;
                                    if(data[i].eAddress){newPrintDiv  += data[i].eAddress;}
                                    newPrintDiv  += `<br>`;
                                    if(data[i].eCity && data[i].eState){
                                        newPrintDiv  += data[i].eCity +`, ` + data[i].eState;
                                    }else{
                                        if(data[i].eCity){newPrintDiv  += data[i].eCity;}
                                        if(data[i].eState){newPrintDiv  += data[i].eState;}
                                    }
                                    if(data[i].Ezip){newPrintDiv  += " " +data[i].Ezip;} 
                                    newPrintDiv  += `<br>
                                </div>
                                    <div id="bankName" >
                                        <strong>` + data[i].bank_name;
                                        newPrintDiv  += `</strong><br>` + data[i].bank_address;
                                        newPrintDiv  += `</div>
            
                                    <div id="check_info">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <th>Check Date__</th> 
                                                    <th>Check No.</th>
                                                </tr>
                                                <tr>
                                                    <td id = cDate>` + printChecks.formatDate(data[i].date); 
                                                    newPrintDiv  += `</td>
                                                    <td id="cNum"> &nbsp; &nbsp; &nbsp; `;
                                                    newPrintDiv  += data[i].next_check_num;
                                                    newPrintDiv  += `</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        
                                        <table style ="padding-top: 45px";>
                                            <tbody>
                                                <tr>
                                                    <th>Amount</th>`;
                                newPrintDiv  += `</tr>
                                                <tr>
                                                    <td id="Amount_num">` + currency(data[i].credit).format();
                                                    newPrintDiv  += `</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>`;
                                    newPrintDiv  += `<div id="textAmount">`;
                                var textNumber = printChecks.textNum(data[i].credit);
                                newPrintDiv  +=  textNumber;
                                newPrintDiv  +=  `</div>
                                    <div style ="padding-left: 90px; padding-top: 95px;">
                                        <small>Pay To The Order Of</small><br>
                                        <span style ="font-weight: bold;">`+ data[i].profile;
                                        newPrintDiv  += `</span><br><span id="payToAddress">`;
                                        if(data[i].address_line_1){newPrintDiv  += data[i].address_line_1;}
                                        if(data[i].address_line_2){newPrintDiv  += " " + data[i].address_line_2;}
                                        //+ data[i].address_line_1 + " " + data[i].address_line_2;   
                                        newPrintDiv  += `</span><br><span>`;
                                        if(data[i].city && data[i].state){
                                            newPrintDiv  += data[i].city +`, ` + data[i].state;
                                        }else{
                                            if(data[i].city){newPrintDiv  += data[i].city;}
                                            if(data[i].state){newPrintDiv  += data[i].state;}
                                        }
                                        if(data[i].zip){newPrintDiv  += " " +data[i].zip;}

                                        // <span id="payToAddress">`+ data[i].address_line_1 + " " + data[i].address_line_2;   
                                        // newPrintDiv  += `</span><br><span>`+ data[i].cs;
                                        // newPrintDiv  += `, ` + data[i].zip;
                                        newPrintDiv  += `</span>
                                    </div>`; 
                                    
                                newPrintDiv  +=  `<div id="bottum">
                                    
                                    <div style="padding-top: 5px;position: absolute;left: 1px;top: 110px;height: 15px;width: 400px;text-overflow: ellipsis;overflow: hidden;"><b>Memo: </b>`; 
                                    
                                    if( data[i].memo ) {
                                        newPrintDiv  += data[i].memo
                                    } 
                                    
                                    newPrintDiv  += `</div>
                                    <div id="signature">
            
                                        <div id="authsig"><small>Authorized Signature</small></div>
                                    </div>

                                    <div id="accountNumber" style=" font-family: micr37;" >
                                     <span id ="checkNumber">C` + data[i].next_check_num +'C';
                                      newPrintDiv  += `</span>
                                      <span id ="routingNum">A` + data[i].routing + `A`;
                                      newPrintDiv  += `</span> <span id ="acctNum">`  + data[i].account_number + `C`;
                                      newPrintDiv  += `</span> </div>
                                    </div>
            
                                    
                                </div>
                                
                            <div id="stub" style="border-bottom: 1px dashed gray;"  class="page-break">
            
                                <table align="center";  class="stub"><thead><tr style =" width:100%" ><th style =" width:16.66%">date </th><th style = " width:16.66%"> Type </th><th style =" width:16.66%"> Reference</th><th style='text-align:right; width:16.66%'>Original amount</th><th style='text-align:right; width:16.66%'>Balance</th><th style='text-align:right; width:16.7%'>Payment</th></tr></thead>`;
                                if(data[i].details){
                                    var details = data[i].details;
                                    for(var j = 0; j < details.length; j++){
                                    
                                        newPrintDiv  +=  "<tr style ='width:100%' ><td style =' width:16.66%' >" + details[j].bill_date + "</td><td style =' width:16.66%'> " + details[j].type + " </td><td style =' width:16.66%'> " + details[j].transaction_ref + "</td><td style='text-align:right; width:16.66%'>"+ details[j].original_amount +"</td><td style='text-align:right; width:16.66%'>"+ details[j].open_balance +"</td><td style='text-align:right; width:16.7%'>"+ details[j].payment +"</td></tr>";		
                                    }
                                }
                                newPrintDiv  += `</table>
                            </div>
                            <div id="stub">
            
                            <table align="center";  class="stub"><thead><tr style =" width:100%" ><th style =" width:16.66%">date </th><th style = " width:16.66%"> Type </th><th style =" width:16.66%"> Reference</th><th style='text-align:right; width:16.66%'>Original amount</th><th style='text-align:right; width:16.66%'>Balance</th><th style='text-align:right; width:16.7%'>Payment</th></tr></thead>`;
                            if(data[i].details){
                                var details = data[i].details;
                                for(var j = 0; j < details.length; j++){
                                
                                    newPrintDiv  +=  "<tr style ='width:100%' ><td style =' width:16.66%' >" + details[j].bill_date + "</td><td style =' width:16.66%'> " + details[j].type + " </td><td style =' width:16.66%'> " + details[j].transaction_ref + "</td><td style='text-align:right; width:16.66%'>"+ details[j].original_amount +"</td><td style='text-align:right; width:16.66%'>"+ details[j].open_balance +"</td><td style='text-align:right; width:16.7%'>"+ details[j].payment +"</td></tr>";		
                                }
                            }
                            newPrintDiv  += `</table>
                            </div>
                        </div>`;
            
                                
                }
                            newPrintDiv += `</div>`;
                            $('#Checkarea').empty();
                            $('#Checkarea').append(newPrintDiv);
                            function pad(n,a) { return ("000000" + n).slice(-a); }
                            var body2 = $('body').find(modal); 				
                        checkprint3();
                        //console.log(printChecks.textNum('123.52'));
                        
                function checkprint3(){			
            
                        $('#Checkarea').addClass('print-section');
                        $(modal).hide();
                        window.print();
                        //$('#Checkarea').empty();
            
                    }
        },
            
        textNum: function(num){
            // function textNum (num) {
                //displaying amount as text
                            var ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine"];
                            var teens = ["Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
                            var tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
                
                            var numtochange = num;
                            var decimal;
                            if (numtochange.indexOf(".")<0) {decimal=numtochange.length} else{decimal= numtochange.indexOf(".")};
            
                            var dollars = numtochange.slice(0,decimal);
                            var cents = numtochange.slice(decimal+1,numtochange.length);
                            var numtext ="";
            
            
                                for (var i = 0; i < dollars.length; i++) {
                                    
                                        console.log(dollars.length - i );
            
                                        switch(dollars.length - i) {
                                            case 15: case 12: case 9: case 6: case 3:
                                                if(ones[dollars.charAt(i)] != 0){
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " Hundred";
                                                }
                                                break;
                                            case 14: case 11: case 8: case 5: case 2:
                                                numtext = numtext + " " + tens[dollars.charAt(i)];
                                                break;
                                            case 13:
                                                if (dollars.charAt(i-1) ==1) {
                                                        numtext = numtext + " " + teens[dollars.charAt(i)] + " Trillion,";
                                                }
                                                else{
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " Trillion,";
                                                }
                                                
                                                break; 
            
                                            
                                            case 10:
                                                if (dollars.charAt(i-1) ==1) {
                                                        numtext = numtext + " " + teens[dollars.charAt(i)] + " Billion,";
                                                }
                                                else{
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " Biliion!,";
                                                }
                                                
                                                break;    
            
                                            
                                            case 7:
                                                if (dollars.charAt(i-1) ==1) {
                                                        numtext = numtext + " " + teens[dollars.charAt(i)] + " million,";
                                                }
                                                else{
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " million,";
                                                }
                                                
                                                break;                        
            
                                            
                                            case 4:
                                                if (dollars.charAt(i-1) ==1) {
                                                        numtext = numtext + " " + teens[dollars.charAt(i)] + " Thousand";
                                                }
                                                else{
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " Thousand";
                                                }
                                                
                                                break;
                                    case 1:
                                                if (dollars.charAt(i-1) ==1) {
                                                        numtext = numtext + " " + teens[dollars.charAt(i)] + " Dollars and";
                                                }
                                                else{
                                                    numtext = numtext + " " + ones[dollars.charAt(i)] + " Dollars and";
                                                }   
                                        }                               
                                }
            
                                if (cents == "00"){
                                    numtext = numtext+" Zero Cents";
                                    } else {
                                        for (var i = 0; i <cents.length; i++) {     
                                            switch(cents.length-i){
                                                case 2:
                                                
                                                numtext = numtext+" "+ tens[cents.charAt(i)];
            
                                                break; 
            
                                                case 1:
            
                                                if (cents.charAt(i -1) ==1) {
                                                numtext = numtext+" "+ teens[cents.charAt(i)]+" Cents";
                                                }
                                                else{
                                                numtext = numtext+" "+ ones[cents.charAt(i)]+" Cents" ; 	
                                                }   
                                                break;  
                                            } 
                                    }  
                        }  
                        return numtext;
            // }  
        },

            printChecksButton: function(){
                $('body').on('click', '#checkButtonClicked', function (e)
            {
                var transactions2 = [];
                e.preventDefault();
                var that2 = this;
                //console.log(that2);
                var headerTransaction_id = $(this).closest('.modal').find("section:first").find('#account_id').closest('.select').find('input[type="hidden"]').val();
                var transNum = $(this).closest('.modal').find('#transNum').val(); 

                            transactions2.push({'th_id': transNum, 'id': headerTransaction_id, 'single':true });
            

                    $.post(JS.baseUrl+"transactions/onPrint", {
                            'params': JSON.stringify(transactions2)
                        }, function (result) {
                            console.log(result);
                            console.log("result");
                        var result2 = JSON.parse(result);
                        //console.log(result2[0].next_check_num);
                        //getcheckNumber(result2[0].next_check_num, result2, that2);
                        //checkprint(result2)
                        //printChecks.checkPrint(result2, $(that2).closest('.modal'));
                        printChecks.getCheckNumber(result2[0].next_check_num, result2, $(that2).closest('.modal'));
                        JS.openModalsObjectRemove($(that2).closest('.modal').attr('type'), $(that2).closest('.modal').attr('openModal-id'));
                    })
                    
                    console.log(transactions2);

                });
            },
            printMultipleCheckButton: function(){
                $('body').on('click', '#multipleCheckButtonClicked', function (e)
                {
                    var transactions2 = [];
                  e.preventDefault();
                  var that2 = this;
                  //console.log(that2);
                  var info = $(this).closest('.modal').find('#checks_body').find('tr');
              
                          info.each( function(){ if($(this).hasClass('setName')){
                              transactions2.push({'th_id':$(this).find('#th_id').val(), 'id':$(this).find('#id').val()});
                          }  
                      })
                      $.post(JS.baseUrl+"transactions/onPrint", {
                              'params': JSON.stringify(transactions2)
                          }, function (result) {
                              console.log(result);
                              console.log("result");
                          var result2 = JSON.parse(result);
                          console.log(result2[0].next_check_num);
                          if(result2.length > 1){
                                printChecks.checkPrint(result2, $(that2).closest('.modal'));
                                JS.openModalsObjectRemove($(that2).closest('.modal').attr('type'), $(that2).closest('.modal').attr('openModal-id'));
                          }else{
                                printChecks.getCheckNumber(result2[0].next_check_num, result2, $(that2).closest('.modal'));
                                JS.openModalsObjectRemove($(that2).closest('.modal').attr('type'), $(that2).closest('.modal').attr('openModal-id'));
                          }
                      })
                      
                      console.log(transactions2);
              
                });
            },
            printBlankCheck: function(result, voided = null){

                    var today = moment().format('MM/D/YYYY');
                    var data = result;
                    if(!data){JS.showAlert('danger', 'Bank information required!'); return;}
                    var newPrintDiv = "<div style ='margin-top: -20px;'>";

                    newPrintDiv +=  ` <div class="page-break">    
                            <div id="check">
                            <div id="name" ><strong>` + data.eName + `</strong><br>`;
                                       
                            if(data.eAddress){newPrintDiv  += data.eAddress;}
                            newPrintDiv  += `<br>`;
                            if(data.eCity && data.eState){
                                newPrintDiv  += data.eCity +`, ` + data.eState;
                            }else{
                                if(data.eCity){newPrintDiv  += data.eCity;}
                                if(data.eState){newPrintDiv  += data.eState;}
                            }
                            if(data.Ezip){newPrintDiv  += " " +data.Ezip;}

                            // newPrintDiv  += `</strong><br>` + data.address;
                            // newPrintDiv  += `<br>` + data.cs;
                            // newPrintDiv  += `, `+ data.zip;
                            newPrintDiv  += `<br></div>`;
                            newPrintDiv  += `<div id="bankName" >
                            <strong>` + data.bank_name;
                            newPrintDiv  += `</strong><br>` + data.bank_address;
                            newPrintDiv  += `</div>
                            
                               
                                        
                                <div id="check_info">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <th>Check Date__</th> 
                                                <th>Check No.</th>
                                            </tr>
                                            <tr>
                                            <td id = cDate>` + today; 
                                            newPrintDiv  += `</td>
                                            <td id="cNum"> &nbsp; &nbsp; &nbsp; `;
                                            newPrintDiv  += data.next_check_num;
                                            newPrintDiv  += `</td>
                                        </tr>
                                        </tbody>
                                    </table>                                         
                                </div>
                                        
                                <div style="position: absolute;top: 100px;">
                                <small style="position: absolute;left: 25px;">Pay To The Order Of</small>
                                <input style="border-bottom:1px solid black;width:480px;position: absolute;left: 150px;">
                                <span style="position: absolute; left: 660px;">$</span>
                                <input style="border: 2px solid #a3afae33;width:100px;position: absolute;left: 670px;">
                                <br>
                                <br>
                                <input style="border-bottom:1px solid black;width: 680px;">   
                                <span>Dollars</span>
                                </div>`;

                                if(voided){
                                    newPrintDiv  += `<div style ="position: absolute;Top: 25px;
                                    font-size: 200px;
                                    font-weight: 900;
                                    opacity: 10%;
                                    text-align: center;width: 100%;">VOID</div>`
                                }

                                newPrintDiv  += `<div id="bottum" style ="top:180px;">
                                    <div>
                                        <div>
                                            <span style="position: absolute;left: 30px;">Memo</span>
                                            <input style="border-bottom:1px solid black;width:300px;position: absolute;left: 90px;">
                                            <input style="border-bottom:1px solid black;width:250px;position: absolute;left: 510px;">
                                        </div>
                                    </div>
                                </div>
                                <div id="accountNumber" style=" font-family: micr37; top:165px;">
                                    <span id ="checkNumber">C` + data.next_check_num +"C";
                                    newPrintDiv  += `</span>
                                    <span id ="routingNum">A` + data.routing + `A`;
                                    newPrintDiv  += `</span> <span id ="acctNum">`  + data.account_number + `C`;
                                    newPrintDiv  += `</span> 
                                </div>
                            </div>
                        </div>
                        `;
                
                                $('#Checkarea').empty();
                                $('#Checkarea').append(newPrintDiv);
                                function pad(n,a) { return ("000000" + n).slice(-a); }				
                            checkprint2();
                            
                    function checkprint2(){			
                
                            $('#Checkarea').addClass('print-section');
                
                            window.print();
                            $('#Checkarea').empty();
                
                        }
            },
            getCheckNumber: function(number, checkInfo, modal){
                var ref = $(modal).find('#transaction_ref').val();
                if($.isNumeric(ref)){
                    //console.log('is numeric');
                    number = ref;
                }
                //console.log(ref);
               // function getcheckNumber(number, checkInfo, modal){
                console.log('getCheckNumber');
                var printResult = '';
                    bootbox.prompt({
                                //message: "Select Check Number",
                                title: "Select Check Number",
                                inputType: "number",
                                value: number,
                                buttons: {
                                    confirm: {
                                        label: 'Print',
                                        className: 'btn-danger'
                                    },
                                    cancel: {
                                        label: 'Cancel',
                                        className: 'btn'
                                    }
                                },
                                callback: function (result) {
                                    if (result) {
                                        console.log(checkInfo[0].next_check_num);
                                        var next_check_num = checkInfo[0].next_check_num;
                                        checkInfo[0].next_check_num = result;
                                        // for(var i =0; i < checkInfo.length; i++){
                                        //         console.log(checkInfo[0].next_check_num);
                                        //         $(modal).closest('body').find('.bootbox').hide();
                                        //         //printChecks.checkprint(checkInfo);
                                        //         printChecks.checkPrint(checkInfo, modal, $(modal).find('#check_print'));
                                        //     }
                                        var modalType = $(modal).attr('type');
                                        $(modal).closest('body').find('.bootbox').hide();
                                        console.log(modalType);
                                        if(modalType == 'check' || modalType == 'applyRefund' || modalType == 'check-grid'){
                                            printChecks.checkPrint(checkInfo, modal);
                                        }
                                        if(modalType == 'pay-bills' || modalType == 'bill-payment'){
                                            printChecks.billsPrint(checkInfo, modal);
                                        }
                                        
                                            
                                    }
                                    console.log('did not hit result');
                                    if(result){printResult = 'pass';}else{printResult = 'fail';}
                                    console.log(printResult);
                                    $.post(JS.baseUrl+"transactions/verifyPrint", {
                                        'th_id': checkInfo[0].th_id,
                                        'account_id': checkInfo[0].account_id,
                                        'printResult': printResult,
                                        'check_number': result ? result : null,
                                        'next_check_number': result ? next_check_num : null,
                                        }, function (result) {
                                    });
                                }
                            });
                   // }
            },
            formatDate: function(date){
                var newFormat = date.split("-");//.reverse().join("/");
                var newDate = newFormat[1] + '/' + newFormat[2] + '/' + newFormat[0];
                // var today = date ? new Date(date) : new Date();
                // var dd = String(today.getDate()).padStart(2, '0');
                // var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
                // var yyyy = today.getFullYear();

                // today = mm + '/' + dd + '/' + yyyy;
                return newDate;
            },
            //prints a single invoice
            printInvoices: function(){

                $('body').on('click', '#printThisInvoice', function(e){
                    e.preventDefault();
                    var modal = $(this).closest('.modal');
                    var invoices =  $(modal).find('#printInvoice').html();
                    $('#Checkarea').empty();
                    
                    $('#Checkarea').append(invoices);
                    //console.log(invoices);
                    $('#Checkarea').addClass('print-section');
                    //$(modal).hide();
                    //setTimeout(function () { window.print(); }, 300);
                    window.print();
                    $('#Checkarea').empty();
                });
            },
            //emails a single invoice
            emailInvoice: function(){

                $('body').on('click', '#emailThisInvoice', function(e){
                    e.preventDefault();
                    var modal = $(this).closest('.modal');
                    //var id = $(this).attr('data-id');
                    //var type = $(this).attr('type');
                    //var invoices =  $(modal).find('#printInvoice').html();
                    var statements = $(modal).find('.statements');
                    itirate(statements);

                });

                async function itirate(statements){
                    console.log('itirate function');
                    for(var i = 0; i < statements.length; i++){
                        id= $(statements[i]).attr('invoice-profile-id');
                        type='tenant';
                        $(statements[i]).addClass("pdfStyles");
                        var sdate = new Date();
                        nsdate = (sdate.getMonth() + 1) + '-' + sdate.getDate() + '-' +  sdate.getFullYear();
                        await posts(statements[i], id, type);
                    }
                }
                async function posts(statement, id, type){
                    $.post(JS.baseUrl+'reports/pdf', {header: $(statement).html(), filename:"Statement" + nsdate + id}, function(url) {
                        console.log(url);
                        console.log(id + 'id');
                         $.post(JS.baseUrl+"invoice/sendInvoice", {
                                'params': JSON.stringify($(statement).html()),
                                'id': id,
                                'type': type,
                                'attachment':url
                            }, function (result) {
                            var result = JSON.parse(result);
                            //console.log(result);
                            //JS.showAlert('success', 'Email succesfully sent!');
                            JS.showAlert(result.type, result.message);
                            if(result.type == 'success'){
                                //$(modal).hide();
                            }
                        });
                    });
                }
            },
            filterStatementDates: function(date){
                
                $('body').on('change', '.statement-date', function(e){
                    e.preventDefault();
                    var modal = $(this).closest('.modal');
                    var id = $(this).attr('data-id');
                    var type = $(this).attr('type');
                    var date = $(modal).find('#as_of_date').val();
                    var date2 =$(modal).find('#start_date').val();
                    //console.log(id);
                    //console.log(type);
                    console.log(date);
                    console.log(date2);
                    
                    $.post(JS.baseUrl+"invoice/getFilteredStatements/" + id + '/' + type + '/' + date+ '/' + date2,  {
                        'id': id,
                        'type': type,
                        'date':date,
                        'date2':date2
                    }, function (result) {
                        //console.log(result);
                    var result = JSON.parse(result);
                    //console.log(result);
                    modal.find('#printInvoice').empty().html(result);
                });

                });
            }

}
    var printChecks = new printChecks();
    $(document).ready(function () {
        //printChecks.checkPrint();
        printChecks.printChecksButton();
        printChecks.printMultipleCheckButton();
        printChecks.printInvoices();
        printChecks.emailInvoice();
        printChecks.filterStatementDates();
    });



    $('body').on('click', '.editBillPrint', function(e){
        e.preventDefault();
            var transactions = [];
            var that2 = this;
            //console.log(that2);
            var account_id = $(this).closest('.modal').find('#bankInfo').find('input[type="hidden"]').val();
            var transId = $(this).closest('.modal').find('#th_id').val();

            transactions.push({'th_id': transId, 'account_id': account_id, 'single':true});


            $.post(JS.baseUrl + "transactions/printBillPayment", {
                    'params': JSON.stringify(transactions)
            }, function (result) {

                    var result2 = JSON.parse(result);
                    var finalResults = [];
                    finalResults.push(result2);
                    console.log( $('finalResults[0]') );
                    //printChecks.billsPrint(finalResults, $(that2).closest('.modal'));
                    printChecks.getCheckNumber(result2.next_check_num, result2, $(that2).closest('.modal'));
                    JS.openModalsObjectRemove($(that2).closest('.modal').attr('type'), $(that2).closest('.modal').attr('openModal-id'));
            })
}) 