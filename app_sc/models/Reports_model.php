<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reports_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->connections = Array(
            "leases" => Array(
                "units" => Array("units.id = leases.unit_id", "LEFT"),
                "properties" => Array("properties.id = units.property_id", "LEFT"),
                //"leases_profiles" => Array("leases_profiles.lease_id = leases.id", "RIGHT"),
                //"profiles" => Array("profiles.id = leases_profiles.profile_id", "LEFT"),
            ),
            "units" => Array(
                "properties" => Array("units.property_id = properties.id", "LEFT"),
            ),
            "transactions" => Array(
                "accounts" => Array("transactions.account_id = accounts.id", "LEFT"),
                "properties" => Array("transactions.property_id = properties.id", "LEFT"),
                "account_types" => Array("accounts.account_types_id = account_types.id", "LEFT"),
                "account_category" => Array("account_types.account_category_id = account_category.id", "LEFT"),
                "transaction_header" => Array("transactions.trans_id = transaction_header.id", "LEFT"),
                "transaction_type" => Array("transaction_header.transaction_type = transaction_type.id", "LEFT"),
                "bills" => Array("transaction_header.id = bills.trans_id", "LEFT"),
            ),
        );
        $this->mtt = Array("transaction" => "transaction_header");
    }

    public function getAllTypes()
    {
        $result = Array();
        $q = $this->db->get('report_types');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $type)
                $result[$type->id] = $type;
        }
        /*$q = $this->db->get('report_recordsets');
        if ($q->num_rows() > 0) {
            foreach ($q->result() as &$type) {
                $type->id = "0.".$type->id;
                $result["0." . $type->id] = $type;
            }
        }*/
        return $result;
    }

    public function getColumnsByTable($table, $type, $array = false)
    {       
        $result = Array();
        $q = $this->db->where('table_name', $table)->where('tc.type_id', $type)->join('report_columns c', 'c.id = tc.column_id', 'left')->select('c.*')->get('report_type_columns tc');
        if ($q->num_rows() > 0)
            if($array) {
                $result = $q->result_array();
                foreach ($result as &$row)
                    if($row['type'] == 'usd') {$row['format'] = 'usd';$row['type'] = 'num';}
            } else {
                $result = $q->result();
                foreach ($result as &$row)
                    if($row->type == 'usd') {$row->format = 'usd';$row->type = 'num';}
            }
        return $result;
    }

    public function getColumnsByType($type, $array = false)
    {
        $result = Array();
        $q = $this->db->where('c.type_id', $type)->select('c.*')->get('report_columns c');
        if ($q->num_rows() > 0)
            if($array) {
                $result = $q->result_array();
                foreach ($result as &$row)
                    if($row['type'] == 'usd') {$row['format'] = 'usd';$row['type'] = 'num';}
            } else {
                $result = $q->result();
                foreach ($result as &$row)
                    if($row->type == 'usd') {$row->format = 'usd';$row->type = 'num';}
            }
            console.log($result);
        return $result;
    }


    public function getAllColumns($type)
    {
        $type_id = $_SESSION['allTypes'][$type]->id;
        $columns = $this->getColumnsByType($type_id);
        $result = Array();
        if(false && strpos($type, '.') !== false) {
            $type = explode('.', $type);
            $q = $this->db->where('id', $type[1])->get('report_recordsets');
            if ($q->num_rows() > 0) $data = $q->row();
            $temp['table'] = $data->name;
            $temp['data'] = Array();
            $q = $this->db->query($data->query);
            $types = explode(',', $data->types);
            $types = array_map('trim', $types);
            if ($q->num_rows() > 0){
                $data = $q->result_array();
                foreach ($data[0] as $key => $value) {
                    $temp1 = new stdClass();
                    $temp1->id = ++$i;
                    $temp1->name = $key;
                    $temp1->type = $types[$i-1] ? $types[$i-1] : 'text';
                    if($temp1->type == 'usd') {$temp1->format = 'usd';$temp1->type = 'num';}
                    $temp['data'][] = $temp1;
                }
            }
            $result[] = $temp;
            return $result;
        }
        foreach ($columns as &$column) {
            if(!isset($result[$column->table_name])) $result[$column->table_name] = Array();
            if($column->type == 'usd') {$column->format = 'usd';$column->type = 'num';}
            $result[$column->table_name]['data'][] = $column;
            $result[$column->table_name]['table'] = $column->table_name;
        }

        return $result;
    }

    public function getAllColumnsArray($type)
    {
        $type_id = $_SESSION['allTypes'][$type]->id;
        $result = Array();
        if(false && strpos($type, '.') !== false) {
            $type = explode('.', $type);
            $q = $this->db->where('id', $type[1])->get('report_recordsets');
            if ($q->num_rows() > 0) $data = $q->row();
            $q = $this->db->query($data->query);
            $types = explode(',', $data->types);
            $types = array_map('trim', $types);
            if ($q->num_rows() > 0){
                $data = $q->result_array();
                foreach ($data[0] as $key => $value) {
                    $temp1 = Array();
                    $temp1['id'] = ++$i;
                    $temp1['name'] = $key;
                    $temp1['type'] = $types[$i-1] ? $types[$i-1] : 'text';
                    $temp[] = $temp1;
                }
            }
            $result = $temp;
            return $result;
        }
        $columns = $this->getColumnsByType($type_id, true);
        return $columns;
    }

    public function getReportData($type, $cf)
    {
        $mainTable = $_SESSION['allTypes'][$type]->table;
        $columns = $this->getAllColumns($type);
        $select = array();
        $mtype = $_SESSION['allTypes'][$type]->modal_type;
        if($mtype == 'transaction') $select[] = "transaction_header.transaction_type AS 'main-type'";
        if($this->mtt[$mtype]) $select[] = $this->mtt[$_SESSION['allTypes'][$type]->modal_type] . ".id" . " AS 'main-id'";
        if(strpos($type, '.') !== false) {
            $type = explode('.', $type);
            $q = $this->db->where('id', $type[1])->get('report_recordsets');
            if ($q->num_rows() > 0) $data = $q->row();
            $q = $this->db->query($data->query);
            $result = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result_array() as $row) {
                    $temp = Array();$i=1;
                    foreach ($row as $key => $field) {
                        $temp["c".$i++] = $field;
                    }
                    $result[] = $temp;
                }
            }
        } else {
            foreach ($columns as $column) {
                foreach ($column['data'] as $data) {
                    $select[] = $data->table_name . "." . $data->column_name . " AS c" . $data->id;
                    if($data->key_column != null) {
                        $select[] = $data->table_name . "." . $data->key_column . ' AS "c' . $data->id.'-k"';
                        if(in_array($data->table_name, array('accounts', 'units'))) {
                            $select[] = $data->table_name . ".parent_id " . " AS 'c" . $data->id."-p'";
                        }
                    }
                }
            }
            foreach ($cf as $c) {
                $select[] = "(".$c['query'] . ") AS c" . $c['id'];
            }

            if ($mainTable == 'units') {
                $select[] = "units.parent_id AS parent_id, units.id AS item_id, units.name AS item_name";
            }
            if ($mainTable == 'accounts') {
                $select[] = "accounts.parent_id AS parent_id, accounts.id AS item_id, accounts.name AS item_name";
            }
            //$this->db->select(implode(', ', $select));
            //print_r($mainTable);

            $q = $this->db->query(preg_replace("/select (.*?) from/i",  'select ' . implode(', ', $select) .' from', $mainTable, 1), 1);
            $result = array();
            if ($q->num_rows() > 0) {
                foreach ($q->result_array() as $row) {
                    $temp = Array();
                    foreach ($row as $key => $field) {
                        $temp[$key] = $field;
                    }
                    $result[] = $temp;
                }
            }
        }

        return $result;
    }

    public function getTableHeader($type, $cf)
    {
        $result = array();
        $result[] = array( "name"=>"cempty", "data"=>"cempty", "title"=>"", "visible"=>true, "class"=>"detail-control");
        $columns = $this->getAllColumns($type);
        if(strpos($type, '.') === false) {
            foreach ($columns as $column){
                foreach ($column['data'] as $data){
                    $result[] = array( "name"=>"c".$data->id, "data"=>"c".$data->id, "title"=>$data->name, "visible"=>true, "format"=>$data->format ? $data->format : $data->type, "type"=> $data->format == 'usd' ? "num-fmt" : "");
                }
            }
        } else{
            foreach ($columns as $column){
                foreach ($column['data'] as $data){
                    $result[] = array( "name"=>"c".++$i, "data"=>"c".$i, "title"=>$data->name, "visible"=>true);
                }
            }
        }
        foreach ($cf as $c){
            $result[] = array( "name"=>"c".$c['id'], "data"=>"c".$c['id'], "title"=>$c['name'], "visible"=>true, "format"=>$c['format'] ? $c['format'] : $c['type'], "type"=> $c['type'] == 'usd' ? "num-fmt" : "");
        }
        return $result;
    }

    public function h_getTableHeader($column, $range, $datef)
    {
        $result = array();
        if($range->bottom == "" || $range->top == "")
        $columns = $this->getAllColumns($type);
        if(strpos($type, '.') === false) {
            foreach ($columns as $column){
                foreach ($column['data'] as $data){
                    $result[] = array( "name"=>"c".$data->id, "data"=>"c".$data->id, "title"=>$data->name, "visible"=>true);
                }
            }
        } else{
            foreach ($columns as $column){
                foreach ($column['data'] as $data){
                    $result[] = array( "name"=>"c".++$i, "data"=>"c".$i, "title"=>$data->name, "visible"=>true);
                }
            }
        }
        foreach ($cf as $c){
            $result[] = array( "name"=>"c".$c['id'], "data"=>"c".$c['id'], "title"=>$c['name'], "visible"=>true);
        }
        return json_encode($result);
    }

    public function getReports($data = null)
    {
        if (!isset($data))
            $q = $this->db->get('reports');
        else
            $q = $this->db->get_where('reports', $data);
        if ($q->num_rows() > 0){
            return $q->result();
        }
        return array();
    }

    public function getReport($id)
    {
        $q = $this->db->get_where('reports', array('id' => $id), 1);
        if ($q->num_rows() > 0){
            return $q->row();
        }
        return array();
    }


    public function addReport($data)
    {
        $this->db->insert('reports', $data);
        return $this->db->insert_id();
    }

    public function addRecordset($data, $fields)
    {
        $this->db->insert('report_types', $data);
        $id = $this->db->insert_id();
        foreach($fields as &$field) {
            $field['type_id'] = $id;
            $this->db->insert('report_columns', $field);
        }
        return true;
    }

    public function saveReport($id, $data, $name)
    {
        $this->db->update('reports', Array('settings'=>$data, 'name' => $name), Array('id' => $id));
        return true;
    }

    public function editReport($data, $id)
    {
        $this->db->update('reports', $data, array('id' => $id));
        return true;
    }

    public function deleteReport($id)
    {
        $this->db->delete('reports', Array("id" => $id));
        return true;
    }

    public function CustReport( $id, $reportOptions)
    {
        $q = $this->db->query('SELECT `table` FROM report_types WHERE id ='.$id);
        $q2 ="";
        if ($q->num_rows() > 0){
            $row = $q->row(); 
            $q2 = $row->table;
        }
 
        $q3 = $this->db->query($q2.$reportOptions);
        if ($q3->num_rows() > 0){
            return $q3->result();
        }

       
        


    }



}
