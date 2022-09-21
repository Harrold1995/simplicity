<?php $userId = $this->ion_auth->get_user_id(); ?>
<form style= "display:none; z-index: 4000;width: 218px;position: fixed;right: 15%;top: 40%; background: white; height:400px;"
 id="noteForm" action="notes/addNote/<?=isset($addNoteForm->target) ? $addNoteForm->target : '';?>"
  method="post" class="form-fixed notesForm" type="<?=isset($addNoteForm->type) ? $addNoteForm->type : '';?>"
   formType="<?=isset($addNoteForm->target) ? $addNoteForm->target : '';?>"><!-- formType used for js reload-->
				<h2>New Note</h2>
				<p>Title
					<label for="title"></label>
					<input type="text" id="title" name="title">
				</p>
				<p style="height:200px;">Note
					<label for="note"></label>
					<textarea  style="height:180px;" id="note" name="note"></textarea>
				</p>
				<input type="hidden" id="object_id" name="object_id" value="<?=isset($addNoteForm->objectId) ? $addNoteForm->objectId : '';?>"/>
				<input type="hidden" name="profile_id" value="<?=isset($userId) ? $userId : '';?>"/>
				<p><button type="submit" after="mclose" id="noteSubmitButton">Save</button></p>
				<p><button id="noteCancelButton" type="button">cancel</button></p>
			</form>