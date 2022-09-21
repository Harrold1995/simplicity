<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auth
 * @property Ion_auth|Ion_auth_model $ion_auth        The ION Auth spark
 * @property CI_Form_validation $form_validation The form validation library
 */
class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library(array('ion_auth', 'form_validation'));
        $this->load->helper(array('url', 'language'));
        $this->form_validation->set_error_delimiters($this->config->item('error_start_delimiter', 'ion_auth'), $this->config->item('error_end_delimiter', 'ion_auth'));

        $this->lang->load('users');
    }


    public function index()
    {
        if (!$this->ion_auth->logged_in()) {
            // redirect them to the login page
            redirect('users/login', 'refresh');
        } elseif (!$this->ion_auth->is_admin()) // remove this elseif if you want to enable this for non-admins
        {
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        } else {
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

            //list the users
            $this->data['users'] = $this->ion_auth->users()->result();
            foreach ($this->data['users'] as $k => $user) {
                $this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
            }
            $this->data['groups'] = $this->ion_auth->get_groups();

            $this->meta['title'] = "Users";
            $this->meta['h2'] = "Users";
            $this->page_construct('users/index', $this->data, $this->meta);
            //$this->_render_page('users/index', $this->data);        
        }
    }

     /**
     * Create a new user
     */
    public function create_user()
    {
        $this->data['title'] = $this->lang->line('create_user_heading');

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('users', 'refresh');
        }

        $tables = $this->config->item('tables', 'ion_auth');
        $identity_column = $this->config->item('identity', 'ion_auth');
        $this->data['identity_column'] = $identity_column;

        // validate form input
        $this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'trim|required');
        if ($identity_column !== 'email') {
            $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identity_column . ']');
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');
        } else {
            $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email|is_unique[' . $tables['users'] . '.email]');
        }
        $this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'trim');
        $this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'trim');
        $this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

        if (isset($_POST) && !empty($_POST) && !isset($_POST['params'])) {
            if ($this->form_validation->run() === true) {
                $email = strtolower($this->input->post('email'));
                $identity = ($identity_column === 'email') ? $email : $this->input->post('identity');
                $password = $this->input->post('password');

                $additional_data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'email_password' => $this->input->post('email_password'),
                );
            }
            if ($this->form_validation->run() === true && $this->ion_auth->register($identity, $password, $email, $additional_data)) {
                // check to see if we are creating the user
                // redirect them back to the admin page
                
                echo json_encode(array('type' => 'success', 'message' => $this->ion_auth->messages()));
                return;
            } else {
                echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors()));
                return;
            }
        }
            // display the create user form
            // set the flash data error message if there is one
            $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

            $this->data['target'] = 'users/create_user';

            $this->data['first_name'] = array(
                'name' => 'first_name',
                'id' => 'first_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('first_name'),
            );
            $this->data['last_name'] = array(
                'name' => 'last_name',
                'id' => 'last_name',
                'type' => 'text',
                'value' => $this->form_validation->set_value('last_name'),
            );
            $this->data['identity'] = array(
                'name' => 'identity',
                'id' => 'identity',
                'type' => 'text',
                'value' => $this->form_validation->set_value('identity'),
            );
            $this->data['email'] = array(
                'name' => 'email',
                'id' => 'email',
                'type' => 'text',
                'value' => $this->form_validation->set_value('email'),
            );
            $this->data['emailpass'] = array(
                'name' => 'email_password',
                'id' => 'emailpass',
                'type' => 'password',
                'value' => $this->form_validation->set_value('email_password'),
            );
            $this->data['company'] = array(
                'name' => 'company',
                'id' => 'company',
                'type' => 'text',
                'value' => $this->form_validation->set_value('company'),
            );
            $this->data['phone'] = array(
                'name' => 'phone',
                'id' => 'phone',
                'type' => 'text',
                'value' => $this->form_validation->set_value('phone'),
            );
            $this->data['password'] = array(
                'name' => 'password',
                'id' => 'password',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password'),
            );
            $this->data['password_confirm'] = array(
                'name' => 'password_confirm',
                'id' => 'password_confirm',
                'type' => 'password',
                'value' => $this->form_validation->set_value('password_confirm'),
            );

            $this->_render_page('users/create_user', $this->data);
        
    }

    /**
     * Edit a user
     *
     * @param int|string $id
     */
    public function edit_user($id = 1)
    {
        
        $this->data['title'] = $this->lang->line('edit_user_heading');

        if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id))) {
            redirect('users', 'refresh');
        }

        $user = $this->ion_auth->user($id)->row();


        // validate form input
        if($user->username != $this->input->post('identity')) $this->form_validation->set_rules('identity', $this->lang->line('create_user_validation_identity_label'), 'trim|required|is_unique[users.username]');
        $this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'trim|required');
        $this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'trim|required');
        $this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'trim|required');
        $this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'trim|required');       
        $this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'trim|required|valid_email');

        if (isset($_POST) && !empty($_POST) && !isset($_POST['params'])) {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === false || $id != $this->input->post('id')) {
                //show_error($this->lang->line('error_csrf'));
            }

            // update the password if it was posted
            if ($this->input->post('password')) {
                $this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
                $this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
            }

            if ($this->form_validation->run() === true) {
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'company' => $this->input->post('company'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'username' => $this->input->post('identity'),
                    'email_password' => $this->input->post('email_password'),
                );

                // update the password if it was posted
                if ($this->input->post('password')) {
                    $data['password'] = $this->input->post('password');
                }

                // Only allow updating groups if user is admin
                if ($this->ion_auth->is_admin()) {
                    // Update the groups user belongs to
                    $groupData = $this->input->post('groups');

                    if (isset($groupData) && !empty($groupData)) {
                        $this->ion_auth->remove_from_group('', $id);

                        foreach ($groupData as $grp) {
                            $this->ion_auth->add_to_group($grp, $id);
                        }
                    }
                }

                // check to see if we are updating the user
                if ($this->ion_auth->update($id, $data)) {
                    echo json_encode(array('type' => 'success', 'message' => 'User successfully changed.'));
                    return;
                } else {
                    // redirect them back to the admin page if admin, or to the base url if non admin
                    $this->session->set_flashdata('message', $this->ion_auth->errors());
                    if ($this->ion_auth->is_admin()) {
                        redirect('users', 'refresh');
                    } else {
                        redirect('/', 'refresh');
                    }
                }
            } else {
                echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors()));
                return;
            }
        }
        $id = $this->input->post('id');

        $user = $this->ion_auth->user($id)->row();
        $groups = $this->ion_auth->groups()->result_array();
        $currentGroups = $this->ion_auth->get_users_groups($id)->result();

        // display the edit user form
        $this->data['csrf'] = $this->_get_csrf_nonce();

        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));


        $this->data['target'] = 'users/edit_user/'.$id;
        // pass the user to the view
        $this->data['user'] = $user;
        $this->data['groups'] = $groups;
        $this->data['currentGroups'] = $currentGroups;

        $this->data['first_name'] = array(
            'name' => 'first_name',
            'id' => 'first_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('first_name', $user->first_name),
        );
        $this->data['last_name'] = array(
            'name' => 'last_name',
            'id' => 'last_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('last_name', $user->last_name),
        );
        $this->data['email'] = array(
            'name' => 'email',
            'id' => 'email',
            'type' => 'text',
            'value' => $this->form_validation->set_value('email', $user->email),
        );
        $this->data['emailpass'] = array(
            'name' => 'email_password',
            'id' => 'emailpass',
            'type' => 'password',
            'value' => $this->form_validation->set_value('email_password', $user->email_password),
        );
        $this->data['identity'] = array(
            'name' => 'identity',
            'id' => 'identity',
            'type' => 'text',
            'value' => $this->form_validation->set_value('identity', $user->username),
        );
        $this->data['company'] = array(
            'name' => 'company',
            'id' => 'company',
            'type' => 'text',
            'value' => $this->form_validation->set_value('company', $user->company),
        );
        $this->data['phone'] = array(
            'name' => 'phone',
            'id' => 'phone',
            'type' => 'text',
            'value' => $this->form_validation->set_value('phone', $user->phone),
        );
        $this->data['password'] = array(
            'name' => 'password',
            'id' => 'password',
            'type' => 'password',
        );
        $this->data['password_confirm'] = array(
            'name' => 'password_confirm',
            'id' => 'password_confirm',
            'type' => 'password',
        );

        $this->content_construct('users/edit_user', $this->data);
    }

        /**
     * Delete the user
     *
     * @param int|string|null $id The user ID
     */
    public function delete_user($id = null)
    {
        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            // redirect them to the home page because they must be an administrator to view this
            return show_error('You must be an administrator to view this page.');
        }

        $id = (int)$id;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('confirm', $this->lang->line('delete_user_validation_confirm_label'), 'required');
        //$this->form_validation->set_rules('id', $this->lang->line('delete_user_validation_user_id_label'), 'required|alpha_numeric');

        $this->data['target'] = 'users/delete_user/'.$id;
        $this->data['title'] = $this->lang->line('delete_user_heading');

        if ($this->form_validation->run() === false) {
            // insert csrf check
            $this->data['csrf'] = $this->_get_csrf_nonce();
            $this->data['user'] = $this->ion_auth->user($id)->row();

            $this->_render_page('users/delete_user', $this->data);
        } else {
            // do we really want to delete user?
            if ($this->input->post('confirm') == 'yes') {
                // do we have a valid request?
                if ($this->_valid_csrf_nonce() === false || $id != $this->input->post('id')) {
                    //return show_error($this->lang->line('error_csrf'));
                }

                // do we have the right userlevel?
                if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                    $this->ion_auth->delete_user($id);
                }
            }

            // redirect them back to the auth page
            echo json_encode(array('type' => 'success', 'message' => 'User has been deleted.'));
            return;
            
        }
    }

/**
 * Create a new group
 */
public function create_group()
{
    $this->data['title'] = $this->lang->line('create_group_title');

    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        redirect('users', 'refresh');
    }

    // validate form input
    $this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'trim|required|alpha_dash');
    
    if (isset($_POST) && !empty($_POST) && !isset($_POST['params'])) {
      if ($this->form_validation->run() === true) {
          $new_group_id = $this->ion_auth->create_group($this->input->post('group_name'), $this->input->post('description'));
          if ($new_group_id) {
              // check to see if we are creating the group
              // redirect them back to the admin page
              echo json_encode(array('type' => 'success', 'message' => $this->ion_auth->messages()));
              return;
          } else {
                  echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors()));
                  return;
          }
        }
      }
      
        // display the create group form
        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

        $this->data['target'] = 'users/create_group';

        $this->data['group_name'] = array(
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name'),
        );
        $this->data['description'] = array(
            'name' => 'description',
            'id' => 'description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('description'),
        );

        $this->_render_page('users/create_group', $this->data);
    
}

    /**
     * Edit a group
     *
     * @param int|string $id
     */
    public function edit_group($id=1)
    {
        

        if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
            redirect('users', 'refresh');
        }

        // validate form input
        $this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash');

        if (isset($_POST) && !empty($_POST) && !isset($_POST['params'])) {
            if ($this->form_validation->run() === true) {
                $group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

                if ($group_update) {
                    echo json_encode(array('type' => 'success', 'message' => 'User successfully changed.'));
                    return;
                } else {
                    echo json_encode(array('type' => 'danger', 'message' => $this->ion_auth->errors(), 'errors' => $this->parse_errors()));
                    return;
                }
                redirect("users", 'refresh');
            } else{
                echo json_encode(array('type' => 'danger', 'message' => 'Please fix the errors in the form.', 'errors' => $this->parse_errors()));
                return;
            }
        }

        // set the flash data error message if there is one
        $this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));
        
        $id = $this->input->post('id');
        
        // bail if no group id given
        if (!$id || empty($id)) {
            redirect('users', 'refresh');
        }

        $this->data['title'] = $this->lang->line('edit_group_title');
        $this->data['target'] = 'users/edit_group/'.$id;

        $group = $this->ion_auth->group($id)->row();

        // pass the user to the view
        $this->data['group'] = $group;

        $readonly = $this->config->item('admin_group', 'ion_auth') === $group->name ? 'readonly' : '';

        $this->data['group_name'] = array(
            'name' => 'group_name',
            'id' => 'group_name',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_name', $group->name),
            $readonly => $readonly,
        );
        $this->data['group_description'] = array(
            'name' => 'group_description',
            'id' => 'group_description',
            'type' => 'text',
            'value' => $this->form_validation->set_value('group_description', $group->description),
        );

        $this->_render_page('users/edit_group', $this->data);
    }

    /**
* Delete the group
*
* @param int|string|null $id The group ID
*/
public function delete_group($id = null)
{
    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        // redirect them to the home page because they must be an administrator to view this
        return show_error('You must be an administrator to view this page.');
    }

    $id = (int)$id;

    $this->load->library('form_validation');
    $this->form_validation->set_rules('confirm', $this->lang->line('delete_group_validation_confirm_label'), 'required');
    //$this->form_validation->set_rules('id', $this->lang->line('delete_group_validation_group_id_label'), 'required|alpha_numeric');

    $this->data['target'] = 'users/delete_group/'.$id;
    $this->data['title'] = $this->lang->line('delete_group_heading');

    if ($this->form_validation->run() === false) {
        // insert csrf check
        $this->data['csrf'] = $this->_get_csrf_nonce();
        $this->data['group'] = $this->ion_auth->group($id)->row();

        $this->_render_page('users/delete_group', $this->data);
    } else {
        // do we really want to delete group?
        if ($this->input->post('confirm') == 'yes') {
            // do we have a valid request?
            if ($this->_valid_csrf_nonce() === false || $id != $this->input->post('id')) {
                //return show_error($this->lang->line('error_csrf'));
            }

            // do we have the right grouplevel?
            if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin()) {
                $this->ion_auth->delete_group($id);
            }
        }

        // redirect them back to the auth page
        echo json_encode(array('type' => 'success', 'message' => 'Group has been deleted.'));
        return;
        
    }
}

public function change_permissions($id)
{
    
    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        redirect('users', 'refresh');
    }

    // Update Permissions
    if (isset($_POST) && !isset($_POST['params'])) {
        
        $permissions_update = $this->permissions->editGroupPermissions($id, $_POST);

        if ($permissions_update) {
            echo json_encode(array('type' => 'success', 'message' => 'Permissions successfully changed.'));
            return;
        } else {
            echo json_encode(array('type' => 'danger', 'message' => "An error occured!"));
            return;
        }      
    }
    
    // Get Permissions
    $id = $this->input->post('id');
    
    // bail if no group id given
    if (!$id || empty($id)) {
        redirect('users', 'refresh');
    }

    $this->data['title'] = 'Permissions';
    $this->data['target'] = 'users/change_permissions/'.$id;

    $group = $this->ion_auth->group($id)->row();

    $permissions = $this->permissions->getGroupPermissions($id);
    $allpermissions = $this->permissions->getAllPermissions();
    // pass the user to the view
    $this->data['group'] = $group;
    $this->data['permissions'] = $permissions;
    $this->data['allpermissions'] = $allpermissions;
    
    $this->_render_page('users/permissions-new', $this->data);
}

public function change_user_permissions($id)
{   
    if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin()) {
        redirect('users', 'refresh');
    }

    // Update Permissions
    if (isset($_POST) && !isset($_POST['params'])) {
        $properties = $_POST['property'];
        $data = $_POST;
        unset($data['property']);
        $permissions_update = $this->permissions->editUserPermissions($id, $data, $properties);

        if ($permissions_update) {
            echo json_encode(array('type' => 'success', 'message' => 'Permissions successfully changed.'));
            return;
        } else {
            echo json_encode(array('type' => 'danger', 'message' => "An error occured!"));
            return;
        }      
    }
    
    // Get Permissions
    $id = $this->input->post('id');

    $groups = $this->ion_auth->get_users_groups($id)->result();
    $upermissions = $this->permissions->getUserPermissions($id);
    $groups_permissions = array();
    foreach ($groups as $group) $groups_permissions = array_merge($groups_permissions, $this->permissions->getGroupPermissions($group->id));
    $groups_permissions = array_unique($groups_permissions);

    $this->data['groups'] = $groups_permissions;
    
    // bail if no group id given
    if (!$id || empty($id)) {
        redirect('users', 'refresh');
    }

    $this->load->model('properties_model');
    $this->data['properties'] = $this->properties_model->getAllProperties(false, false);
    $this->data['ps'] = $this->permissions->getUserProperties($id);;
    $this->data['title'] = 'Permissions';
    $this->data['target'] = 'users/change_user_permissions/'.$id;

    //$group = $this->ion_auth->group($id)->row();

    //$permissions = $this->permissions->getGroupPermissions($id);
    $allpermissions = $this->permissions->getAllPermissions();
    // pass the user to the view
    $this->data['group'] = $group;
    $this->data['permissions'] = $groups_permissions;
    $this->data['upermissions'] = $upermissions;
    $this->data['allpermissions'] = $allpermissions;
    
    $this->_render_page('users/permissions_user', $this->data);
}

    /**
     * @return array A CSRF key-value pair
     */
    public function _get_csrf_nonce()
    {
        $this->load->helper('string');
        $key = random_string('alnum', 8);
        $value = random_string('alnum', 20);
        $this->session->set_flashdata('csrfkey', $key);
        $this->session->set_flashdata('csrfvalue', $value);

        return array($key => $value);
    }

    /**
     * @return bool Whether the posted CSRF token matches
     */
    public function _valid_csrf_nonce()
    {
        $csrfkey = $this->input->post($this->session->flashdata('csrfkey'));
        if ($csrfkey && $csrfkey === $this->session->flashdata('csrfvalue')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $view
     * @param array|null $data
     * @param bool $returnhtml
     *
     * @return mixed
     */
    public function _render_page($view, $data = null, $returnhtml = false)//I think this makes more sense
    {
        $this->viewdata = (empty($data)) ? $this->data : $data;

        $view_html = $this->load->view($view, $this->viewdata, $returnhtml);

        // This will return html on 3rd argument being true
        if ($returnhtml) {
            return $view_html;
        }
    }


}
