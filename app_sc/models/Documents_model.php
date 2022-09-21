<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Documents_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->connections = Array(
            "leases" => Array(
                "units" => Array("units.id = leases.unit_id", "LEFT"),
                "properties" => Array("properties.id = units.property_id", "LEFT"),
                "leases_profiles" => Array("leases_profiles.lease_id = leases.id", "RIGHT"),
                "profiles" => Array("profiles.id = leases_profiles.profile_id", "LEFT"),
            ),
            "properties" => Array(
                "units" => Array("units.property_id = properties.id", "RIGHT"),
            ),
        );
        $this->selects = Array(
            "Leases" => Array(
                1 => Array("property", "properties", "properties.id", "properties.name", ""),
                2 => Array("unit", "units", "units.id", "units.name", "units.property_id"),
                3 => Array("lease", "leases", "leases.id", "CONCAT('Lease ', leases.start, ' - ',  leases.end)", "leases.unit_id"),
            ),
            "Properties" => Array(
                1 => Array("property", "properties", "properties.id", "properties.name", ""),
            ),
        );
    }

    public function getAllFilters()
    {
        $result = Array();
        $q = $this->db->get('document_types');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $type) {
                $this->db->select('dtf.column_status as status, df.*');
                $this->db->from('document_type_filters dtf');
                $this->db->join('document_filters df', 'df.id = dtf.filter_id', 'LEFT');
                $this->db->where('type_id', $type->id);
                $q1 = $this->db->get();
                if ($q1->num_rows() > 0) {
                    $filters = $q1->result();
                    $temp = array();
                    foreach ($filters as $filter) {
                        $temp[$filter->id] = $filter;
                    }
                    $result[$type->id] = $temp;
                }
            }
        }
        return $result;
    }

    public function getAllTypes()
    {
        $result = Array();
        $q = $this->db->get('document_types');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $type)
                $result[$type->id] = $type;
        }
        return $result;
    }

    public function getFolders($path)
    {
        $q = $this->db->get_where('document_folders', array('path' => $path));
        if ($q->num_rows() > 0) {
            return $q->result();
        }
        return array();
    }

    public function getAvailableFilters($path, $values = '')
    {
        $fid = $path[0];
        $type = $path[1];
        unset($path[0]);
        unset($path[1]);
        foreach ($_SESSION['allFilters'][$type] as &$filter) {
            if (!in_array($filter->id, $path)) {
                $filter->values = $values;
                $result[$filter->id] = $filter;
            }
        }
        return $result;
    }

    public function getFiltersByPath($path)
    {
        $path = implode('/', $path);
        $q = $this->db->get_where('document_folders', array('path' => $path));
        $result = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $folder) {
                $result[] = $folder->filter;
            }
        }
        return $result;
    }

    public function getAllFolders()
    {
        $q = $this->db->get_where('document_folders', array('path' => $path));
        $result = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $folder) {
                $result[] = ($folder->path != '' ? $folder->path . "/" : "") . $folder->filter;
            }
        }
        return $result;
    }

    public function generateFolders($path, $values)
    {
        $tvalues = implode('/', $values);
        $current = $path[count($path) - 1];
        $filter = $_SESSION['allFilters'][$path[1]][$current];
        $mainTable = $_SESSION['allTypes'][$path[1]]->table;
        $this->db->select("0 as id, " . $filter->field . " AS value, " . $filter->display_field . " AS name");
        $this->db->from($mainTable);
        foreach ($this->connections[$mainTable] as $table => $params) {
            $this->db->join($table, $params[0], $params[1]);
        }
        $i = 0;
        foreach ($values as $value) {
            $this->db->where($_SESSION['allFilters'][$path[1]][$path[$i + 2]]->field, $values[$i]);
            $i++;
        }
        $this->db->group_by($filter->field);
        $q = $this->db->get();
        //echo $this->db->last_query();
        $result = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as &$filter) {
                $filter->values = ($tvalues != "" ? $tvalues . "/" : "") . $filter->value;
                $result[] = $filter;
            }
        }
        return $result;
    }

    public function getSpecialFolders($path, $values)
    {
        switch($values[0]){
            case "favorites":
                $this->db->select()->from("document_favorites");
                break;
            case "paths":
                $this->db->select()->from("document_paths");
                break;
        }
        $q = $this->db->get();
        $result = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as &$filter) {
                $filter->values = "";
                if($values[0] == "favorites"){
                    $temp = explode("/", $filter->path);
                    $filter->path = "0/".$temp[1];
                    $filter->id = 0;
                    for($i=2;$i<count($temp);$i++)
                        if($i % 2 == 0) {
                            $filter->path .= "/".$temp[$i];
                        } else {
                            $filter->values .= $temp[$i]."/";
                        }
                    $filter->values = rtrim($filter->values, '/');
                    $filter->path = rtrim($filter->path, '/');
                }
                $result[] = $filter;
            }
        }
        return $result;
    }

    public function getTableColumns($path, $values, $status = -1)
    {
        $result = array("Title");
        foreach ($_SESSION['allFilters'][$path[1]] as $filter) {
            if($filter->status != '0') $result[] = $filter->column_name;
        }
        return $result;
    }

    public function getTableHeader($path)
    {
        $result = array();
        $result[] = array("data"=>"id", "title"=>"Id", "width"=>"20px");
        $result[] = array("data"=>"title", "title"=>"Title", "visible"=>true);
        foreach ($_SESSION['allFilters'][$path[1]] as $filter) {
            if($filter->status != '0') $result[] = array( "name"=>"c".$filter->id, "data"=>"c".$filter->id, "title"=>$filter->column_name, "visible"=>($filter->status == '2'));
        }
        $select = '<label for="tda" class="hidden">Choose Columns</label>
                    <span class="select">
                    <select id="d-columns-select">
                        <option selected disabled>Choose Columns</option>
                    </select>
                        <div id="d-columns-overlay" class="d-filters-overlay"></div>
                        <div id="d-columns-popup" class="d-filters-popup"></div>
                    </span>';
        $result[] = array("data"=>"actions", "title"=>$select, "class" => "dt-right");
        return json_encode($result);
    }

    public function getBreadcrumbs($path, $values)
    {
        $crumbs = Array();
        if (count($path) == 0)
            switch($values[0]){
                case 'unsorted':
                    $crumbs[] = Array("name" => "Unsorted", "path" => "-1", "values" => $values[0]);
                    break;
                case 'favorites':
                    $crumbs[] = Array("name" => "Favorites", "path" => "", "values" => $values[0]);
                    break;
                case 'paths':
                    $crumbs[] = Array("name" => "Saved Paths", "path" => "", "values" => $values[0]);
                    break;
                case 'recent':
                    break;
                default:
                    $crumbs[] = Array("name" => "Favorites", "path" => "", "values" => "");
                    break;
            }
        else {
            if ($path[0] == 0) $home = Array("name" => "Home", "path" => "0", "values" => ""); else {
                $crumbs[] = Array("name" => "Saved Paths", "path" => "", "values" => "paths");
                $data = $this->getPathsFolder($path[0]);
                $home = Array("name" => $data->name, "path" => $path[0], "values" => "");
            }
            $crumbs[] = $home;
        }
        if (count($path) >= 2) {
            $first = Array("name" => $_SESSION['allTypes'][$path[1]]->name, "path" => "0/" . $path[1], "values" => "");
            $crumbs[] = $first;
        }
        $i = 0;
        foreach ($path as $node) {
            $filter = $_SESSION['allFilters'][$path[1]][$node];
            $temp = Array("name" => $filter->name, "path" => implode('/', array_slice($path, 0, $i + 1)), "values" => implode('/', array_slice($values, 0, $i - 2)));
            $i++;
            if ($i == 1 || $i == 2) continue;
            $crumbs[] = $temp;
            if (isset($values[$i - 3])) {
                $name = $this->getValueName($path[1], $path[$i - 1], $values[$i - 3]);
                $temp = Array("name" => $name, "path" => implode('/', array_slice($path, 0, $i)), "values" => implode('/', array_slice($values, 0, $i)));
                $crumbs[] = $temp;
            }
        }

        return $crumbs;
    }

    public function getValueName($type, $filter, $value)
    {
        $temp = $_SESSION['allFilters'][$type][$filter];
        $this->db->select($temp->display_field . " AS name");
        $this->db->from($temp->table);
        $this->db->where($temp->field, $value);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->name;
        }
        return "";
    }

    public function getDocumentsList($path, $values)
    {
        //$path = array(0, 2);
        //$values = array("unsorted");
        if(isset($values[0]) && $values[0] == 'unsorted') {
            $this->db->select("DISTINCT(documents.id) as id, documents.name as title");
            $this->db->from('documents')->where('reference_id', 0);
            $q = $this->db->get();
            $result = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result_array() as $filter) {
                    $temp = Array();
                    foreach ($filter as $key => $field) {
                        $temp[$key] = $field;
                    }
                    $result[] = $temp;
                }
            }
            return $result;
        }
        $tvalues = implode('/', $values);
        $filters = $_SESSION['allFilters'][$path[1]];
        $select = array();
        foreach ($filters as $filter) {
            if(is_null($filter->column_field))
                $select[] = $filter->display_field . " AS c" . $filter->id;
            else
                $select[] = $filter->column_field . " AS c" . $filter->id;
        }
        $mainTable = $_SESSION['allTypes'][$path[1]]->table;
        $this->db->select("DISTINCT(documents.id) as id, documents.name as title, " . implode(', ', $select));
        $this->db->from('documents');
        $this->db->join($mainTable, 'documents.reference_id = ' . $mainTable . '.id', "LEFT");
        foreach ($this->connections[$mainTable] as $table => $params) {
            $this->db->join($table, $params[0], "LEFT");
        }
        $i = 1;
        $this->db->where('documents.type', $path[1]);
        //$this->db->group_by("documents.id");
        foreach ($values as $value) {
            $this->db->where($_SESSION['allFilters'][$path[1]][$path[$i + 1]]->field, $values[$i-1]);
            $i++;
        }

        $q = $this->db->get();
        //echo $this->db->last_query();
        $result = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result_array() as $filter) {
                $temp = Array();
                foreach ($filter as $key => $field) {
                    $temp[$key] = $field;
                }
                $temp['selects'] = $this->getDocumentSelectValues($temp['id']);
                $result[] = $temp;
            }
        }
        return $result;
    }

    public function getDocumentSelectValues($id){
        $temp = $this->db->select()->from('documents')->where('documents.id', $id)->get()->row();
        //print_r($temp);
        $type = $temp->type;
        $result = $type."|";

        $data = $this->selects[$_SESSION['allTypes'][$type]->name];
        $last = $data[count($data)];
        $temp1 = $last[2]." as f0, ";
        $this->db->from($last[1])->where($last[2], $temp->reference_id);
        $x = 1;
        for($i = count($data)-1; $i > 0; $i--){
            $temp1 .= $data[$i][2]." as f".$x++.", ";
            $this->db->join($data[$i][1], $data[$i][2]." = ".$data[$i + 1][4]);
        }
        $q = array_reverse($this->db->select($temp1)->get()->result_array()[0]);

        return $result.implode('|', $q);
    }

    public function updateFolders($path, $filter)
    {
        $q = $this->db->select()->from('document_folders')->where(Array('path' => $path, 'filter' => $filter))->get();
        if ($q->num_rows() == 0)
            $this->db->insert('document_folders', Array('path' => $path, 'filter' => $filter));
        else {
            $this->db->delete('document_folders', array('path' => $path, 'filter' => $filter));
            $this->db->like('path', $path . '/' . $filter, "after");
            $this->db->delete('document_folders');
        }
    }

    public function addFavoritesFolder($name, $path)
    {
        $this->db->insert('document_favorites', Array('name' => $name, 'path' => $path));
    }

    public function savePaths($name)
    {
        $this->db->insert('document_paths', Array('name' => $name));
        $fid = $this->db->insert_id();
        $q = $this->db->select()->from('document_folders')->like('path', '0', "after")->get();
        $folders = $q->result_array();
        foreach ($folders as &$folder) {
            $folder['path'] = preg_replace('#^0#', $fid, $folder['path']);
        }
        $this->db->insert_batch('document_folders', $folders);
    }

    public function getFavoritesFolder($id)
    {
        $q = $this->db->select()->from('document_favorites')->where("id", $id)->get();
        if ($q->num_rows() > 0)
            return $q->row();
        else
            return Array();
    }

    public function getPathsFolder($id)
    {
        $q = $this->db->select()->from('document_paths')->where("id", $id)->get();
        if ($q->num_rows() > 0)
            return $q->row();
        else
            return Array();
    }

    public function uploadFile(){
        $config['allowed_types'] = '*';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('file');
        $data['name'] = $this->upload->data('file_name');
        $this->db->insert('documents', $data);
        return $this->db->insert_id();
    }

    public function deleteFiles($data){
        $this->db->where_in('id', $data);
        $this->db->delete('documents');
    }

    public function getFileSelectOptions($type, $ind, $val, $docid) {
        $result = Array();
        if($type == '0'){
            $result = $_SESSION['allTypes'];
            array_unshift($result, (Object)Array("id" => 0, "name" => "Select type", "selected" => true));
        }else{
            $data = $this->selects[$_SESSION['allTypes'][$type]->name];
            if(isset($data[$ind])){
                $d = $data[$ind];
                $this->db->select($d[2]." as id, ".$d[3]." as name")->from($d[1]);
                if($d[4] != "")
                    $this->db->where($d[4], $val);
                $q = $this->db->get();
                //echo $this->db->last_query();
                $result = $q->result();
                array_unshift($result, (Object)Array("id" => 0, "name" => "Select ".$d[0], "selected" => true));
            } else {
                if($ind - 1 == count($data) && $docid != '0') {
                    $this->db->update('documents', Array("type" => $type, "reference_id" => $val), array('id' => $docid));
                    return Array("status" => "success", "val" => $data[count($data)][0]);
                }
            }
        }
        if(count($result) > 1)
            return Array("status" => "select", "data" => $result);
        else
            return Array("status" => "error", "val" => $data[$ind-1][0]);
    }

    public function getDocuments($id,$referenceId)
    {
        $this->db->select('d.name');
        $this->db->from('documents d');
        $this->db->join('document_types dt', ' d.type = dt.id');
        $this->db->where('d.reference_id', $id);
        $this->db->where('d.type', $referenceId);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
                foreach (($q->result()) as $row) {
                    $data[] = $row;
                }
                return $data;
            }
        return false;
    }

    public function getDocumentsProperty($pid)
    {
         $this->db->select('p.name as Property');
         $this->db->from('properties p');
        // $this->db->join('document_types dt', ' d.type = dt.id');
         $this->db->where('p.id', $pid);
        // $this->db->where('d.type', $referenceId);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
                $data = $q->row();
                return $data;
            }
        return false;
    }

    public function getDocumentsUnit($uid)
    {
        $this->db->select('p.name as Property, u.name as Unit');
        $this->db->from('units u');
        $this->db->join('properties p', 'u.property_id = p.id', 'left');
        $this->db->where('u.id', $uid);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data;
        }
        return null;
    }

    public function getDocumentsTenant($tid)
    {
        $this->db->select('CONCAT(pr.first_name," ",pr.last_name) as Profile, p.name as Property, u.name as Unit');
        $this->db->from('profiles pr');
        $this->db->join('leases_profiles lp', 'lp.profile_id = pr.id', 'left');
        $this->db->join('units u', 'lp.unit_id = u.id', 'left');
        $this->db->join('properties p', 'u.property_id = p.id', 'left');
        $this->db->where("pr.id=".$tid)->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            return $data;
        }
        return null;
    }

    public function getDocumentsLease($id)
    {
        $this->db->select('CONCAT(l.start," - ",l.end) as Lease, p.name as Property, u.name as Unit');
        $this->db->from('leases l');
        $this->db->join('units u', 'l.unit_id = u.id');
        $this->db->join('properties p', 'u.property_id = p.id');
        $this->db->where('l.id', $id);
        $this->db->limit(1);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $result = $q->row();
            $tenants = $this->getAllLeaseTenants($id);
            $result->tenants = $tenants;
            return $result;
        }
        return null;
    }

    public function getAllLeaseTenants($id)
    {
        $data = "";
        $step = 0;
        $this->db->select('CONCAT_WS(" ",p.first_name,p.last_name) as tenant');
        $this->db->from('profiles p');
        $this->db->join('leases_profiles lp', 'lp.profile_id = p.id');
        $this->db->where('lp.lease_id', $id);
        //$this->db->join('leases l', 'lp.lease_id = l.id');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if($step == 0){
                    $data = $row->tenant;
                    $step++;
                }else{
                    $data = $data . ", ". $row->tenant;
                }
            }
            return $data;
        }
        return null;
    }

    public function uploadAttachment($referenceId, $typeId){

        $config['allowed_types'] = 'pdf|docs|jpg|jpeg|png|csv|xls';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $this->upload->do_upload('attach_document');
        $data['attach_document'] = $this->upload->data('file_name');
        $this->db->insert('documents', Array("name" => $data['attach_document'], "reference_id" => $referenceId, "type" => $typeId));
    }

    function attach_document($id){
        $id_type =explode("--", $id);
        $id=$id_type[0];
        $type=$id_type[1];
        $data = $this->input->post();
        $config['allowed_types'] = 'pdf|docs|jpg|jpeg|png|csv|xls';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $uploaded = $this->upload->do_upload('attach_document');
        $data['attach_document'] = $this->upload->data('file_name');
        $typeNumber = $this->getTypeNumber($type);
        if($typeNumber > 0 && $id > 0 && $data['attach_document'] != "" && $uploaded){
            $this->db->insert('documents', Array("name" => $data['attach_document'], "reference_id" => $id, "type" => $typeNumber));
            return array('type' => 'success', 'message' => 'Document succesfully submitted!');
            //return 'Document succesfully submitted!'; 
        }else{
            return array('type' => 'danger', 'message' => 'Document could not be submitted!');
            //return 'Document could not be submitted!'; 
        }
    }

    function get_documents($id){
        $id_type =explode("--", $id);
        $id=$id_type[0];
        $type=$id_type[1];
        if($type == 'property'){$type = 'properties';}
        $typeNumber = $this->getTypeNumber($type);
        $documents = $this->getDocuments($id,$typeNumber);
        if($documents){
            $a =  json_encode($documents);
        }else{
        $a = 'No documents available.';
        }
        //$a = 'I am a tooltipster!'. $id. '-'. $type;
        //$a =  json_encode($documents);
        return $a;
    }

    function getTypeNumber($type){
        $typeNumber = $this->getTypeId($type);
        return $typeNumber;
    }

    public function getTypeId($type)
    {
        $q = $this->db->get('document_types');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as &$row) {
                if($row->table == $type){
                    return $row->id;
                }
            }
            return null;
        }
        return null;
    }

    public function attach_rec_document($id){
        $data = $this->input->post();
        $config['allowed_types'] = 'pdf|docs';
        $config['upload_path'] = 'uploads/documents';
        $this->load->library('upload', $config);
        $uploaded = $this->upload->do_upload('attach_document');
        $data['attach_document'] = $this->upload->data('file_name');
        if($data['attach_document'] != "" && $uploaded){
            $this->db->update('reconciliations',  array('statement_attachment' => $data['attach_document']), array('id' => $id));
            return array('type' => 'success', 'message' => 'Document succesfully submitted!'); 
        }else{
            return array('type' => 'danger', 'message' => 'Document could not be submitted!'); 
        }
    }
    
}
