joseph breuer
Jul 15 (2 days ago)
to me 
<!DOCTYPE html>
<html>
<head>
	<title></title>

 <style type="text/css">

	@font-face {
    font-family: micr37;
    src: url(<?php echo base_url(); ?>themes/default/assets/styles/fonts/micrenc.ttf);
}
#check_print {
	width: 790px;
	border: 1px solid gray;
	background-color: green;
}
		
#check {
	
	padding: 10px;
	height: 300px;
	width: 780px;
	border-bottom:  1px solid gray;
}
#name {
	float: left;
}
#check_info {
	float: right;
}
#check_info  th  {
text-align: center;
padding: 0;

}
 table  {
	border: 1px solid gray;
}
#Amount_num {
	text-align: right;
}

#textAmount {
	position: relative;
	top: 110px;
	right: 50px;
}
#bottum {

	position: fixed;
	top: 200px;
}
#signature {
	text-align: center;
     border-top: 1px solid gray;
	position: fixed;
	left: 493px;
	top: 238px;
	width: 250px;
}
#accountNumber {
	position: fixed;
	top: 265px;
	left: 180px;
	/*text-align: center;*/
	 font-family: micr37;
   
}

	</style>
</head>
<body>

   <div id="check1" class="print-section">

   		    <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/views/forms/check/check_CSS.css">
   	<div id="check_print" style="font-size: 18px !important">	    
         <div id="check" style="font-size: 20px !important">
   	   	  <div id="name" >
   	   	  	<strong>DK Technology NY inc</strong><br>
   	   	  	37 Lee Ave<br>
   	   	  	Brooklyn NY 11211<br>
   	   	  </div>
   	   	  <div id="check_info">
   	   	  	  <table ">
   	   	  	  	 <tr>
   	   	  	  	 	<th>Check Date</th> 
   	   	  	  	 	<th>Check No.</th>

   	   	  	  	 </tr>
   	   	  	  	 <tr>
   	   	  	  	 	<td >12/27/2017</td>
   	   	  	  	 	 <td>100005455145</td>
   	   	  	  	 </tr>
   	   	  	  </table>
              
              <table >
              	 <tr>
              	 	<th>Amount</th>
              	 </tr>
              	 <tr>
              	 	<td id="Amount_num">$35.01</td>
              	 </tr>
              </table>

   	   	  </div>


   	   <div id="textAmount" style="font-size: 15px !important"> Four Thousand Seven Hundred Two Dollars</div>

   	  <div id="bottum">
   	  	 <div style="float: left;">
   	  	 	<small>Pay To The Order Of</small><br>
   	  	 	Devorah Klar<br>
   	  	 	262 Keap st<br>
   	  	 	Brooklyn NY 11211

   	  	 </div>
   	  	 <div id="signature">
   	  	 
         <div id="authsig"><small>Authorized Signature</small></div>

         </div>

           <div id="accountNumber" style="font-family: micr37 !important;">0001000 :540000021 :066325694</div>
      </div>
  </div>
  <div id="stub">
   	 <h6 >JB NY REALTY LLC</h6>

   	  <table class="stub" style="border: none !important; text-align: center; width: 100%">
   	  	 <tr style="border: none !important; ">
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 	

   	  	 </tr >
   	  	 <tr style="border: none !important;">
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 
   	  	 	
   	  	 </tr style="border: none !important;">
   	  	 <tr style="border: none !important;">
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 
   	  	 	
   	  	 </tr>
   	  	 <tr style="border: none !important;">
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 	
   	  	 	
   	  	 </tr>
   	  </table>
   </div>

   <div id="stub">
   	 <h6 >JB NY REALTY LLC</h6>

   	  <table class="stub">
   	  	 <tr>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 	

   	  	 </tr>
   	  	 <tr>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 
   	  	 	
   	  	 </tr>
   	  	 <tr>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 
   	  	 	
   	  	 </tr>
   	  	 <tr>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">dans auto shop</td>
   	  	 	<td style="border: none !important; text-align: center;">$500.20</td>
   	  	 	
   	  	 	
   	  	 </tr>
   	  </table>
   </div>

</div>
<h5><?php echo $id; ?> </h5>
</div></body></html>
								
<script type="text/javascript">



	 var ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine"];
	 var teens = ["Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
	 var tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];
	
    

     var numtochange = document.getElementById('Amount_num').innerHTML;
     console.log(numtochange.length);
     var decimal;
     if (numtochange.indexOf(".")<0) {decimal=numtochange.length} else{decimal= numtochange.indexOf(".")};
        console.log(decimal);
     var dollars = numtochange.slice(1,decimal);
     var cents = numtochange.slice(decimal+1,numtochange.length);





     var numtext ="";
     
//hare i difin thet i shuld ++ the value till 3 
            for (var i = 0; i < dollars.length; i++) {
             //here i say thet    
            	   console.log(dollars.length - i );

            	   switch(dollars.length - i) {
					    case 15: case 12: case 9: case 6: case 3:
					        numtext = numtext + " " + ones[dollars.charAt(i)] + " hundred";
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
					        	 numtext = numtext + " " + teens[dollars.charAt(i)] + " Thousand,";
					        }
					        else{
					        	numtext = numtext + " " + ones[dollars.charAt(i)] + " Thousand,";
					        }
					        
					        break;

					    
					    case 1:
					        if (dollars.charAt(i-1) ==1) {
					        	 numtext = numtext + " " + teens[dollars.charAt(i)] + " dollars and";
					        }
					        else{
					        	numtext = numtext + " " + ones[dollars.charAt(i)] + " dollars and";
					        }

					}


				 
				}

	 for (var i = 0; i <cents.length; i++) {
	 console.log(i);
     console.log(cents);
    



			switch(cents.length -i){
				case 2:
				
				numtext = numtext+" "+ tens[cents.charAt(i)];
				
				console.log(numtext);
				break; 

				case 1:

				if (cents.charAt(i -1) ==1) {
				numtext = numtext+" "+ teens[cents.charAt(i)]+" cents";
			    }
				else{
				numtext = numtext+" "+ ones[cents.charAt(i)]+" cents" ; 	
				}
				console.log(numtext);
				break;
			}



					}

     document.getElementById('textAmount').innerHTML = numtext;

</script>
<!--
<!DOCTYPE html>
<html>
<head>
	<title>chack</title>
	
	
</head>
<body>

<style type="text/css">
	#check{
	
	border: 2px solid #f37ce4;
	border-radius: 12px;
	width: 900px;
	height: 320px;
	margin-left: 10px;
}
#accountName {

	float: left;
	margin-top: 30px;
	margin-left: 55px;
}


th, td{
	padding-left:  10px;
}
td {
	text-align: center;
}
#check_date {
	float: right;
	margin-top: 30px;
	margin-right: 45px;
	;
}
#text{

	float: left;
	position: fixed; 
	top: 165px;
	left: 50px;
	padding-bottom: 0px;

}
#amount{
	text-align: center;
	background: gray;
}
#amountBox {
	border: 1px solid black;
	width: 100px;
	height: 37px;
	position: fixed;
	top: 125px;
	left: 775px;
}	
	
#Amount_num{
	
	text-align: right;
}
#paytoName{
	float: left;
	width: 50%;
	position: fixed;
	top: 205px;
	left: 80px;
	
}
#pay_to{
	color: black;
	margin-left: 0px;
	font-size: 10px;
}
#signature{

}
#line{

	border-top: 1px solid black;
	float: right;
	width: 375px;
	position: fixed;
	top:270px;
	left: 510px;
}
#sigTaxt{
	font-size: 10px;

	text-align: center;
	
}
#accountNum{
font-family: micr;
margin-top:275px;
margin-left: 170px;

font-size: 24px;
}

@font-face {
    font-family: micr;
    src: url(micrenc.ttf);
}
</style>
<div id="check">
    
	    <div id="accountName">
	     	<spen>
	     	<span id="dk">	DK Technolgy NY inc</span><br>
	     	    37 Lee Ave<br>
	     	    Brooklyn NY 11211<br>
	     	</spen>    
	    </div>

	    <div id="check_date">
	    	<table>
	    		<tr>
	    		<th>Check Date</th>
	    		<th>Check no.</th>
	    		</tr>
	    		<tr>
	    			<td>06/28/2018</td>
	    			<td>10002</td>
	    		</tr>
	    	</table>
	    
    </div>

	    <div id="dollarAmount">
	         <div id="text"> ****One Thousand Eight Hundret Two And 01/100 Dollars***</div>
	      <div id="amountBox">
	      	 <div id="boxHead">
	      	 	<div id="amount">Amount</div><div id="Amount_num" >$310582.25</div>
	      	 </div>
	      </div>
	</div>    
    <div id="bottomofchack">
     
      	<div id="paytoName">
      		 <div id="pay_to">Pay To The<br>Order Of<br></div>
	      	 <span>
	      	 	Yonatan T. Craven<br>
	      	 	418 Ridge 4th st<br>
	      	 	Lakewood, NJ 08701 
	      	 </span>
        </div> 
            <div id="line">
            	<div id="sigTaxt">Authoized Signarure</div>
            </div>
            <br>
            <div id="accountNum">
            	0010002 :021000021: 880605562"'
            </div>
					            
					           

    </div>
</div>





</body>


<script type="text/javascript">



	 var ones = ["","one","two","three","four","five","six","seven","eight","nine"];
	 var teens = ["ten","eleven","twelve","thirteen","fourteen","fifteen","sixteen","seventeen","eighteen","nineteen"];
	 var tens = ["","","twenty","thirty","forty","fifty","sixty","seventy","eighty","ninety"];
	
    

     var numtochange = document.getElementById('Amount_num').innerHTML;
     console.log(numtochange);
     var decimal;
     if (numtochange.indexOf(".")<0) {decimal=numtochange.length} else{decimal= numtochange.indexOf(".")};
     var dollars = numtochange.slice(1,decimal);
     var cents = numtochange.slice(decimal+1,numtochange.length);





     var numtext ="";
     
//hare i difin thet i shuld ++ the value till 3 
            for (var i = 0; i < dollars.length; i++) {
             //here i say thet    
            	   console.log(dollars.length - i );

            	   switch(dollars.length - i) {
					    case 15: case 12: case 9: case 6: case 3:
					        numtext = numtext + " " + ones[dollars.charAt(i)] + " hundred";
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
					        	 numtext = numtext + " " + teens[dollars.charAt(i)] + " Thousand,";
					        }
					        else{
					        	numtext = numtext + " " + ones[dollars.charAt(i)] + " Thousand,";
					        }
					        
					        break;

					    
					    case 1:
					        if (dollars.charAt(i-1) ==1) {
					        	 numtext = numtext + " " + teens[dollars.charAt(i)] + " dollars and";
					        }
					        else{
					        	numtext = numtext + " " + ones[dollars.charAt(i)] + " dollars and";
					        }

					}


				 
				}

	 for (var i = 0; i <cents.length; i++) {
	 console.log(i);
     console.log(cents);
    



			switch(cents.length -i){
				case 2:
				
				numtext = numtext+" "+ tens[cents.charAt(i)];
				
				console.log(numtext);
				break; 

				case 1:

				if (cents.charAt(i -1) ==1) {
				numtext = numtext+" "+ teens[cents.charAt(i)]+" cents";
			    }
				else{
				numtext = numtext+" "+ ones[cents.charAt(i)]+" cents" ; 	
				}
				console.log(numtext);
				break;
			}



					}

     document.getElementById('text').innerHTML = numtext;

</script>
</html>--->