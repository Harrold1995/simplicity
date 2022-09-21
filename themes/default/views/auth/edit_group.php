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
            
            <div class="row mt-3">
              <div class="col">
                
                <div class="form-group row">
                  <label for="group_name" class="col-auto">Group Name</label>
                  <div class="col field-input">
                    <input type="text" value="<?= $group_name[value] ?>" class="form-control" name="group_name" id="group_name">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="group_description" class="col-auto">Group Description</label>
                  <div class="col field-input">
                    <input type="text" value="<?= $group_description[value] ?>" class="form-control" name="group_description" id="group_description">
                  </div>
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
