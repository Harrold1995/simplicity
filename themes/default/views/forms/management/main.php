<div class="modal fade capital-modal" id="Management" tabindex="-1" role="dialog"  type="Management" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		    <div id="root" class="no-print">
				<div class="modal-content text-primary popup-a form-entry shown" style="width: 100%;  padding: 30px; margin-left: 50px;">
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
										<input style="width: 18%; display: inline;" data-toggle="datepicker" value="<?=$date ?>" name = "as_of_date" id="capital_as_of_date" triggerchange="true" >
										

										<span style="margin-right: 12px;">Check Date </span>
										<input style="width: 18%; display: inline;" data-toggle="datepicker" value="" name="check_date" >
								</div>
							</div >
							<div class="formGridSlickTable mobile-hide utilities" style="z-index: 2; height:100%; ">

							</div>
							<p class="strong scheme-b m20 size-a overlay-a remove" id="selectedCharges">0 Properties selected</p>
							<button type="submit"  class="slgrid">Create Management Checks</button>
					</form>               
				</div>
            </div>
    </div>

</div>

<script>
	var sqldate = <?php echo "'".$date."'"; ?>;
    var template = function (data) {
        var result = [];
		result.push(['row[' + data.id + '][id]', data.id]);
		result.push(['row[' + data.id + '][property_id]', data.property_id]);
		result.push(['row[' + data.id + '][amount]', data.amount || 0 ]);
		result.push(['row[' + data.id + '][default_bank]', data.default_bank || 0]);
		result.push(['row[' + data.id + '][frequency]', data.frequency || 0]);
		result.push(['row[' + data.id + '][account_id]', data.account_id || 0]);
		result.push(['row[' + data.id + '][calcAmount]', data.calcAmount || 0]);
		result.push(['row[' + data.id + '][memo]', data.memo || 0]);
		result.push(['row[' + data.id + '][vendor]', data.vendor || 0]);

		return result;
		
    };
    var grid = $('.modal').last().formGridSlick({
		
        template: template,
        dataUrl: JS.baseUrl+ 'management/managementFunction/'+sqldate,
        iuUrl: 'properties/instantUpdateSettings',
        type: 'capital'
	});
	$('#capital_as_of_date').datepicker();
	$('#capital_as_of_date').change(function(e){
		ndate = e.target.value;
        var date = new Date(ndate);
		date = date.getFullYear() + "-"+ (date.getMonth()+1)+ "-"+date.getDate();
		$('.modal').last().formGridSlick({
		
        template: template,
        dataUrl: JS.baseUrl+ 'management/managementFunction/'+date,
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
        dataUrl: JS.baseUrl+ 'management/managementFunction/'+date,
        iuUrl: 'properties/instantUpdateSettings',
        type: 'capital'
	    });
	}

	
    

</script>





