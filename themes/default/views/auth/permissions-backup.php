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
              
                
                
        
                <?php foreach ($permissions as $key => $permission) { 
                  if ($key == 'id' || $key == 'group_id') continue; 
                  $field = explode('_',$key);
                  if($word != $field[0]){ ?>
                    <div class="col-lg-12">
                    <?php echo ucfirst($field[0]); ?>
                    </div> 
                    <?php $word = $field[0];
                  } ?>

                <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                  <input type="hidden" name="<?=$key?>" value="0"/>
                  <input type="checkbox" name="<?=$key?>" class="custom-control-input" id="<?=$key?>" value="1" <?= ($permission == 1) ? 'checked' : ''; ?>>
                    <label class="custom-control-label checkbox-right" for="<?=$key?>"><?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?></label>
                </div>

                <?php } ?>
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
