<div class="modal fade unit-modal" data-type="unit" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <form action="<?php echo $target; ?>" method="post" type="unit">
        <div class="modal-header ui-draggable-handle">
          <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">
            <div class="row mt-3">
              <div class="col-6">
                <div class="form-group row">
                  <label for="name" class="col-auto">Unit</label>
                  <div class="col field-input">
                      <?php if (isset($unit) && isset($unit->id)) echo '<input type="hidden" name="id" value="' . $unit->id . '"/>'; ?>
                    <input type="text" value="<?= isset($unit) && isset($unit->name) ? $unit->name : '' ?>" class="form-control" name="name" id="name" placeholder="Unit">
                  </div>
                </div>
                  <?php //if(!isset($unit) || isset($unit) && $unit->property_id != -1) {?>
                <div class="form-group row">
                  <label for="property_id" class="col-auto">Property</label>
                  <div class="col field-select">
                    <select onchange="JS.loadList('api/getUnitsList',$('[name=\'property_id\']:last').val(), '#parent_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up" id="property_id" name="property_id" modal="property" type="table" key="properties.name">
                        <?php
                        echo '<option value="-1" '.(isset($unit) && $unit->property_id == -1 ? 'selected' : '' ).'>'.$property_name.'</option>';
                        foreach ($properties as $property) {
                            echo '<option value="' . $property->id . '" ' . (isset($unit) && $unit->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                        } ?>
                    </select>
                  </div>
                </div>
                  <?php //} ?>
                <div class="form-group row">
                  <label for="unit_type_id" class="col-auto">Type</label>
                  <div class="col field-select">
                    <select class="form-control editable-select quick-add" id="unit_type_id" name="unit_type_id" type="setting" key="unit_types">
                        <?php
                        foreach ($unit_types as $k => $utype) {
                            echo '<option value="' . $k . '" ' . (isset($unit) && $unit->unit_type_id == $k ? 'selected' : '') . '>' . $utype . '</option>';
                        } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="status" class="col-auto">Status</label>
                  <div class="col field-select">
                  <select class="form-control editable-select quick-add" id="status" name="status" type="setting" key="unit_status">
                        <?php
                        foreach ($unit_status as $k => $ustatus) {
                            echo '<option value="' . $k . '" ' . (isset($unit) && $unit->status == $k ? 'selected' : '') . '>' . $ustatus . '</option>';
                        } ?>
                  </div>
                  </select>
                </div>
                <div class="form-group row">
                  <label for="floor" class="col-auto">Floor</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($unit) && isset($unit->floor) ? $unit->floor : '' ?>" class="form-control" name="floor" id="floor" placeholder="Floor">
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="form-group row">
                  <label for="parent_id" class="col-auto">Subunit of</label>
                  <div class="col field-select">
                    <select class="form-control editable-select" id="parent_id" name="parent_id">
                        <?php
                        echo '<option class="nested0" value="0" ' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '></option>';
                        if (isset($subunits))
                            foreach ($subunits as $sunit) {
                                if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
                                echo '<option data-id="'.$sunit->id.'" data-parent-id="'.$sunit->parent_id.'" class="nested'.$sunit->step.'"value="' . $sunit->id . '" ' . (isset($unit) && $unit->parent_id == $sunit->id ? 'selected' : '') . '>' . $sunit->name . '</option>';
                            } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="sq_ft" class="col-auto">SQ FT</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($unit) && isset($unit->sq_ft) ? $unit->sq_ft : '' ?>" class="form-control" id="sq_ft" name="sq_ft" placeholder="Sq. ft">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="late_charge" class="col-auto">Late Charge Setup</label>
                  <div class="col field-select">
                    <select class="form-control" id="late_charge" name="late_charge">
                      <option value="1">Default</option>
                      <option value="2">3Setting 1</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="broker" class="col-auto">Broker</label>
                  <div class="col field-select">
                    <select class="form-control" id="broker" name="broker">
                      <option value="1">Broker 1</option>
                      <option value="2">Broker 2</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="market_rent" class="col-auto">Market rent</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($unit) && isset($unit->market_rent) ? $unit->market_rent : '' ?>" class="form-control" name="market_rent" id="market_rent" placeholder="Rent">
                  </div>
                </div>

              </div>
            </div>
            <div class="row mt-3">
              <div class="col">

                <div class="form-group row">
                  <label for="memo" class="col-auto">Memo</label>
                  <div class="col field-input">
                    <textarea onkeyup="JS.textAreaAdjust(this)" class="form-control" id="memo" name="memo" rows="1"><?= isset($unit) && isset($unit->memo) ? $unit->memo : '' ?></textarea>
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
