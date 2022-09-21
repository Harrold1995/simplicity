<article id="timesheetArticle">
					<ul class="list-horizontal a">
						<li><a href="./">Time Sheets</a></li>
						<li><a href="./">Employee Info</a></li>
						<li><a href="./">Reports</a></li>
					<!--</ul>-->
						<!--<ul class="list-horizontal a" style="float: right;">-->
							<!--<li style="margin-right: 5px;">
								<!- -<label for="inbetweenDates"><input type="radio" id="inbetweenDates" name="inbetweenDates"><div class="input"></div>From</label>- ->
								<input type="text" data-toggle="datepicker" id="fromDate"  class="leaveEmpty" >
								</li>
								<li style="margin-right: 5px;">
								<span>To</span>
								</li>
								<li style="margin-right: 20px;">
								<input type="text" data-toggle="datepicker" id="toDate" class="leaveEmpty">
							</li>
							<li class="check-a"><label for="alldates"><input type="checkbox" id="alldates" name="alldates" checked>All dates<div class="input"></div></label></li>-->
						</ul>
					<header class="heading-a">
						<ul>
							<li style="margin-right: 5px;">
								<!--<label for="inbetweenDates"><input type="radio" id="inbetweenDates" name="inbetweenDates"><div class="input"></div>From</label>-->
								<input type="text" data-toggle="datepicker" id="fromDate"  class="leaveEmpty" >
								</li>
								<li style="margin-right: 5px;">
								<span>To</span>
								</li>
								<li style="margin-right: 20px;">
								<input type="text" data-toggle="datepicker" id="toDate" class="leaveEmpty">
							</li>
							<li class="check-a"><label for="alldates"><input type="checkbox" id="alldates" name="alldates" checked>All dates<div class="input"></div></label></li>
							<li class="size-a"><i class="icon-user"></i><?= isset($employee) ? $employee->first_name .' ' . $employee->last_name : '';?></li>
						</ul>
						<ul>
							<li><a href="./">New Entry</a></li>
							<li><a href="./">Export</a></li>
							<li><a href="./">Approve</a></li>
						</ul>
					</header>
					<ul class="list-c">
                    <?php if($SingleEmployeeTimesheet) $totalHours = $SingleEmployeeTimesheet['totalHours']; unset($SingleEmployeeTimesheet['totalHours']);?>
                    <?php if($SingleEmployeeTimesheet) $totalpay = $SingleEmployeeTimesheet['totalPay']; unset($SingleEmployeeTimesheet['totalPay']);?>
                    <?php if($SingleEmployeeTimesheet) $totalLunchHours = $SingleEmployeeTimesheet['totalLunchHours']; unset($SingleEmployeeTimesheet['totalLunchHours']);?>t
						<li><i class="icon-time"></i><span  id="timesheetTotalHours"><?= isset($totalHours) ? $totalHours  : '0';?></span><span>Total Hours</span></li>
						<li><i class="icon-time"></i><span id="timesheetTotalLunchHours"> <?= isset($totalLunchHours) ? $totalLunchHours  : '0';?></span> <span>Lunch Hours</span></li>
						<li><i class="icon-dollar"></i> <span id="timesheetTotalAmount"><?= isset($totalpay) ? '$' . number_format($totalpay, 2) : '0';?></span> <span>Total pay</span></li>
					</ul>

                        
<div id="DataTables_Table_7_wrapper" class="dataTables_wrapper no-footer has-table-c mobile-hide b">
            <div class="dataTables_scroll">
              <div class="dataTables_scrollHead">
                <div class="dataTables_scrollHeadInner">
                  <table class="mobile-hide dataTable no-footer table-e" role="grid">
                    <thead>
                        <tr role="row">
                            <th width="10%">Date</th>
							<th width="10%">Start</th>
							<th width="10%">End</th>
							<th width="10%">Duration</th>
							<th width="10%">Project</th>
							<th width="10%">Task</th>
							<th width="10%">Rate</th>
							<th width="10%">Total</th>
							<th width="10%">Bill?</th>
							<th width="10%" class="text-right"><a href="#" id="addTime" employee-id="<?= isset($employee) ? $employee->id  : '';?>"><i class="icon-plus-thin"></i> <span class="hidden">Add</span></a></th>
                      </tr>
                      </thead>
                  </table>
                </div>
              </div>
            <div class="dataTables_scrollBody" style="height: calc(100vh - 550px); overflow:auto">
            <div class="table-wrapper"  tabindex="-1">
              <table class="table-b clickable" id="DataTables_Table_7" role="grid" aria-describedby="DataTables_Table_7_info">
                <thead>
                </thead>
					        <tbody id="employeeTimesheet">

                                <?php  if (isset($SingleEmployeeTimesheet)){
                                    foreach ($SingleEmployeeTimesheet as $timesheet) {?>
                                
                                    <tr data-id="<?=$timesheet->th_id?>" data-type="">
                                        <td width="10%"><?=isset($timesheet->date) ? $timesheet->date : '';?></td>
                                        <td width="10%"><?=isset($timesheet->start_time) ? date("g:i:s a", strtotime($timesheet->start_time)) : '';?></td>
                                        <td width="10%"><?=isset($timesheet->end_time) ? date("g:i:s a", strtotime($timesheet->end_time)) : '';?></td>
                                        <td width="10%"><?=isset($timesheet->duration) ? $timesheet->duration : '';?></td>
                                        <td width="10%"><?=isset($timesheet->project) ? $timesheet->project : '';?></td>
                                        <td width="10%"><?=isset($timesheet->task) ? $timesheet->task : '';?></td>
                                        <td width="10%"><?=isset($timesheet->rate) ? $timesheet->rate : '';?></td>
                                        <td width="10%"><?=isset($timesheet->total) ? '$' . number_format($timesheet->total, 2) : '';?></td>
																				<td width="10%">
																					<ul class="check-a a">
																						<li><label for="bill" class="checkbox <?= isset($timesheet) && ($timesheet->bill == 1) ? 'active' : '' ?>"><input type="hidden" name="bill" value="0" /><input type="checkbox" value="1" <?= isset($bill) && ($timesheet->bill == 1) ? 'checked' : '' ?> id="bill"  name="bill"  class="hidden" aria-hidden="true"><div class="input" ></div> bill?</label></li>
																					</ul>
																				</td>
                                        <!-- <td width="10%">< ?=isset($timesheet->bill) ? $timesheet->bill : '';?></td> -->
                                        <td width="10%" class="text-right">
                                                <a href="./">Edit</a>
                                                <a class="overlay-l" href="./">Delete</a>
                                        </td>
                                        <!-- <td width="10%">$400.00</td> -->
                                        </tr> 
                                    <?php }}else {echo "<tr><td class='text-center' style='color: #f37ce4;font-size: large;'><strong>No Timesheet for this profile</strong></td></tr>";
                                    } ?>
                            </tbody>
														<style type="text/css" onload="dateFilter($(this).closest('#timesheetArticle'))"></style>
                  </table>
                </div>
              </div>
            </div>
          </div>
                                   
					</table>
					<table class="table-e">
						<tr>
							<th>Date</th>
							<th>Start</th>
							<th>End</th>
							<th>Duration</th>
							<th>Project</th>
							<th>Task</th>
							<th>Rate</th>
							<th>Total</th>
							<th>Bill?</th>
							<th></th>
						</tr>
						<tr>
							<td>Date</td>
							<td>Start</td>
							<td>End</td>
							<td>Duration</td>
							<td>Project</td>
							<td>Task</td>
							<td>Rate</td>
							<td>Total</td>
							<td>Bill?</td>
							<td></td>
						</tr>
					</table>
				</article>

                </main>

<!-- for adding time -->
<?php require_once VIEWPATH . 'timesheet/timesheet-add.php';?>

<script >
  var timesheet = <?php echo $SingleEmployeeTimesheet ? json_encode($SingleEmployeeTimesheet) : '0'?>;
    for (var i = 0; i < timesheet.length; i++) {
      if(timesheet[i].start_time != null) { timesheet[i].start_time = formatAMPM(timesheet[i].start_time);}
      if(timesheet[i].end_time != null){timesheet[i].end_time = formatAMPM(timesheet[i].end_time); }
    }
	console.log(timesheet);
	function dateFilter(modal){
		JS.datePickerInit(modal);
		filters(timesheet, modal);
		console.log(modal);
	}

               //from and to date eventlistner
                function filters(timesheet, body){
                   var fromDate = $(body).find('#fromDate');
                    var toDate = $(body).find('#toDate');
                    var allDates = $(body).find('#alldates');
                    var timesheetBody = $(body).find('#employeeTimesheet');
                    $(fromDate).on('input',  function(){
											console.log('fromDate');
                        if($(allDates).closest('label').hasClass('active')){
                            $(allDates).closest('label').trigger('click');
                        }
                        filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
                    });
                    $(toDate).on('input',  function(){
											console.log('toDate');
                        if($(allDates).closest('label').hasClass('active')){
                          $(allDates).closest('label').trigger('click');
                        }
                        filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
                   });

                    $(allDates).closest('label').on('click',  function(){
                        console.log('allDates');
                        var checked = $(allDates).prop("checked");
                        console.log(checked);
                        if(checked){
                            $(toDate).val('');
                            $(fromDate).val('');
                        }
                        filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
                   });
                }
                //filters transactions based on to and from dates
                function filterTimesheet(timesheet, body, fromDate, toDate){
                    console.log(fromDate);
                    //console.log(fromDate.value);
                    var dateSearchTimesheet = [];
                    var total = 0;
                    var totalSeconds = 0;
                    var totalLunchSeconds = 0;
                        for (var i = 0; i < timesheet.length; i++) {
                            var oldDate = new Date(timesheet[i].date.replace(/-/g, '\/'));
                            var newFromDate = new Date(fromDate.val());
                            var newToDate = new Date(toDate.val());
                                if((newFromDate > 0 ? oldDate >= newFromDate : true) && (newToDate > 0 ? oldDate <= newToDate : true)){
                                  total += timesheet[i].total;
                                  if(timesheet[i].project == 'Lunch'){
                                    totalLunchSeconds += explode_time(timesheet[i].duration);
                                  }
                                  totalSeconds += explode_time(timesheet[i].duration);
                                  dateSearchTimesheet.push(timesheet[i]);
                                }
                        }
                        var totalHours = secondsToHms(totalSeconds);
                        var totalLunchHours = secondsToHms(totalLunchSeconds);
                        //console.log(secondsToHms(totalSeconds));
                        //console.log(total);
                        //console.log(dateSearchTimesheet);
                        displayFilteredTimesheet(dateSearchTimesheet, body);
                        calculateTotals(totalHours, totalLunchHours, total);
                }

                function displayFilteredTimesheet(timesheet, body){
                  var newTimesheet = '';
                  for (var i = 0; i < timesheet.length; i++) {
                    newTimesheet += `<tr data-id="` + timesheet[i].th_id + `" data-type="">
                                        <td width="10%">` + timesheet[i].date + `</td>
                                        <td width="10%">` + timesheet[i].start_time + `</td>
                                        <td width="10%">`;
                                        newTimesheet += timesheet[i].end_time ?  timesheet[i].end_time : "";
                                        newTimesheet += `</td>
                                        <td width="10%">` + timesheet[i].duration + `</td>
                                        <td width="10%">` + timesheet[i].project + `</td>
                                        <td width="10%">` + timesheet[i].task + `</td>
                                        <td width="10%">` + timesheet[i].rate + `</td>
                                        <td width="10%">$` + number_format(timesheet[i].total) + `</td>
																				<td width="10%">
																					<ul class="check-a a">
                                            <li><label for="bill" class="checkbox `;
                                            newTimesheet += timesheet[i].bill == 1 ? 'active' : '';
                                            newTimesheet += ` "><input type="hidden" name="bill" value="0" /><input type="checkbox" value="1" `;
                                            newTimesheet += timesheet[i].bill == 1 ? 'checked' : '';
                                            newTimesheet += ` id="bill"  name="bill"  class="hidden" aria-hidden="true"><div class="input" ></div> bill?</label></li>
																					</ul>
																				</td>
                                        
                                        <td width="10%" class="text-right">
                                                <a href="./">Edit</a>
                                                <a class="overlay-l" href="./">Delete</a>
                                        </td>
                                        
                                        </tr>`;
                  }
                  $(body).empty().append(newTimesheet);
                }


                function formatAMPM(timeString) {
                  console.log(timeString)
                  var H = +timeString.substr(0, 2);
                  var h = H % 12 || 12;
                  var ampm = (H < 12 || H === 24) ? "AM" : "PM";
                  timeString = h + timeString.substr(2, 3) + ampm;
                  return timeString;
                }

                  function explode_time(time) { //explode time and convert into seconds
                      var time = time.split(':');
                      time = time[0] * 3600 + time[1] * 60;
                      //console.log(time);
                      return time;
                  }
                  //converts seconds to hours and minutes
                  function secondsToHms(d) {
                    d = Number(d);
                    var h = Math.floor(d / 3600);
                    var m = Math.floor(d % 3600 / 60);
                    //var s = Math.floor(d % 3600 % 60);
                    var hDisplay = h > 0 ? h + ':' : "0" + ':';
                    var mDisplay = m > 0 ? m : "";
                    // var hDisplay = h > 0 ? h + (h == 1 ? " hour, " : " hours, ") : "";
                    // var mDisplay = m > 0 ? m + (m == 1 ? " minute, " : " minutes, ") : "";
                    //var sDisplay = s > 0 ? s + (s == 1 ? " second" : " seconds") : "";
                    return hDisplay + mDisplay;// + sDisplay; 
                }
                //sets total hours and amount
                function calculateTotals(totalHours, totalLunchHours, totalAmount){
                   $('body').find('#timesheetTotalHours').empty().append(totalHours);
                   $('body').find('#timesheetTotalLunchHours').empty().append(totalLunchHours);
                   $('body').find('#timesheetTotalAmount').empty().append('$' + number_format(totalAmount));
                }
      
                JS.checkboxes();
 </script>