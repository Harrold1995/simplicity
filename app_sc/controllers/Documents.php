<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Documents extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('documents_model');
        $_SESSION['allFilters'] = $this->documents_model->getAllFilters();
        $_SESSION['allTypes'] = $this->documents_model->getAllTypes();
        $_SESSION['allFolders'] = $this->documents_model->getAllFolders();
    }

    /**
     * @param $param
     */
    function _remap($method, $params) {
        if(method_exists($this,$method))
            $this->$method();
        else
            $this->index($this->uri->segment_array());
    }

    function index($params = [])
    {
        $this->meta['title'] = "Documents";
        $this->meta['h2'] = "Documents";
        unset($params[1]);
        $i=0;
        foreach($params as $node){
            if($i>=3 && $i % 2 == 1)
                $values[] = $node;
            else
                $path[] = $node;
                $i++;
        }
        if(count($params) == 0) $this->data['path'] = "0";
            else $this->data['path'] = implode('/', $path);
        $this->data['values'] = implode('/', $values);
        $this->page_construct('documents/index', $this->data, $this->meta);
    }

    function unsorted($params)
    {
        $this->meta['title'] = "Documents";
        $this->meta['h2'] = "Documents";
        $this->data['path'] = '-1';
        $this->data['values'] = 'unsorted';
        $this->page_construct('documents/index', $this->data, $this->meta);
    }

    function paths($params)
    {
        $this->meta['title'] = "Documents";
        $this->meta['h2'] = "Documents";
        $this->data['path'] = '';
        $this->data['values'] = 'paths';
        $this->page_construct('documents/index', $this->data, $this->meta);
    }

    function favorites($params)
    {
        $this->meta['title'] = "Documents";
        $this->meta['h2'] = "Documents";
        $this->data['path'] = '';
        $this->data['values'] = 'favorites';
        $this->page_construct('documents/index', $this->data, $this->meta);
    }

    function getData(){
        $path = $this->input->post('path');
        $values = $this->input->post('values');
        $tvalues = $values;
        if($path!='') $path = explode('/', $path); else $path = array();
        if($values!='') $values = explode('/', $values); else $values = array();
        $generated = false;
        if($path[count($path)-1] == '-1') {
            $generated = true;
            array_pop($path);
            $filters = $this->getColumnsList($path, $values);
            $table = $this->getTable($path, $values);
            $crumbs = $this->getBreadcrumbs($path, $values);
            $columns = $this->documents_model->getTableHeader($path);
            echo json_encode(Array("filters" =>$filters, "folders" => $table, "crumbs"=>$crumbs, "columns"=>$columns));
            return;
        }
        if(count($path) == count($values)+3) {
            $generated = true;
            $available = $this->documents_model->generateFolders($path, $values);
        }elseif(count($path)==0) {
            $generated = true;
            $available = $this->documents_model->getSpecialFolders($path, $values);
        }else{
            $available = $this->documents_model->getAvailableFilters($path, $tvalues);
            $visible = $this->documents_model->getFiltersByPath($path);
        }
        $folders = $this->getFolders($path, $values, $available, $visible, $generated);
        $filters = $this->getFiltersList($path, $available, $visible, $generated);
        $crumbs = $this->getBreadcrumbs($path, $values);
        echo json_encode(Array("filters" =>$filters, "folders" => $folders, "crumbs"=>$crumbs));
    }

    function getFolders($path, $values, $available, $visible, $generated = false)
    {
        $this->data['folders'] = $visible;
        $this->data['generated'] = $generated;
        $this->data['path'] = implode('/', $path);
        $this->data['values'] = implode('/', $values);
        switch(count($path)) {
            case 0:
                $this->data['filters'] = $available;
                break;
            case 1:
                $this->data['filters'] = $_SESSION['allTypes'];
                break;

            default:
                $this->data['filters'] = $available;
                $allFolder = new stdClass();
                $allFolder->id = -1;
                $allFolder->name = "Show All";
                $this->data['allFolder'] = $allFolder;
        }
        return $this->load->view('documents/folders-template', $this->data, true);
    }

    function getTable($path, $values)
    {
        $this->data['path'] = implode('/', $path);
        $this->data['values'] = implode('/', $values);
        $this->data['columns'] = $this->documents_model->getTableColumns($path, $values);

        return $this->load->view('documents/table-template', $this->data, true);
    }

    function getUploadTemplate()
    {
        echo $this->load->view('documents/upload-template', null, true);
    }

    function getFileTemplate()
    {
        $this->data['types'] = $_SESSION['allTypes'];
        echo $this->load->view('documents/newfile-template', $this->data, true);
    }

    function getFileSelect()
    {
        $this->data['index'] = $this->input->post('index');
        $this->data['type'] = $this->input->post('type');
        $this->data['document_id'] = $this->input->post('document-id');
        $this->data['preselected'] = $this->input->post('preselected');
        $options = $this->documents_model->getFileSelectOptions($this->input->post('type'), $this->input->post('index'), $this->input->post('value'), $this->input->post('document-id'));
        if($options['status'] == 'select') {
            $this->data['data'] = $options['data'];
            echo json_encode(array("status" => "select", "select" => $this->load->view('documents/fileselect-template', $this->data, true)));
        } elseif($options['status'] == 'error') {
            echo json_encode(array("status" => "danger", "message" => "Not enough data, please select another ".$options["val"]."."));
        } elseif($options['status'] == 'success') {
            echo json_encode(array("status" => "success", "message" => "Document was successfully linked with selected ".$options["val"]."."));
        }
    }

    function getAddFavoritesModal()
    {
        echo $this->load->view('documents/addFavoritesModal', null, true);
    }

    function getBreadCrumbs($path, $values)
    {
        $this->data['path'] = implode('/', $path);
        $this->data['values'] = implode('/', $values);
        $this->data['crumbs'] = $this->documents_model->getBreadcrumbs($path, $values);

        return $this->load->view('documents/breadcrumbs-template', $this->data, true);
    }

    function getFiltersList($path, $available, $visible, $disabled = false)
    {
        if($disabled) return Array("label" => "disabled");
        $this->data['filters'] = $visible;
        switch(count($path)) {
            case 0:
                $label = "disabled";
                $list = $_SESSION['allTypes'];
                break;
            case 1:
                $label = "Types to Display: ";
                $list = $_SESSION['allTypes'];
                break;
            default:
                $label = "Selected filters: ";
                $list = $available;
                break;
        }
        $this->data['list'] = $list;
        return Array("label" => $label, "list" => $this->load->view('documents/list-template', $this->data, true));

    }

    function getColumnsList($path, $values)
    {
        $this->data['filters'] = Array();
        $this->data['list'] = Array();
        $filters = $_SESSION['allFilters'][$path[1]];
        foreach ($filters as $filter) {
            $filter->name = $filter->column_name;
            if($filter->status >= '1')$this->data['list'][] = $filter;
            if($filter->status == '2') $this->data['filters'][] = $filter->id;
        }
        return Array("label" => "disabled", "list" => $this->load->view('documents/list-template', $this->data, true));

    }

    function toggleFolderDisplay(){
        $path = $this->input->post('path');
        $filter = $this->input->post('filter');
        $this->documents_model->updateFolders($path, $filter);
    }

    function addFavoritesFolder(){
        $name = $this->input->post('name');
        $path = $this->input->post('path');
        $this->documents_model->addFavoritesFolder($name, $path);
        echo json_encode(array('type' => 'success', 'message' => 'Folder added to favorites.'));
    }

    function savePaths(){
        $name = $this->input->post('name');
        $this->documents_model->savePaths($name);
        echo json_encode(array('type' => 'success', 'message' => 'Path saved.'));
    }

    function getAjaxTable()
    {
        $path = $this->input->post('path');
        $values = $this->input->post('values');
        $tvalues = $values;
        if($path!='') $path = explode('/', $path); else $path = array();
        if($values!='') $values = explode('/', $values); else $values = array();
        echo json_encode(Array("data"=>$this->documents_model->getDocumentsList($path, $values)));
    }

    function uploadFiles(){
        $id = $this->documents_model->uploadFile();
        echo json_encode(Array("id"=>$id));
    }
    function deleteFiles(){
        $this->documents_model->deleteFiles($this->input->post('data'));
        echo json_encode(Array("type"=>"success", "message"=> "Files successfully deleted"));
    }
    function deleteDocs(){
        $this->documents_model->deleteFiles($this->input->post('data'));
        echo json_encode(Array("type"=>"success", "message"=> "Documents successfully deleted"));
    }
}
