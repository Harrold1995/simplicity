<div class="modal fade capital-modal" id="Disburse" tabindex="-1" role="dialog"  type="Disburse" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		    <div id="root" class="no-print">
				<div class="modal-content text-primary popup-a form-entry shown" style="width: 112%;  padding: 30px;">
					<form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="disbursement-entry" type="disburse" style ="height: 70%;">
							<div class="t_input_wrapper">
								<header class="ui-draggable-handle">
									<h2><?=$title ?></h2>
									<nav>
										<ul>
											<li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
											<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
											<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
										</ul>
									</nav>
									<p  class="submit"><button type="button" id="exit">Exit</button></p>
								</header>
								<div class ="remove" style="padding: 10px;">
										<span style="margin-right: 12px;">As of </span>
										<input style="width: 18%; display: inline;" data-toggle="datepicker" value="<?=$date ?>" name = "as_of_date" id="capital_as_of_date" >
										

										<span style="margin-right: 12px;">Due date </span>
										<input style="width: 18%; display: inline;" data-toggle="datepicker" value="" name="due_date" >
								</div>
							</div >
							<div class="formGridSlickTable mobile-hide utilities" style="z-index: 2; height:100%; ">

							</div>
							<p class="strong scheme-b m20 size-a overlay-a remove" id="selectedCharges">0 Properties selected</p>
							<?php if ($title == 'Disbursements'){
									echo '<button type="submit"  class="slgrid">Generate Disbursement Reports</button>';
								} else {
									echo '<button type="submit"  class="slgrid">Generate Capital Calls</button>';
								} 
								echo '<input type="hidden"  name="type" value="'.$title.'"></input>';
							?>
					</form>               
				</div>
            </div>
    </div>

</div>

<script>
	//var date = $(this).closest('.modal').find('input[name="capital_as_of_date"]').val();
	var sqldate = <?= "'".$date."'" ?>;
	//var sqldate =  date.replace(/\//g, '-');
    var template = function (data) {
        var result = [];
        //if(data.mainid) data.id = data.mainid;
        //if(!data.subid) data.subid = 0;
        //if(data.parent === undefined) {
		//var newFormat =  data.date.split("/");//.reverse().join("/");
		//var newDate = newFormat[2] + '-' + newFormat[0] + '-' + newFormat[1];
		result.push(['row[' + data.id + '][id]', data.id]);
		result.push(['row[' + data.id + '][property]', data.name]);
		result.push(['row[' + data.id + '][bank]', data.bank_balance || 0 ]);
		result.push(['row[' + data.id + '][payables]', data.payables || 0]);
        //}
		result.push(['row[' + data.id + '][reserves]', parseFloat(data.reserves.replace(/,/g, '')) || 0]);
        result.push(['row[' + data.id + '][sd]', data.sd|| 0]);
        result.push(['row[' + data.id + '][lmr]', data.lmr || 0]);
        result.push(['row[' + data.id + '][income]', data.income|| 0 ]);
		result.push(['row[' + data.id + '][expense]', data.expense || 0]);
		result.push(['row[' + data.id + '][additional_expense]', data.additional_expense]);
        result.push(['row[' + data.id + '][included_in_payables]', data.included_in_payables || 0]);
		result.push(['row[' + data.id + '][cc_amt]', data.cc_amt || 0]);
		result.push(['row[' + data.id + '][cc_notes]', data.cc_notes]);

		return result;
		
    };
    var grid = $('.modal').last().formGridSlick({
		
        template: template,
        dataUrl: JS.baseUrl+ 'disburse/capitalFunction/'+sqldate,
        iuUrl: 'properties/instantUpdateSettings',
        type: 'capital'
	});
	
	$('#capital_as_of_date').blur(function(e){
		console.log("changed");
		ndate = e.target.value;
		console.log(e.target);
        var date = new Date(ndate);
		console.log(grid.data);
		date = date.getFullYear() + "-"+ (date.getMonth()+1)+ "-"+date.getDate();
		$('.modal').last().formGridSlick({
		
        template: template,
        dataUrl: JS.baseUrl+ 'disburse/capitalFunction/'+date,
        iuUrl: 'properties/instantUpdateSettings',
        type: 'capital'
	    });
	});
	function refreshGrid(ndate){
		var date = new Date(ndate);
		console.log(grid.data);
		date = date.getFullYear() + "-"+ (date.getMonth()+1)+ "-"+date.getDate();
		$('.modal').last().formGridSlick({
		
        template: template,
        dataUrl: JS.baseUrl+ 'disburse/capitalFunction/'+date,
        iuUrl: 'properties/instantUpdateSettings',
        type: 'capital'
	    });
	}

	
	

    //prcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    //vcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    //payBill.setTransactionsProperties(< ?php echo $jTransactions; ?>);
    

</script>





