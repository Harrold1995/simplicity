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
                                    <label for="first_name" class="col-auto">First Name</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $first_name[value] ?>" class="form-control" name="first_name" id="first_name">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="first_name" class="col-auto">First Name</label>
                                    <div class="col field-input">
                                        <span class="select">
                                            <select class="form-control editable-select" id="profile_id" name="newCharge[profile_id]">
                                                    <option value="0"></option>
                                                        <?php
                                                        foreach ($tenants as $tenant) {
                                                            echo '<option value="' . $tenant->id . '">' . $tenant->name . '</option>';
                                                        } ?>
                                            </select>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="last_name" class="col-auto">Last Name</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $last_name[value] ?>" class="form-control" name="last_name" id="last_name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="identity" class="col-auto">Username</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $identity[value] ?>" class="form-control" name="identity" id="identity">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-auto">Email</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $email[value] ?>" class="form-control" name="email" id="email">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="emailpass" class="col-auto">Email App Password</label>
                                    <div class="col field-input">
                                        <input type="password" value="<?= $emailpass[value] ?>" class="form-control" name="email_password" id="emailpass">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="company" class="col-auto">Company</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $company[value] ?>" class="form-control" name="company" id="company">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phone" class="col-auto">Phone</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= $phone[value] ?>" class="form-control" name="phone" id="phone">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-auto">Password</label>
                                    <div class="col field-input">
                                        <input type="password" class="form-control" name="password" id="password">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password_confirm" class="col-auto">Confirm Password</label>
                                    <div class="col field-input">
                                        <input type="password" class="form-control" name="password_confirm" id="password_confirm">
                                    </div>
                                </div>

                                <?php if ($this->ion_auth->is_admin()){ ?>

                                <p><?php echo lang('edit_user_groups_heading'); ?></p>
                                <div class="row">
                                    <?php foreach ($groups as $group) { ?>
                                        <?php
                                        $gID = $group['id'];
                                        $checked = null;
                                        $item = null;
                                        foreach ($currentGroups as $grp) {
                                            if ($gID == $grp->id) {
                                                $checked = ' checked="checked"';
                                                break;
                                            }
                                        }
                                        ?>

                                        <div class="col-auto  custom-control custom-checkbox form-group">
                                            <input type="checkbox" name="groups[]" class="custom-control-input" id="group-<?php echo $group['id']; ?>" value="<?php echo $group['id']; ?>"<?php echo $checked; ?>>
                                            <label class="custom-control-label checkbox-right" for="group-<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?></label>
                                        </div>

                                    <?php } ?>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" refresh>
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
