<article class="right-side">
    <div id="users-list" class="dataTables_wrapper no-footer has-table-c mobile-hide b">
        <h2>Users</h2>
        <div>
            <div class="table-wrapper" tabindex="-1">
                <table class="table-b" id="DataTables_Table_4" role="grid" aria-describedby="DataTables_Table_4_info">
                    <thead>
                        <tr>
                            <th><?php echo lang('index_fname_th'); ?></th>
                            <th><?php echo lang('index_lname_th'); ?></th>
                            <th><?php echo lang('index_username_th'); ?></th>
                            <th><?php echo lang('index_email_th'); ?></th>
                            <th><?php echo lang('index_groups_th'); ?></th>
                            <th><?php echo lang('index_status_th'); ?></th>
                            <th><?php echo lang('index_action_th'); ?></th>
                            <th><?php echo lang('index_action_th'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user->first_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user->last_name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php foreach ($user->groups as $group): ?>
                                        <a href="#" data-type="custom" url="auth/edit_group" data-id="<?= $group->id ?>" data-mode="edit"><?= $group->name ?></a>
                                        <br/>
                                    <?php endforeach ?>
                                </td>

                                <td><?php echo ($user->active) ? '<a href="#" data-type="custom" url="auth/deactivate/' . $user->id . '" data-id="' . $user->id . '" data-mode="edit">Active</a>' : '<a href="auth/activate/' . $user->id . '">Inactive</a>'; ?></td>
                                <td><?php echo htmlspecialchars($user->email, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <a href="#" data-type="custom" url="auth/user_logs" data-id="<?= $user->id ?>" data-mode="edit">Log</a>
                                    <span> | </span>
                                    <a href="#" data-type="custom" url="auth/edit_user" data-id="<?= $user->id ?>" data-mode="edit">Edit</a>
                                    <span> | </span>
                                    <a href="#" data-type="custom" url="auth/delete_user/<?= $user->id ?>" data-id="<?= $user->id ?>" data-mode="edit" style="color:red">Delete</a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p><a href="#" data-type="custom" url="auth/create_user" data-mode="add">Create a new user</a></p>

        <h2 style="margin-top:35px">Groups</h2>
        <div>
            <div class="table-wrapper" tabindex="-1">
                <table class="table-b" id="DataTables_Table_4" role="grid" aria-describedby="DataTables_Table_4_info">
                    <thead>
                        <tr>
                            <th>Group ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Permissions</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groups as $group): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group->id, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($group->name, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($group->description, ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo '<a href="#" data-type="custom" url="auth/change_permissions/' . $group->id . '" data-id="' . $group->id . '" data-mode="edit">Change</a>'; ?></td>

                                <td>
                                    <a href="#" data-type="custom" url="auth/edit_group" data-id="<?= $group->id ?>" data-mode="edit">Edit</a>
                                    <span> | </span>
                                    <?php echo '<a href="#" data-type="custom" url="auth/delete_group/' . $group->id . '" data-id="' . $group->id . '" data-mode="edit" style="color:red">Delete</a>'; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <p><a href="#" data-type="custom" url="auth/create_group" data-mode="add">Create a new group</a></p>
    </div>
</article>

<aside class="left-side" style="opacity: 1;">
    <table class="tree-table" style="opacity: 1;">
        <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a id="users_permissions" href="<?=base_url('/users')?>">Users & Permissions</a></td></tr>
        <tr <?=($page=='seditor')?' class="on" ':''?>><td><a href="<?=base_url('/settings/seditor')?>">Settings Editor</a></td></tr>
        <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a href="<?=base_url('/settings/ltemplates')?>">Lease Templates</a></td></tr>
        <tr <?=($page=='ltemplates')?' class="on" ':''?>><td><a id="companySettings" href="#">Company Settings</a></td></tr>
    </table>

    <!-- <table class="tree-table">
        <tbody>
            <tr>
                <td><a href="/settings/seditor">Settings Editor</a></td>
            </tr>
            <tr class="on">
                <td><a href="/settings/ltemplates">Lease Templates</a></td>
            </tr>
            <tr class="on">
                <td><a href="/users">Users &amp; Permissions</a></td>
            </tr>
        </tbody>
    </table> -->
</aside>