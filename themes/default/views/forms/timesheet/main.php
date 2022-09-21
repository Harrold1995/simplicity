
<div class="modal fade timesheet-modal" id="timesheetModal" tabindex="-1" role="dialog" main-id=<?= isset($timesheet) && isset($timesheet->id) ? $timesheet->id : '-1' ?> type="timesheet" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:520px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                    id="timesheetForm" action="<?php echo $target; ?>" method="post" class="form-fixed timesheet" type="timesheet"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					<h2 class="text-uppercase"><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						
					</nav>		
				</header>

       
       
              <div class ="has-table-c">
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>
                    <p>Date
					<label for="date"></label>
					<input id="datePicker" type="date" id="date" name="date">
				</p>
                <p>Start Time
					<label for="start_time"></label>
					<input type="datetime-local" id="start_time" name="start_time">
				</p>
                <p>End time
					<label for="end_time"></label>
					<input type="datetime-local" id="end_time" name="end_time">
				</p>
                <p>Project
					<label for="project"></label>
					<input type="text" id="project" name="project">
				</p>
                <p>Task
					<label for="task"></label>
					<input type="text" id="task" name="task">
				</p>
				<!-- <p style="height:90px;">Description
					<label for="Description"></label>
					<textarea  style="height:80px;" id="Description" name="Description"></textarea>
				</p> -->
				<p><button type="submit" after="mclose" id="addTimesheet">Save</button></p>
				<p><button id="timeCancelButton" type="button">cancel</button></p>
                    </tbody>
						  
					<tfoot>
						<tr>
						</tr>
					</tfoot>
					
				</table>
			</div>

        <footer>
        <?= $header ? 
        "<div>Last Modified $header->modified<div>
         <div>Last Modified by <a href='#!'>$header->user</a><div>" : '';
        ?>
         
        </footer>
                            </form>   
                    </div>
		</div>

</div>
</div>