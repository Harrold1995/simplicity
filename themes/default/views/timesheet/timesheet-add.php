<form action="timesheet/addTimesheet/<?=isset($employee->id) ? $employee->id : '';?>"  style= "display:none; z-index: 4000;width: 218px;position: fixed;right: 5%; background: white;"
 id="addTimeForm" method="post" class="form-fixed" type="timesheet">
 <h2>New Entry</h2>
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
				<!--<h2>New Entry</h2>
				<p>
					<label for="ffa">Date</label>
					<input type="date" id="ffa" name="ffa">
				</p>
				<p>
					<label for="ffb">Start Time</label>
					<input type="text" id="ffb" name="ffb">
				</p>
				<p>
					<label for="ffc">End time</label>
					<input type="text" id="ffc" name="ffc">
				</p>
				<p>
					<label for="ffd">Project</label>
					<input type="text" id="ffd" name="ffd" required>
				</p>
				<p>
					<label for="ffe">Task</label>
					<input type="text" id="ffe" name="ffe" required>
				</p>
				<p>
					<label for="fff">Description</label>
					<textarea id="fff" name="fff"></textarea>
				</p>
				<p><button type="submit">Save</button></p>-->
			</form>
