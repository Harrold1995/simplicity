<div class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <!--<div id="infoMessage"><?php echo $message; ?></div>-->
      <form action="<?php echo $target; ?>" method="post">
        
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $title; ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
  
        <div class="modal-body">
          <div class="container-fluid">
          <p><?php echo sprintf(lang('delete_group_subheading'), $group->name); ?></p>
            <div class="row mt-3">
              <div class="col">
                <div class="form-group row">
                  <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio1" name="confirm" class="custom-control-input" value="yes" checked="checked">
                  <label class="custom-control-label" for="customRadio1" style="padding-left:20px">Yes</label>
                </div>
                <div class="custom-control custom-radio">
                  <input type="radio" id="customRadio2" name="confirm" class="custom-control-input" value="no">
                  <label class="custom-control-label" for="customRadio2" style="padding-left:20px">No</label>
                </div>
              </div>          
            </div> 
                 
          </div>        
        </div>
        
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
            Save
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Cancel
          </button>
        </div>
        
      </form>
    </div>
  </div>
</div>
