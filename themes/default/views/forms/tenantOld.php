<div class="modal fade tenant-modal" tabindex="-1" role="dialog" aria-hidden="true">

  <div class="modal-dialog modal-dialog-centered modal-md modal-md-special" role="document">
    <div class="modal-content">
      <form action="<?php echo $target; ?>" method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container-fluid">

            <div class="row mt-3">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="first_name" class="col-auto">First Name</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->first_name) ? $tenant->first_name : '' ?>" class="form-control" name="first_name" id="first_name" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="last_name" class="col-auto">Last Name</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->last_name) ? $tenant->last_name : '' ?>" class="form-control" name="last_name" id="last_name" placeholder="">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="email" class="col-auto">Email</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->email) ? $tenant->email : '' ?>" class="form-control" name="email" id="email" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="phone" class="col-auto">Phone</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->phone) ? $tenant->phone : '' ?>" class="form-control" name="phone" id="phone" placeholder="">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="address_line_1" class="col-auto">Address Line 1</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->address_line_1) ? $tenant->address_line_1 : '' ?>" class="form-control" name="address_line_1" id="address_line_1" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="address_line_2" class="col-auto">Address Line 2</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->address_line_2) ? $tenant->address_line_2 : '' ?>" class="form-control" name="address_line_2" id="address_line_2" placeholder="">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="city" class="col-auto">City</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->city) ? $tenant->city : '' ?>" class="form-control" name="city" id="city" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="state" class="col-auto">State</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->state) ? $tenant->state : '' ?>" class="form-control" name="state" id="state" placeholder="">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="country" class="col-auto">Country</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->country) ? $tenant->country : '' ?>" class="form-control" name="country" id="country" placeholder="">
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="area_code" class="col-auto">Area Code</label>
                  <div class="col field-input">
                    <input type="text" value="<?= isset($tenant) && isset($tenant->area_code) ? $tenant->area_code : '' ?>" class="form-control" name="area_code" id="area_code" placeholder="">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="def_expense_acc" class="col-auto">Default Expense Account</label>
                  <div class="col field-select">
                    <select class="form-control" id="def_expense_acc" name="def_expense_acc">
                      <option value="1">Capital One</option>
                      <option value="2">Bank Of America</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <div class="form-group row">
                  <label for="status" class="col-auto">Status</label>
                  <div class="col field-select">
                    <select class="form-control" id="status" name="status">
                      <option value="1">Active</option>
                      <option value="2">Inactive</option>
                    </select>
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