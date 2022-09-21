<article>
					<ul class="list-horizontal a">
						<li><a href="./">Time Sheets</a></li>
						<li><a href="./">Employee Info</a></li>
						<li><a href="./">Reports</a></li>
					</ul>
					<header class="heading-a">
						<ul>
							<li><span class="strong">Week</span> 10/31/2017 -11/05/2017</li>
							<li class="size-a"><i class="icon-user"></i><?= isset($employee) ? $employee->first_name .' ' . $employee->last_name : '';?></li>
						</ul>
						<ul>
							<li><a href="./">New Entry</a></li>
							<li><a href="./">Export</a></li>
							<li><a href="./">Approve</a></li>
						</ul>
					</header>
					<ul class="list-c">
						<li><i class="icon-time"></i> 37 <span>Total Hours</span></li>
						<li><i class="icon-time"></i> 5 <span>Lunch Hours</span></li>
						<li><i class="icon-dollar"></i> $2,500 <span>Total pay</span></li>
					</ul>

                        
<div id="DataTables_Table_7_wrapper" class="dataTables_wrapper no-footer has-table-c mobile-hide b">
            <div class="dataTables_scroll">
              <div class="dataTables_scrollHead">
                <div class="dataTables_scrollHeadInner">
                  <table class="mobile-hide dataTable no-footer table-e" role="grid">
                    <thead>
                        <tr role="row">
                            <th>Date</th>
							<th>Start</th>
							<th>End</th>
							<th>Duration</th>
							<th>Project</th>
							<th>Task</th>
							<th>Rate</th>
							<th>Total</th>
							<th>Bill?</th>
							<th class="text-right"><a href="#" id="addTime" employee-id="<?= isset($employee) ? $employee->id .' ' . $employee->id : '';?>"><i class="icon-plus-thin"></i> <span class="hidden">Add</span></a></th>
                      </tr>
                      </thead>
                  </table>
                </div>
              </div>
            <div class="dataTables_scrollBody" style="height: calc(100vh - 550px);">
            <div class="table-wrapper"  tabindex="-1">
              <table class="table-b clickable" id="DataTables_Table_7" role="grid" aria-describedby="DataTables_Table_7_info">
                <thead>
                </thead>
					        <tbody>

                                <?php  if (isset($SingleEmployeeTimesheet)){
                                    foreach ($SingleEmployeeTimesheet as $timesheet) {?>
                                
                                    <tr data-id="<?=$timesheet->th_id?>" data-type="<?=$timesheet->name2?>">
                                        <td width="10%"><?=isset($timesheet->date) ? $timesheet->date : '';?></td>
                                        <td width="10%"><?=isset($timesheet->start) ? $timesheet->start : '';?></td>
                                        <td width="10%"><?=isset($timesheet->end) ? $timesheet->end : '';?></td>
                                        <td width="10%"><?=isset($timesheet->duration) ? $timesheet->duration : '';?></td>
                                        <td width="10%"><?=isset($timesheet->project) ? $timesheet->project : '';?></td>
                                        <td width="10%"><?=isset($timesheet->task) ? $timesheet->task : '';?></td>
                                        <td width="10%"><?=isset($timesheet->rate) ? $timesheet->rate : '';?></td>
                                        <td width="10%"><?=isset($timesheet->total) ? $timesheet->total : '';?></td>
                                        <td width="10%"><?=isset($timesheet->bill) ? $timesheet->bill : '';?></td>
                                        <td width="10%">$400.00</td>
                                        </tr> 
                                    <?php }}else {echo '<tr>
                                            <td>1/1/2018</td>
                                            <td>12:30 PM</td>
                                            <td>4:45 PM</td>
                                            <td>4.25 hrs</td>
                                            <td>1223 main st</td>
                                            <td>WO # 234</td>
                                            <td><span class="text-left">$</span> 23.67</td>
                                            <td><span class="text-left">$</span> 100.59</td>
                                            <td class="check"><label for="teb"><input type="checkbox" id="teb" name="teb"> <span class="hidden">Yes</span></label></td>
                                            <td class="text-right">
                                                <a href="./">Edit</a>
                                                <a class="overlay-l" href="./">Delete</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>1/1/2018</td>
                                            <td>12:30 PM</td>
                                            <td>4:45 PM</td>
                                            <td>4.25 hrs</td>
                                            <td>1223 main st</td>
                                            <td>WO # 234</td>
                                            <td><span class="text-left">$</span> 23.67</td>
                                            <td><span class="text-left">$</span> 100.59</td>
                                            <td class="check"><label for="tec"><input type="checkbox" id="tec" name="tec"> <span class="hidden">Yes</span></label></td>
                                            <td class="text-right">
                                                <a href="./">Edit</a>
                                                <a class="overlay-l" href="./">Delete</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>1/1/2018</td>
                                            <td>12:30 PM</td>
                                            <td>4:45 PM</td>
                                            <td>4.25 hrs</td>
                                            <td>1223 main st</td>
                                            <td>WO # 234</td>
                                            <td><span class="text-left">$</span> 23.67</td>
                                            <td><span class="text-left">$</span> 100.59</td>
                                            <td class="check"><label for="ted"><input type="checkbox" id="ted" name="ted"> <span class="hidden">Yes</span></label></td>
                                            <td class="text-right">
                                                <a href="./">Edit</a>
                                                <a class="overlay-l" href="./">Delete</a>
                                            </td>
                                        </tr>';
                                    // "<tr><td class='text-center' style='color: #f37ce4;font-size: large;'><strong>No Timesheet for this profile</strong></td></tr>";
                                    } ?>
                            </tbody>
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