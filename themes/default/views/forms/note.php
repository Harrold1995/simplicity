            
<div class="modal fade notes-modal" id="notesModal" tabindex="-1" role="dialog" main-id=<?= isset($account) && isset($account->id) ? $account->id : '-1' ?> type="account" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height: 400px;">
                  <form action="<?php echo $target; ?>" method="post"type="account">
                  <header class="modal-header">
					<h2 class="text-uppercase">New Note</h2>

				</header>      
    <?php $userId = $this->ion_auth->get_user_id(); ?>
                                                <p>
                                                    <label for="title">Title</label>
                                                    <input type="text" id="title" name="title">
                                                </p>
                                                <p>
                                                    <label for="note">Note</label>
                                                    <textarea  style="height:40px;" id="note" name="note"></textarea>
                                                </p>
                                                <input type="hidden" id="object_id" name="object_id" value="<?=isset($propertyId) ? $propertyId : '';?>"/>
                                                <input type="hidden" name="profile_id" value="<?=isset($userId) ? $userId : '';?>"/>
                                                <p><button type="submit" after="mclose" id="noteSubmitButton">Save</button></p>
                                                <p><button id="noteCancelButton" type="button">cancel</button></p>
                        <!--</form>-->

 <!--</section>-->
            <footer>
              <!--<ul class="list-btn">
                <li><button type="submit" after="mnew">Save &amp; New</button></li>
                <li><button type="submit" after="mclose">Save &amp; Close</button></li>
                <li><button>Duplicate</button></li>
                <li><button type="button">Cancel</button></li>

              </ul>-->
            </footer>

          </form>

      </div>
		</div>
  </div>
</div>






<script>

 


</script>