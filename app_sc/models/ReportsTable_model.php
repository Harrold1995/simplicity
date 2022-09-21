<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ReportsTable_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->defparams_from = Array("{params.today}");
        $this->defparams_to = Array(date('Y-m-d', time()));
        $this->formatters = Array("usd"=>"UsdFormatter", "num"=>"NumFormatter", "date"=>"DateFormatter", "link"=>"LinkFormatter");
        $this->type_to_table = Array("transaction" => "transaction_header", "unit" => "units", "property" => "properties", "lease" => "leases", "tenant" => "profiles", "vendors" => "profiles");
    }

    public function getReport($type, $settings) {
        $this->columnDefs = $this->getColumnsByType($type);
        $this->sortdata = Array();
        foreach($settings->grouping as $gr){
            if($gr->type == 'sorting') {
                $this->sortdata[] = (Object)Array('column' => $gr->column, 'order' => $gr->order);
            }
        }
        foreach($settings->cf as $cf){
            $this->columnDefs[$cf->id] = (Object)Array("name" => $cf->name, "format" => $cf->type, "type" => ($cf->type == 'usd' ? 'num' : $cf->type));
            if($cf->af == '1') {
                if (!$this->afs)
                    $this->afs = Array();
                $this->afs[] = 'c'.$cf->id;
            }
        }
        $horizontal = $this->isHorizontal($settings->grouping);
        if($horizontal){
            $this->dateColumns = true;
            $this->cindex = Array();
            $this->h = $settings->grouping[$horizontal];
        }
        $this->gtotal = false;
        $this->columns = $this->getReportColumns($type, $settings, $horizontal);
        //print_r($settings);
        $this->data = $this->getReportData($type, $settings, $horizontal);
        if($horizontal) $this->addTotals($this->h);
        $this->applyMaxWidth();
        return json_encode(Array("data"=>$this->data, "columns"=>$this->columns));
    }

    public function getReportData($type, $settings = null, $h = false)
    {
        $cf = $settings->cf;
        $result = $this->getRawReportData($settings, $cf, $this->parseFilters($settings->filters, $settings->ufilters, $type), $this->parseSorting($settings->grouping), $this->parseParams($settings->params));
        $result = $this->applyGrouping($result, $settings, $h);
        return $result;
    }

    public function getReportColumns($type, $settings = null, $h = false)
    {
        $cf = $settings->cf;
        $params = $this->parseParams($settings->params);
        $params['from'][] = "'";
        if($h = $this->isHorizontal($settings->grouping)) return $this->getHorizontalColumns($settings, $h);
        $result = array();
        $temp = array("id" => "cempty", "field" => "cempty", "name" => "", "minWidth" => 200, "formatter" => "NameFormatter", "resizable" => true);
        $result[] = (Object)$temp;
        $columns = $this->getColumnsByType($type);
        foreach ($columns as $data) {
            $temp = array("id" => "c" . $data->id, "field" => "c" . $data->id, "name" => $data->name, "minWidth" => 0, "width" => 120, "resizable" => true, "source" => $data->source, "key_column" => $data->key_column, "link_type" => $data->link_type, "formatter" => $this->formatters[$columns[$data->id]->format]);
            $temp['name'] = str_replace($params['from'], $params['to'], $temp['name']);
            $result[] = (Object)$temp;
        }
        foreach ($cf as $data) {
            $temp = array("id" => "c" . $data->id, "field" => "c" . $data->id, "name" => $data->name, "minWidth" => 0, "width" => 120, "resizable" => true, "formatter" => $this->formatters[$data->type]);
            $temp['name'] = str_replace($params['from'], $params['to'], $temp['name']);
            $result[] = (Object)$temp;
        }
        return $result;
    }

    public function getRawReportData($settings, $cf, $where, $order, $params)
    {
        $type = ($settings->newtype) ? $settings->newtype : $settings->type;;
        $sql = $_SESSION['allTypes'][$type]->table;
        $name = $_SESSION['allTypes'][$type]->name;
        $type = $settings->type;
        $columns = $this->getColumnsByType($type);
        $select = array();
        $select[] = $this->type_to_table[$_SESSION['allTypes'][$type]->modal_type] . ".id" . " AS 'main-id'";
        if($_SESSION['allTypes'][$type]->modal_type == 'transaction')
            $select[] = "transaction_header.transaction_type AS 'main-type'";

        foreach ($columns as $data) {

                $select[] = ($data->table_name   && strpos($data->column_name, '(') == false ? $data->table_name . "." : "") . $data->column_name . " AS c" . $data->id;
           
            if ($data->key_column != null) {
                $select[] = $data->table_name . "." . $data->key_column . ' AS "c' . $data->id . '-k"';
                if (in_array($data->table_name, array('accounts', 'units'))) {
                    $select[] = $data->table_name . ".parent_id " . " AS 'c" . $data->id . "-p'";
                }
                if($data->table_name == 'accounts')
                    $select[] = $data->table_name . ".report_id " . " AS 'report_id'";
            }
        }
        foreach ($cf as $c) {
            $select[] = "(" . $c->query . ") AS c" . $c->id;
        }
        $sql = preg_replace("/{filters}/i", ' ' . $where . ' ', $sql);
        $sql = preg_replace("/{s}(.*?){\/s}/i", implode(', ', $select), $sql);
        $sql = 'select * from (' . $sql . ') as T where ' . $where . (!empty($order) ? ' order by ' . $order : '');
        //echo $sql;
        $sql = str_replace($params['from'], $params['to'], $sql);
        $sql = $this->applySystemParameters($sql);
        $q = $this->db->query($sql, 1);
        if($this->cbname)
            $this->db->query('DROP TABLE IF EXISTS '.$this->cbname);
        //echo $this->db->last_query();
        $result = array();
        $id = 1;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as &$row) {
                $row->id = $id++;
                $result[] = $row;
            }
        }
        //print_r($result);
        return $result;
    }

    public function applySystemParameters($sql) {
        if(strpos($sql, '{params.cash_basis}') !== false) {
            $name = $this->generateCashBasisTable();
            $sql = str_replace('{params.cash_basis}', $name, $sql);
        }
        //echo $sql;
        $sql = str_replace($this->defparams_from, $this->defparams_to, $sql);
        return $sql;
    }

    public function isHorizontal($grs) {
        $item = false;
        foreach($grs as $key => $g) {
            if($g->hg == '1') {
                $item = $key;
                break;
            }
        }
        return $item;
    }

    public function applyMaxWidth() {
        $width = 200;
        if(!property_exists($this->data[0], 'cempty')) return;
        foreach($this->data as $d) {
            if(!property_exists($d, 'cempty')) continue;
            $temp = strlen($d->cempty)*6 + $d->indent*15;
            if($temp > $width) $width = $temp;
        }
        $this->columns[0]->minWidth = $width;
    }

    public function getHorizontalColumns($settings, $hind) {
        $result = array();
        $grs = $settings->grouping;
        $columns = $this->columnDefs;
        $temp = array("id" => "cempty", "field" => "cempty", "name" => "", "minWidth" => 200, "formatter" => "NameFormatter", "resizable" => true);
        $result[] = (Object)$temp;
        $hg = $grs[$hind];
        switch($hg->dtype){
            case 'date':
                $range = $this->getDateRange($hg->column, $settings);
                //print_r($hg->datef);
                switch($hg->datef){
                    case '0':
                        $format = "m/d/Y";
                        $interval = new DateInterval('P1D');
                        $mod = "";
                        break;
                    case '1':
                        $format = "M Y";
                        $interval = new DateInterval('P1M');
                        $mod = 'last day of this month';
                        break;
                    case '2':
                        $format = "Y";
                        $interval = new DateInterval('P1Y');
                        $mod = 'last day of this year';
                        break;
                }
                $period   = new DatePeriod($range['start'], $interval, $range['end']);
                foreach ($period as $i=>$dt) {
                    $temp = array("id" => "h".$i, "field" => "h".$i, "name" => $dt->format($format), "format" => $format, "horizontal" => true, "formatter" => $this->formatters[$columns[$hg->htotal]->format], "minWidth" => 120,
                        "h_column" => $hg->column, "h_type" => $hg->dtype, "h_cond" => '3', "h_name1" => $dt->format('m/d/Y'), "h_name2" => $dt->modify($mod)->format('m/d/Y'));
                    $this->cindex[$dt->format($format)] = $i+1;
                    $result[] = (Object)$temp;
                }
                break;
            default:
                $this->dateColumns = false;

        }
        return $result;
    }

    public function getDateRange($field, $settings) {
        $allfilters = array_merge($settings->filters, $settings->ufilters);
        $range = Array("start" => new DateTime('first day of january this year'), "end" => new DateTime('today'));
        $found = false;
        foreach($allfilters as $f) {
            if($f->column == $field && $f->condition == '3' && $start = new DateTime($f->fields[0]->name1) && $end = new DateTime($f->fields[0]->name2)) {
                $range['start'] = new DateTime($f->fields[0]->name1);
                $range['end'] = new DateTime($f->fields[0]->name2);
                $found = true;
                break;
            }
        }
        if(!$found) {
            $f = (Object)Array("name1"=>$range['start']->format('m/d/Y'), "name2"=>$range['end']->format('m/d/Y'));
            $filter = (Object)Array("column"=>$field, "condition"=>'3', "dtype"=>"date", "fields" => [$f]);
            //print_r($filter);
            $settings->filters[] = $filter;
        }
        return $range;
    }

    public function getColumnsByType($type, $array = false)
    {
        $return = Array();
        $q = $this->db->where('c.type_id', $type)->select('c.*')->get('report_columns c');
        if ($q->num_rows() > 0)
            if ($array) {
                $result = $q->result_array();
                foreach ($result as &$row) {
                    if ($row['type'] == 'usd') {
                        $row['format'] = 'usd';
                        $row['type'] = 'num';
                    } else {
                        $row['format'] = $row['type'];
                    }
                    $return[$row->id] = $row;
                }
            } else {
                $result = $q->result();
                foreach ($result as &$row) {
                    if ($row->type == 'usd') {
                        $row->format = 'usd';
                        $row->type = 'num';
                    } else {
                        $row->format = $row->type;
                    }
                    $return[$row->id] = $row;
                }
            }
        return $return;
    }

    public function getAllColumnsList($type)
    {
        $type_id = $_SESSION['allTypes'][$type]->id;
        $columns = $this->getColumnsByType($type_id);
        $result = Array();
        foreach ($columns as $column) {
            if (!isset($result[$column->table_name])) $result[$column->table_name] = Array();
            $result[$column->table_name]['data'][] = $column;
            $result[$column->table_name]['table'] = $column->table_name;
        }

        return $result;
    }

    public function parseFilters($fs, $ufs, $type)
    {
        $columns = $this->getColumnsByType($type);
        $fs = array_merge((array)$fs, (array)$ufs);
        //print_r($fs);
        $where = Array();
        foreach ($fs as $f) {
            switch ($f->condition) {
                case 0:
                    //if ($columns[$f->column]->key_column) $f->column .= '-k';
                    $str = '(';
                    $cnum = preg_replace('/[^0-9]/', '', $f->column);
                    if($this->columnDefs[$cnum]->source) $f->column .= '-k';
                    foreach ($f->fields as $key => $field) {
                        $field->value = trim($field->value);
                        if($field->value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if($this->columnDefs[$cnum]->table_name == 'accounts' || $this->columnDefs[$cnum]->table_name == 'units')
                            $str .= $this->nestedFilter($cnum, $f->column, $field->value);
                        else
                            $str .= "`c{$f->column}` = '" . strtolower($field->value) . "'";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 1:
                    //if ($columns[$f->column]->key_column) $f->column .= '-k';
                    $str = '(';
                    $cnum = preg_replace('/[^0-9]/', '', $f->column);
                    if($this->columnDefs[$cnum]->source) $f->column .= '-k';
                    foreach ($f->fields as $key => $field) {
                        $field->value = trim($field->value);
                        if($field->value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if($this->columnDefs[$cnum]->table_name == 'accounts' || $this->columnDefs[$cnum]->table_name == 'units')
                            $str .= $this->nestedFilter($cnum, $f->column, $field->value, false);
                        else
                            $str .= "`c{$f->column}` != '" . strtolower($field->value) . "'";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 2:
                    $str = '(';
                    foreach ($f->fields as $key => $field) {
                        $field->value = trim($field->value);
                        if($field->value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        $str .= "c{$f->column} LIKE '%" . strtolower($field->value) . "%'";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 3:
                    $str = '(';
                    foreach ($f->fields as $key => $field) {
                        $field->name1 = trim($field->name1);
                        $field->name2 = trim($field->name2);
                        if($field->name1 == '' || $field->name2 == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if ($f->dtype == 'date' && (strpos($field->name1, '{params.') === false || strpos($field->name2, '{params.') === false)) {
                            $str .= "c{$f->column} BETWEEN '" . (strpos($field->name1, '{params.') === false ? date('Y-m-d', strtotime($field->name1)) : $field->name1) . "' AND '" . (strpos($field->name2, '{params.') === false ? date('Y-m-d', strtotime($field->name2)) : $field->name2) . "'";
                        }else
                            $str .= "c{$f->column} BETWEEN {$field->name1} AND {$field->name2}";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 4:
                    $str = '(';
                    foreach ($f->fields as $key => $field) {
                        $field->value = trim($field->value);
                        if($field->value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if ($f->dtype == 'date' && strpos($field->value, '{params.') === false)
                            $str .= "c{$f->column} > '" . date('Y-m-d', strtotime($field->value)) . "'";
                        else
                            $str .= "c{$f->column} > '{$field->value}'";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 5:
                    $str = '(';
                    foreach ($f->fields as $key => $field) {
                        $field->value = trim($field->value);
                        if($field->value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if ($f->dtype == 'date' && strpos($field->value, '{params.') === false)
                            $str .= "c{$f->column} < '" . date('Y-m-d', strtotime($field->value)) . "'";
                        else
                            $str .= "c{$f->column} < '{$field->value}'";
                    }
                    if($str != '(') $where[] = $str.')';
                    break;
                case 6:
                    //if ($columns[$f->column]->key_column) $f->column .= '-k';
                    $str = '(';
                    $cnum = preg_replace('/[^0-9]/', '', $f->column);
                    if($this->columnDefs[$cnum]->source) $f->column .= '-k';
                    $values = explode(',',$f->fields[0]->value);
                    foreach ($values as $key => $value) {
                        $value = trim($value);
                        if($value == '') continue;
                        if ($key > 0 && $str != '(') $str .= ' OR ';
                        if($this->columnDefs[$cnum]->table_name == 'accounts' || $this->columnDefs[$cnum]->table_name == 'units')
                            $str .= $this->nestedFilter($cnum, $f->column, $value);
                        else
                            $str .= "`c{$f->column}` = '" . strtolower($value) . "'";
                    }
                    if($str != '(') {
                        $where[] = $str.')';
                    } else {
                        if(PFLAG==00 && $this->columnDefs[$cnum]->table_name == 'properties') {$where[] ="`c{$f->column}` in".PROPERTIES;}
                    };
                    break;
            }
        }
        return empty($where) ? 'true' : implode(" AND ", $where);
    }

    public function nestedFilter($column_id, $column, $value, $equal = true) {
        $in = Array();
        $parents = Array($value);
        $in = Array($value);
        $q = $this->db->select('id')->from($this->columnDefs[$column_id]->table_name)->where_in('parent_id', $parents)->get();
        while ($q->num_rows() > 0) {
            $parents = array();
            foreach($q->result() as $r) {
                $parents[] = $r->id;
                $in[] = $r->id;

            }
            $q = $this->db->select('id')->from($this->columnDefs[$column_id]->table_name)->where_in('parent_id', $parents)->get();
        }
        if($equal)
            return '`c'.$column.'` in ('.implode(', ', $in).')';
        else
            return '`c'.$column.'` not in ('.implode(', ', $in).')';
    }

    public function parseSorting($grs)
    {
        $order = Array();
        foreach ($grs as $gr) {
            if ($gr->type == 'sorting')
                $order[] = 'c'.$gr->column.' '.$gr->order;
        }

        return implode(", ", $order);
    }

    public function parseParams($params)
    {
        $result = Array();
        foreach ($params as $param) {
            $result['from'][] = '{params.'.$param->key.'}';
            switch($param->type) {
                case 'date':
                    if(strtotime($param->value))
                        $date = date('Y-m-d', strtotime($param->value));
                    else
                        $date = $param->value;
                    $result['to'][] = "'".$date."'";
                    break;
                case 'num':
                    $result['to'][] = $param->value;
                    break;
                default:
                    $result['to'][] = '"'.$param->value.'"';
            }
        }
        return $result;
    }

    public function applyGrouping($data, $settings, $h)
    {
        $grs = $settings->grouping;
        $top = $settings->top;
        $grArr = array();
        $this->result = array();

        $this->gtotal = !$h && $top->gtotal;
        $this->footer = (Object)Array();
        foreach ($grs as $i=>$gr) {
            if ($gr->type == 'grouping' && (!$h || $i<=$h))
                $grArr[] = Array('column' => 'c' . $gr->column, 'df' => $gr->datef ? $gr->datef : '0', 'header' => add_c($gr->header), 'footer' => add_c($gr->footer), 'hide' => add_c($gr->hide0), 'exp'=>$gr->exp, 'nexp'=>$gr->nexp, 'col'=>$gr->col);
        }
        if($settings->topgroup) {
            foreach ($grArr as $i => $gr) {
                if ($gr['column'] == 'c' . $settings->topgroup) break; else unset($grArr[$i]);
            }
            $grArr = array_values($grArr);
        }
        $function = 'array_group_by';
        if($h) $function = 'array_group_by_hg';
        if (count($grArr)) {
            if (property_exists($data[0], $grArr[0]['column'].'-p')) {
                $data = array_nest($this->populateNestedData($this->$function($data, $grArr, true), $grArr[0]['column'], $grArr[0]), $grArr[0]['column'] . '-p', $grArr[0]['column'] . '-k', 0, $grArr[0]['nexp']);
            }else
                $data = $this->$function($data, $grArr, true);
            if($settings->ctype !='0') {
                $data = $this->getCustomData($data, $settings);
            }
            if($this->h->vt || ($this->h && $top->gtotal)) {
                $footer = (Object)Array('cempty' => 'Total', 'footer' => true);
                foreach($data as $obj) {
                    foreach($obj as $key=>$val) {
                        if($key[0] == 'h')$footer->{$key} += $val;
                    }
                }
                $data[] = $footer;
            }
            if(!$h && $top->gtotal) {
                $newgt_custom =  preg_replace("/'[\s\S]+?'/", '', $top->gt_custom);
                $newgt_custom = trim( $newgt_custom, "'");
                if($top->gt_custom && !preg_match("/[a-z]/i", $newgt_custom)){
                   

                    foreach($this->footer as $key=>$val) {
                        $temp = Array();
                        $i = 0;
                        foreach($data as $obj) {
                            $i++;
                            if($obj->{$key})
                                $temp[$i] = $obj->{$key};
                            elseif($obj->footer && end($obj->data)->footer) {
                                $temp[$i] = end($obj->data)->{$key};
                            }
                        }
                        $str = preg_replace_callback('({([0-9]+)})',
                            function ($m) use ($temp) {
                                return '('.($temp[(int)$m[1]] ? $temp[(int)$m[1]] : 0).')';
                            },
                            $newgt_custom
                        );
                        $this->footer->{$key} = eval('return '.$str.';');
                    }
                }
                if (preg_match("/'([^']+)'/", $top->gt_custom, $random)) {
                    $customName =  $random[1];  
                } else {
                    $customName ="Total";
                }
                $this->footer->cempty = $customName;
                $this->footer->formula = $newgt_custom;
                $this->footer->footer = true;
                /*$temp = Array();
                foreach($data as $obj) {
                    foreach($obj as $key=>$val) {
                        if(is_numeric($val) && $this->columnDefs[substr($key, 1)])
                            $footer->{$key} += $val;
                    }
                }*/

                $data[] = $this->footer;
            }
            if($h && $top->gtotal) {
                $newgt_custom =  preg_replace("/'[\s\S]+?'/", '', $top->gt_custom);
                $newgt_custom = trim( $newgt_custom, "'");
                if($top->gt_custom && !preg_match("/[a-z]/i", $newgt_custom)){
                    $this->footer = end($data);
                    foreach($this->footer as $key=>$val) {
                        $temp = Array();
                        $i = 0;
                        foreach($data as $obj) {
                            $i++;
                            if($obj->{$key})
                                $temp[$i] = $obj->{$key};
                            elseif($obj->footer && end($obj->data)->footer) {
                                $temp[$i] = end($obj->data)->{$key};
                            }
                        }
                        $str = preg_replace_callback('({([0-9]+)})',
                            function ($m) use ($temp) {
                                return $temp[(int)$m[1]] ? "(". $temp[(int)$m[1]].")" : 0;
                            },
                            $newgt_custom
                            
                        );
                        $this->footer->{$key} = eval('return '.$str.';');
                        
                    }
                }
                if (preg_match("/'([^']+)'/", $top->gt_custom, $random)) {
                    $customName =  $random[1];  
                } else {
                    $customName ="Total";
                }
                $this->footer->cempty = $customName;
                $this->footer->footer = true;
            
            }

        } else {
            if(!$h && $top->gtotal) {
                $footer = (Object)Array( 'footer' => true);
                foreach($data as $obj) {
                    foreach($obj as $key=>$val) {
                        if($this->columnDefs[substr($key,1)]->type == 'num') $footer->{$key} += $val;
                    }
                }
                $data[] = $footer;
            }
        }

        $this->recursive($data, null, 0, 0);
        //print_r(array_column($this->result, 'id'));
        return $this->result;

    }

    function recursive($data, $parent, $id, $indent)
    {
        $range = false;
        foreach ($data as &$item) {
            //if($item->cempty == null) continue;
            if($this->h && $this->h->he && !$item->htotal) {
                continue;
            }
            $item->indent = $indent;
            $item->parent = $parent;
            //echo (int)$id != (int)count($this->result) ? $id.' ' : '';
            $this->result[] = $item;
            if (isset($item->data) && count($item->data) > 0) {
                $item->id = $id;
                $id = $this->recursive($item->data, $id, $id + 1, $indent + 1);
                //echo $id." ";
                //$id = count($this->result);
            } else {
                //print_r($item);
                $item->id = $id;
                $id++;
            }
            unset($item->data);
        }
        return $id;
    }

    function array_group_by(array $array, array $keys, $first = false)
    {
        $key = $keys[0];
        $_key = $key['column'];
        $pkey = $_key . '-p';
        $kkey = $_key . '-k';
        $nkey = $_key;
        $df = $key['df'];
        $data = $key;
        if (count($array) > 0 && property_exists($array[0], $kkey)) $_key = $kkey;

        // Load the new array, splitting by the target key
        $grouped = Array();
        foreach ($array as $value) {
            $key = null;

            if (is_object($value) && property_exists($value, $_key)) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            }

            if ($key === null || $key == '') {
                $key = 'no';
            }else {
                $key = d_format($key, $df);
            }
            if (!$grouped[$key]) {
                $name = d_format($value->{$nkey}, $df);
                if($key == 'no') $name = 'No ' . $this->columnDefs[substr($data['column'], 1)]->name;
                $grouped[$key] = (Object)Array('cempty' => $name, 'grouped-by' => $data['column'], 'grouped-key' => $key, 'data' => Array());
                if($value->{$kkey}) {
                    foreach ($this->sortdata as $s) {
                        $grouped[$key]->{'s'.$s->column} = $value->{'c'.$s->column};
                    }
                    //echo json_encode($grouped[$key]);
                }
                if($value->report_id) $grouped[$key]->report_id = $value->report_id;
                if(!$data['exp']) {
                    $grouped[$key]->_collapsed = true;
                    $grouped[$key]->expanded_def = true;
                }
                if($data['col']) {
                    $grouped[$key]->collapsed_def = true;
                }
                if (count($data['footer']) > 0) $grouped[$key]->footer = (Object)Array('cempty'=>$name.' Total');
                if (property_exists($value, $pkey)) $grouped[$key]->{$pkey} = $value->{$pkey};
                if (property_exists($value, $kkey)) $grouped[$key]->{$kkey} = $value->{$kkey};
            }
            foreach($data['header'] as $field) {
                $grouped[$key]->{$field} += $value->{$field};
                //echo $value->c130.",";
            }
            foreach($data['footer'] as $field) {
                $grouped[$key]->footer->{$field} += $value->{$field};
                //echo $value->c130.",";
            }
            //echo"|||";
            if($this->gtotal && $first)
                foreach($this->columnDefs as $k=>$column) {
                    if($column->type == 'num') $this->footer->{'c'.$k} += $value->{'c'.$k};
                }
            $grouped[$key]->data[] = $value;
        }

        foreach ($data['hide'] as $hide) {
            foreach ($grouped as $key => $value) {
                if (isset($grouped[$key]->{$hide}) && round($grouped[$key]->{$hide}, 4) == 0) {
                    unset($grouped[$key]);
                }
            }
        }
        if (count($keys) > 1) {
            array_shift($keys);
            foreach ($grouped as $key => $value) {
                if (property_exists($grouped[$key]->data[0], $keys[0]['column'] . '-p')) {
                    $temp = $this->array_group_by($grouped[$key]->data, $keys);
                    $grouped[$key]->data = array_nest($this->populateNestedData($temp, $keys[0]['column'], $keys[0]), $keys[0]['column'] . '-p', $keys[0]['column'] . '-k', 0, $keys[0]['nexp']);
                } else {
                    $grouped[$key]->data = $this->array_group_by($grouped[$key]->data, $keys);
                }
            }
        }else{
            if($this->afs)
                foreach ($grouped as $key => $value) {
                    foreach($grouped[$key]->data as $i => $data) {
                        foreach($this->afs as $af) {
                            if($i != 0) $grouped[$key]->data[$i]->{$af} += $grouped[$key]->data[$i-1]->{$af};
                        }
                    }
                }
        }
        foreach ($grouped as $key => $value) {
            //print_r($grouped[$key]->footer);
            if(count((array)$grouped[$key]->footer)>1){
                $grouped[$key]->footer->footer = true;
                $grouped[$key]->data[] = $grouped[$key]->footer;
                $grouped[$key]->footer = count($grouped[$key]->data)-1;
            }else
                unset($grouped[$key]->footer);
        }
        //print_r($grouped);
        return $grouped;
    }

    function array_group_by_hg(array $array, array $keys, $first = false)
    {
        $key = $keys[0];
        $_key = $key['column'];
        $pkey = $_key . '-p';
        $kkey = $_key . '-k';
        $nkey = $_key;
        $df = $key['df'];
        $data = $key;
        if (count($array) > 0 && property_exists($array[0], $kkey)) $_key = $kkey;

        $last = count($keys) == 1;
        $grouped = Array();
        foreach ($array as $value) {
            $key = null;
            if (is_object($value) && property_exists($value, $_key)) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            }

            if ($key === null) {
                continue;
            }
            $key = d_format($key, $df);
            if (!$grouped[$key]) {
                $name = d_format($value->{$nkey}, $df);
                $grouped[$key] = (Object)Array('cempty' => d_format($value->{$nkey}, $df), 'ish'=>true, 'grouped-by' => $data['column'], 'grouped-key' => $key, 'data' => Array());

                if($value->{$kkey}) {
                    foreach ($this->sortdata as $s) {
                        $grouped[$key]->{'s'.$s->column} = $value->{'c'.$s->column};
                    }
                    //echo json_encode($grouped[$key]);
                }
                if (count($data['footer']) > 0) $grouped[$key]->footer = (Object)Array('cempty'=>$name.' Total');
                if(!$data['exp']) {
                    $grouped[$key]->_collapsed = true;
                    $grouped[$key]->expanded_def = true;
                }
                if($data['col']) {
                    $grouped[$key]->collapsed_def = true;
                }
                if (property_exists($value, $pkey)) $grouped[$key]->{$pkey} = $value->{$pkey};
                if (property_exists($value, $kkey)) $grouped[$key]->{$kkey} = $value->{$kkey};
            }
            $grouped[$key]->data[] = $value;
        }
        if (count($keys) > 2) {
            array_shift($keys);
            foreach ($grouped as $key => $value) {
                if (property_exists($value->data[0], $keys[0]['column'] . '-p')) {
                    $data = $this->array_group_by_hg($value->data, $keys);
                    $grouped[$key]->data = array_nest($this->populateNestedData($data, $keys[0]['column'], $keys[0]), $keys[0]['column'] . '-p', $keys[0]['column'] . '-k', 0, $keys[0]['nexp']);
                } else {
                    $grouped[$key]->data = $this->array_group_by_hg($value->data, $keys);
                }
                foreach($grouped[$key]->data as $val) {
                    foreach($val as $k=>$v) {
                        if($k[0] == 'h')$grouped[$key]->{$k} += $v;
                    }
                }
            }
        }else{
            foreach ($grouped as $key => $value) {
                $grouped[$key] = $this->get_hg_totals($grouped[$key]);
            }
        }

        foreach ($grouped as $key => $value) {
            if($grouped[$key]->footer){
                foreach($grouped[$key] as $lkey=>$lvalue) {
                    if($lkey[0] == 'h') {
                        $grouped[$key]->footer->{$lkey} = $lvalue;
                    }
                }
                $grouped[$key]->footer->footer = true;
                $grouped[$key]->data[] = $grouped[$key]->footer;
                $grouped[$key]->footer = count($grouped[$key]->data)-1;

            }else{
                unset($grouped[$key]->footer);
            }
            $hide = count($data['hide']);
            //if($data['column'] != 'c45') print_r($data);
            if($hide)
                $grouped[$key]->no0 = 1;
            //print_r($value->cempty." ".$value->footer." | ");
            if ($grouped[$key]->footer!== true && count($data['header']) == 0) $grouped[$key]->noshow = true;


        }
        return $grouped;
    }

    function populateNestedData($data, $key, $gdata) {
        //return $data;
        $column = $this->columnDefs[substr($key, 1)];
        $pkey = $key . '-p';
        $kkey = $key . '-k';
        $initial = array_column($data, $kkey);
        $initial[] = 0;
        $parents = array_diff(array_column($data, $pkey), $initial);
        $counter = 0;
        $selectline = Array();
        $newsortdata = Array();
        foreach($this->sortdata as $s) {
            if($this->columnDefs[$s->column]->table_name == $column->table_name) {
                $def = $this->columnDefs[$s->column];
                $selectline[] = $def->column_name.' as s'.$def->id;
                $newsortdata[] = $s;
            }
        }
        $selectline = implode(',', $selectline);
        if($selectline) $selectline.=', ';
        while(count($parents)) {
            $q = $this->db->select('id, parent_id, '.$selectline.' '.$column->column_name.' as name')->from($column->table_name)->where_in('id', $parents)->get();
            $parents = array();
            if ($q->num_rows() > 0) {
                foreach($q->result() as $r) {
                    //echo $r->name;
                    $counter++;
                    $temp = (Object)Array('cempty' => $r->name, $kkey => $r->id, $pkey => $r->parent_id, 'grouped-key' => $r->id, 'grouped-by' => $key, 'nodata'=>true, 'expanded_def' => !(bool)$gdata['exp'], '_collapsed' => !(bool)$gdata['exp'], 'collapsed_def' => (bool)$gdata['col']);
                    foreach($this->sortdata as $s) {
                        if($this->columnDefs[$s->column]->table_name == $column->table_name) {
                            $temp->{'s'.$s->column} = $r->{'s'.$s->column};
                        }
                    }
                    //print_r($temp);
                    $data[] = $temp;
                    if($r->parent_id != '0') $parents[] = $r->parent_id;
                    $initial[] = $r->id;
                }
                $parents = array_diff($parents, $initial);
            }
        }

        if($counter) {
            $data = $this->sortAfterPopulating($data, $newsortdata);
            //echo json_encode(Array('data'=>$data));
        }
        return $data;
    }

    function sortAfterPopulating($data, $sortdata) {
        if(count($sortdata) == 0) return $data;
        //if(count($data) == 5) echo(json_encode($data));
        return arrayOrderBy($data, $sortdata);
    }

    function get_hg_totals($item) {
        $h = $this->h;
        if($this->dateColumns) {
            foreach($item->data as $obj) {
                $item->{$this->columns[$this->cindex[d_format($obj->{'c'.$h->column}, $h->datef)]]->field} += $obj->{'c'.$h->htotal};
                $item->htotal += $obj->{'c'.$h->htotal};
                //if($item->cempty=='Ask Acountant') print_r($obj);
            }
        }else{
            foreach($item->data as $obj) {
                $fvalue = $obj->{'c'.$h->column};
                $kvalue = $fvalue;
                if($this->columnDefs[$h->column]->source) $kvalue = $obj->{'c'.$h->column.'-k'};
                if(!$this->cindex[$fvalue]) {
                    $i = count($this->columns)-1;
                    //print_r($this->columndefs);
                    $temp = array("id" => "h".$i, "field" => "h".$i, "name" => $fvalue, "horizontal" => true, "formatter" => $this->formatters[$this->columnDefs[$h->htotal]->format], "minWidth" => 120,
                        "h_column" => $h->column, "h_type" => "text", "h_cond" => '0', "h_value" => $kvalue);
                    $this->cindex[$fvalue] = $i+1;
                    $this->columns[] = (Object)$temp;
                }
                $item->{$this->columns[$this->cindex[$fvalue]]->field} += $obj->{'c'.$h->htotal};
                if($this->h->ht) $item->htotal += $obj->{'c'.$h->htotal};
            }
        }
        unset($item->data);
        return $item;
    }

    function addTotals($h) {
        if($h->ht) {
            $temp = array("id" => "htotal", "field" => "htotal", "name" => "Total", "horizontal" => true, "formatter" => $this->formatters[$this->columnDefs[$h->htotal]->format], "minWidth" => 120);
            $this->columns[] = (Object)$temp;
        }
    }

    function getCustomData($data, $settings) {
        $params = Array();
        foreach($settings->custom as $c) {
            $params[$c->name] = $c->value;
        }
        switch($settings->ctype) {
            case 1:
                function recursion($data, $c, $d) {
                    foreach($data as $obj) {
                        
                        if($obj->data) {
                            if(isset($obj->{$c}) && isset($obj->{$d})) {
                                $value = $obj->{$d} - $obj->{$c};
                                $obj->Custformula = "Trial";
                                $obj->formula_params->debit = $c;
                                $obj->formula_params->credit = $d;
                            } 
                              
                            
                            $obj->{$c} = $value < 0 ? -$value : 0;
                            $obj->{$d} = $value > 0 ? $value : 0;                            
                            $obj->data = recursion($obj->data, $c, $d);
                        }
                    }
                    return $data;
                }
                $c = 'c'.$params['param_c_credit'];
                $d = 'c'.$params['param_c_debit'];
                return recursion($data, $c, $d);

                break;
            default: return $data;
        }
    }

    function generateCashBasisTable($print = false) {
        $name = 'cb'.round(microtime(true) * 1000);
        $default_income_acct = $this->db->get_where('items', array('id' => $this->site->settings->default_RC_item), 1)->row()->acct_income;
        $this->cbname = $name;
        $this->db->save_queries = false;
        $this->db->query('CREATE TABLE '.$name.' SELECT t.*, (t.credit + t.debit) as amount, th.transaction_ref, th.transaction_date as transaction_date, "query 1" as source FROM transactions t LEFT JOIN transaction_header th ON th.id = t.trans_id LEFT JOIN properties p ON p.id = t.property_id WHERE p.active = "1" && th.transaction_type NOT IN (2,6,12,13) AND (th.basis != 1 or th.basis is null) AND (t.account_id NOT IN ('.$this->site->settings->accounts_receivable.','.$this->site->settings->accounts_payable.'))');
        $this->db->query('ALTER TABLE '.$name.' ADD COLUMN applied DECIMAL(14,2)');
                    $this->db->select(' transaction_ref, transactions.`id`,transactions.`type_id`,transactions.`type_item_id`, if(account_id = '. $this->site->settings->accounts_payable.',account_id,'.$default_income_acct.') as account_id, `profile_id`,`lease_id`,
                    `property_id`,`unit_id`,`item_id`,`trans_id`,`class_id`,`description`,
                    if(account_id = '. $this->site->settings->accounts_receivable .',(transactions.debit - transactions.credit) - ifnull(ap.amount,0),0 ) as debit ,
                    if(account_id = '. $this->site->settings->accounts_payable .',(transactions.credit - transactions.debit) - ifnull(ap.amount,0),0 ) as credit,`po_id`,`quantity`,`clr`,`paid`,`rec_id`,`deposit_id`,`line_number`,transactions.`to_email`,`billable`, 
                    if(account_id = '. $this->site->settings->accounts_receivable .',(transactions.debit - transactions.credit) - ifnull(ap.amount,0), if(account_id = '. $this->site->settings->accounts_payable .',(transactions.credit - transactions.debit) - ifnull(ap.amount,0),0 )) as amount
                    , transaction_header.transaction_date as transaction_date, "Query2" as source');
                    $this->db->from('transactions');
                    $this->db->join('transaction_header','transactions.trans_id = transaction_header.id');
                    $this->db->join('properties','transactions.property_id = properties.id');
                    $this->db->join('(select tid, sum(amount) as amount from (select transaction_id_a as tid, 0-amount as amount from applied_payments 
                    UNION ALL select transaction_id_b as tid, amount as amount from applied_payments) as ap1 group by tid
                    ) ap','transactions.id = ap.tid','left');
                    $this->db->where('(CASE WHEN (transactions.account_id = '.$this->site->settings->accounts_payable.') THEN (ap.amount != (transactions.debit - transactions.credit) or ap.amount is null)
                    WHEN (transactions.account_id = '.$this->site->settings->accounts_receivable.') THEN (ap.amount != (transactions.credit - transactions.debit) or ap.amount is null) END) AND transaction_type not in (2,6,12,13) AND (transaction_header.basis != 1 or transaction_header.basis is null) AND properties.active = "1"');
                    $q1 =$this->db->get();

        $openAP = $q1->result();
        if($openAP){
            $this->db->insert_batch($name,  $openAP);
        }
        

        $q5 = $this->db->select('t.trans_id, amount, a.id as aid, transaction_id_b, transaction_id_a, th.transaction_date as date, th.transaction_date, th.transaction_type as ttype')->from('applied_payments a')->join('transactions t', 't.id = a.transaction_id_b', 'LEFT')->join('transactions t1', 't1.id = a.transaction_id_a', 'LEFT')->join('transaction_header th', 't1.trans_id = th.id', 'LEFT')->join('properties p', 'p.id = t.property_id', 'LEFT')->where('p.active = 1')->order_by('aid ASC')->get();
        $aps = $q5->result();
        $headers = array_column($aps, 'trans_id');
        $q = $query = $this->db->query('SELECT `t`.`id`, `t`.`account_id`,`t1`.`profile_id`, if(`t1`.`debit` = 0, "credit", "debit") as accid, `t`.`property_id`, `t`.`trans_id`, `t`.`credit`, `t`.`debit`, (t.debit + t.credit) as amount, transaction_type, t.item_id, "query3" as source
        FROM `transactions` `t1` 
        
        Inner join ( select transaction_id_a as apid from applied_payments UNION select transaction_id_b from applied_payments )as ap on apid = t1.id
        
        INNER JOIN transactions t on t1.trans_id = t.trans_id
        inner join transaction_header on t1.trans_id = transaction_header.id');
        $ts = $q->result();
        $trs = quick_array_group_by($ts, 'trans_id');
        

        $result = Array();
        $lid = 0;
        $account = 0;
        foreach($aps as $ap){
            
            if($ap->trans_id!= $lid) {
                $curtrs = $trs[$ap->trans_id];
               
                
                $rollover = 0;
            }
            $amount = $ap->amount;
            $account = $curtrs[0]->accid; 

            if(($ap->ttype ==6 OR $ap->ttype ==2) && ($curtrs[0]->transaction_type ==6 OR $curtrs[0]->transaction_type == 2 ) ){
                continue;
            }
           

            
            while($amount <> 0 && count($curtrs) > 0) {
                  
                
                if($curtrs[0]->id == $ap->transaction_id_b) {
                   
                    //$account = $curtrs[0]->account_id;
                    array_shift($curtrs);
                    if(count($curtrs) == 0) continue;
                }
                if(($amount + $rollover >= $curtrs[0]->amount) or ($account == 'debit' && $curtrs[0]->credit == 0) or ($account == 'credit' && $curtrs[0]->debit == 0) ) {
                    if(($account == 'debit' && $curtrs[0]->credit == 0) or ($account == 'credit' && $curtrs[0]->debit == 0) )  $amount += $curtrs[0]->amount - $rollover; else  $amount -= $curtrs[0]->amount - $rollover;
                    //$amount -= $curtrs[0]->amount - $rollover;
                    $temp = clone $curtrs[0];
                    unset( $temp->accid);
                    unset( $temp->transaction_type);
                    $temp->applied = $curtrs[0]->amount - $rollover;
                    //$temp->amount = $ap->amount;
                    if($temp->debit != '0') $temp->debit = $temp->applied; else $temp->credit = $temp->applied;
                    $temp->transaction_date = $ap->date;
                    $result[] = $temp;
                    array_shift($curtrs);
                    $rollover = 0;
                } else {
                   
                    $temp = clone $curtrs[0];
                    unset( $temp->accid);
                    unset( $temp->transaction_type);
                    $temp->applied = $amount;
                    //$temp->amount = $ap->amount;
                    if(($account == 'debit' && $temp->credit == 0) or ($account == 'credit' && $temp->debit == 0) ) $rollover -= ($amount); else $rollover += $amount;
                    //$rollover += $amount;
                    if($temp->debit != '0') $temp->debit = $temp->applied; else $temp->credit = $temp->applied;
                    $temp->transaction_date = $ap->date;               
                    $result[] = $temp;
                    $amount = 0;
                }
               
            }
            $lid = $ap->trans_id;
        }
        //print_table($result);
        if(count($result))
            $this->db->insert_batch($name, $result);

        //negative charfes and bills

        $q = $this->db->select('transaction_ref, t.trans_id, amount, a.id as aid, transaction_id_b, transaction_id_a, th.transaction_date as date,  th.transaction_type as ttype')->from('applied_payments a')->join('transactions t', 't.id = a.transaction_id_a', 'LEFT')->join('transaction_header th', 't.trans_id = th.id', 'LEFT')->join('properties p', 'p.id = t.property_id', 'LEFT')->where('p.active', 1)->where_in('th.transaction_type', Array(2,6,12,13))->order_by('aid ASC')->get();
        $aps = $q->result();
        $headers = array_column($aps, 'trans_id');
        if ($headers){
            $q = $this->db->select('transaction_ref, t.id, t.account_id, t.property_id, t.trans_id, t.credit, t.debit, (t.credit + t.debit) as amount, transaction_type')->from('transactions t')->join('transaction_header th', 't.trans_id = th.id')->where_in('t.trans_id', $headers)->order_by('id ASC')->get();
            $trs = quick_array_group_by($q->result(), 'trans_id');
            $result2 = Array();
            $lid = 0;
            foreach($aps as $ap){
                if($ap->trans_id!= $lid) {
                    $curtrs = $trs[$ap->trans_id];
                    $rollover = 0;
                }
                $amount = $ap->amount;
    
                if(($ap->ttype ==6 OR $ap->ttype ==2) && ($curtrs[0]->transaction_type ==6 OR $curtrs[0]->transaction_type == 2 ) ){
                    continue;
                }
    
                
                while($amount > 0 && count($curtrs) > 0) {
                    if($curtrs[0]->id == $ap->transaction_id_a) {
                        array_shift($curtrs);
                        if(count($curtrs) == 0) continue;
                    }
                    if($amount + $rollover >= $curtrs[0]->amount) {
                        $amount -= $curtrs[0]->amount - $rollover;
                        $temp = clone $curtrs[0];
                        $temp->applied = ($curtrs[0]->amount - $rollover);
                        //$temp->amount = $ap->amount;
                        if($temp->debit != '0') $temp->debit = $temp->applied; else $temp->credit = $temp->applied;
                        $temp->transaction_date = $ap->date;
                        unset( $temp->transaction_type);
                        $result2[] = $temp;
                        array_shift($curtrs);
                        $rollover = 0;
                    } else {
                        $temp = clone $curtrs[0];
                        $temp->applied = $amount;
                        //$temp->amount = $ap->amount;
                        $rollover += $amount;
                        if($temp->debit != '0') $temp->debit = $temp->applied; else $temp->credit = $temp->applied;
                        $temp->transaction_date = $ap->date;
                        unset( $temp->transaction_type);
                        $result2[] = $temp;
                        $amount = 0;
                    }
                }
                $lid = $ap->trans_id;
            }
        }
        
        //print_r($result);
        if(count($result2))
            $this->db->insert_batch($name, $result2);

        if($print) {
            $q = $this->db->get($name);
            print_table($q->result());
        }
        //echo (memory_get_peak_usage()/1024/1024).' MB';
        return $name;
    }
}

function print_table($data){
    echo"<table><tr>";
    foreach($data[0] as $key=>$value)
        echo"<th>{$key}</th>";
    echo"</tr>";
    foreach($data as $row) {
        echo"<tr>";
        foreach($row as $key=>$value)
            echo"<td>{$value}</td>";
        echo"</tr>";
    }
    echo"</table>";
}
if (!function_exists('array_nest')) {
    function array_nest(array $elements, $pkey, $kkey, $parentId = 0, $nexp = 0)
    {

        $branch = array();
        foreach ($elements as $element) {
            if(!$element->{$kkey} && $parentId == 0) {
                $branch[] = $element;continue;
            } else
            if ($element->{$pkey} == $parentId && $element->{$pkey} != null) {
                $children = array_nest($elements, $pkey, $kkey, $element->{$kkey});
                if ($children) {
                    if($element->data && count($element->data) > 0 || property_exists($element,'ish')) {
                        $other = clone $element;
                        $other->cempty .= ' Other';
                        $children[] = $other;
                        $footer = null;
                        if($other->footer) {
                            $footer = (Object) Array('cempty' => $other->data[$other->footer]->cempty);
                            $other->data[$other->footer]->cempty = $element->cempty.' Other Total';
                        }
                    } else if($element->nodata && !property_exists($element,'ish')){
                        $footer = (Object) Array('cempty' => $element->cempty.' Total');
                    }
                    foreach($element as $key=>$field) {
                        if(is_numeric($field) && strpos($key, '-') === false)
                            $element->{$key} = 0;
                    }

                    foreach($children as $child) {
                        foreach($child as $key=>$field) {
                            if($key[0] == 'h' || is_numeric($field) && strpos($key, '-') === false) {
                                if($key == 'footer') $element->{$key} += 1; else
                                    $element->{$key} += $child->{$key};
                                if($key[0] == 'h' || !$child->footer) $footer = null;
                                //if($child->{'c130-p'} == 258) print_r($child);

                                if ($footer && $child->footer && $child->data[$child->footer]->{$key}) {
                                    $footer->{$key} += $child->data[$child->footer]->{$key};
                                }
                            }
                        }
                    }
                    if($footer) {
                        $footer->footer = true;
                        $children[] = $footer;
                    }
                    $element->data = $children;
                    if($nexp) {
                        $element->_collapsed = false;
                        $element->expanded_def = false;
                    }else{
                        $element->_collapsed = true;
                        $element->expanded_def = true;
                    }
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
if (!function_exists('d_format')) {
    function d_format($value, $df)
    {
        switch ($df) {
            case '0':
                return $value;
            case '1':
                return date('M Y', strtotime($value));
            case '2':
                return date('Y', strtotime($value));
        }
    }
}
if (!function_exists('add_c')) {
    function add_c($array)
    {
        foreach($array as $i => $v)
            $array[$i] = 'c' . $array[$i];
        return $array;
    }
}
function quick_array_group_by($array, $key)
{
    $grouped = Array();
    foreach ($array as $value) {
        if ($value->{$key} === null) {
            $grouped['null'][] = $value;
        } else
        $grouped[$value->{$key}][] = $value;
    }
    return $grouped;
}

function arrayOrderBy(array &$arr, $orders = null) {
    if (is_null($orders)) {
        return $arr;
    }
    usort($arr, function($a, $b) use($orders) {
        $result = array();
        foreach ($orders as $value) {
            $field = 's'.$value->column;
            $sort = $value->order;
            if (!(isset($a->{$field}) && isset($b->{$field}))) {
                continue;
            }
            if (strcasecmp($sort, 'desc') === 0) {
                if($a != $b) {
                    $tmp = $a;
                    $a = $b;
                    $b = $tmp;
                }
            }
            if (is_numeric($a->{$field}) && is_numeric($b->{$field}) ) {
                $result[] = $a->{$field} - $b->{$field};
            } else {
                $result[] = strcmp($a->{$field}, $b->{$field});
            }
        }
        return implode('', $result);
    });
    return $arr;
}