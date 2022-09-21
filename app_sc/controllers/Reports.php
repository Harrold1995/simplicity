<?php defined('BASEPATH') OR exit('No direct script access allowed');
include 'app_sc/helpers/logs/logs.php';

class Reports extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('reports_model');
        $this->load->model('reportsTable_model');
        $_SESSION['allTypes'] = $this->reports_model->getAllTypes();
    }

    function index()
    {
        $this->meta['title'] = "Reports";
        $this->meta['h2'] = "Reports";
        $this->data['reports'] = $this->reports_model->getReports();
        $this->page_construct('reports/index', $this->data, $this->meta);
    }

    function edit($id = null)
    {
        $this->data['report'] = $this->reports_model->getReport($id);
        $this->data['settings'] = json_decode($this->data['report']->settings);
        $_SESSION['reportSettings'] = $this->data['settings'];
        $this->meta['title'] = "Reports";
        $this->meta['h2'] = "Report Editor: " . $this->data['report']->name;
        $this->data['types'] = $_SESSION['allTypes'];
        $this->data['id'] = $id;
        $this->data['right'] = $this->getRight($this->data['settings']->type, $id);
        $this->page_construct('reports/editor', $this->data, $this->meta);
    }

    function getModal()
    {
        $params = json_decode($this->input->post('params'));
        $this->data['t'] = $params->title;
        $id = $this->input->post('id');
        if($params->ftransfer)
            $this->data['defaults'] = $this->filtersToDefaults($id, $params->data);
        else
            $this->data['defaults'] = explode('$$', ltrim($params->data, '$$'));
        $this->data['types'] = $_SESSION['allTypes'];
        if ($id == '0') {
            $type = $params->type;
            $this->data['rtype'] = $type;
            $columns = $this->reports_model->getAllColumnsArray($type);
            $cs = Array();
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
            $filters = explode(',', $params->filters);
            foreach ($filters as $f) {
                $fdata = explode('|', $f);
                $fdata[0] = ltrim($fdata[0], 'c');
                $filter = new stdClass();
                $filter->condition = ($fdata[1]) ? (int)$fdata[1] : 0;
                $filter->column = (int)$fdata[0];
                if (!$cs[$filter->column]) continue;
                $filter->dtype = $cs[$fdata[0]]['type'];
                $this->data['fs'][] = $filter;
            }
        } elseif ($id == '-1') {
            $this->data['settings'] = $params->settings;
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
            $this->data['fs'] = $this->data['settings']->ufilters;
            $this->data['params'] = $this->data['settings']->params;
            $columns = $this->reports_model->getAllColumnsArray($this->data['settings']->type);
            $this->data['columns'] = Array();
            if (isset($this->data['settings']->cf))
                foreach ($this->data['settings']->cf as $cf) {
                    $temp = Array();
                    $temp['id'] = $cf->id;
                    $temp['name'] = $cf->name;
                    $this->data['columns'][$temp['id']] = $temp;
                }
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
        } else {
            $this->data['report'] = $this->reports_model->getReport($id);
            $this->data['report']->settings = $this->dynamicDefaults( $this->data['report']->settings);
            $this->data['settings'] = json_decode(str_replace($this->reportsTable_model->defparams_from, $this->reportsTable_model->defparams_to, $this->data['report']->settings));
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
            $this->data['fs'] = $this->data['settings']->ufilters;
            $this->data['params'] = $this->data['settings']->params;
            $columns = $this->reports_model->getAllColumnsArray($this->data['settings']->type);
            $this->data['columns'] = Array();
            if (isset($this->data['settings']->cf))
                foreach ($this->data['settings']->cf as $cf) {
                    $temp = Array();
                    $temp['id'] = $cf->id;
                    $temp['name'] = $cf->name;
                    $this->data['columns'][$temp['id']] = $temp;
                }
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
        }
        $this->data['columns'] = $cs;
        $this->data['signs'] = Array("equals", "not Equals", "like", "between", "greater than", "less than", "is in");
        $this->data['id'] = $id;
        if ($id != '0') $this->data['title'] = $this->data['report']->name . ' ' . $this->data['t']; else
            $this->data['title'] = $this->data['t'];
        $this->load->view('forms/reports/report', $this->data);
    }

    function dynamicDefaults($settings){
        $settings = str_replace("{today}", date('m/d/Y'), $settings);
        $settings = str_replace("{endOfLastYear}",  date("m/d/Y",strtotime("last year December 31st")), $settings);
        $settings = str_replace("{begOfLastYear}", date("m/d/Y",strtotime("last year January 1st")), $settings);
        $settings = str_replace("{begOfThisYear}", date('01/01/Y'), $settings);
        $settings = str_replace("{endOfLastMonth}", date('m/d/Y', strtotime('last day of previous month')), $settings);
        $settings = str_replace("{begOfThisMonth}", date('m/01/Y'), $settings);
        return $settings;
    }

    function customGetData()
    {
        $filters = $this->input->post('filters');
        $len = count($filters);
        $message = " ";
        foreach ($filters as &$filter) {
            $col = $filter['col'];
            $cond = $filter['cond'];
            $ttype = $filter['ttype'];
            $val = $filter['val'];

            if ($ttype == "text") {
                $val = "'" . $val . "'";
            } elseif ($ttype == "date") {
                $val = "'" . date("Y-m-d", strtotime($val)) . "'";
            } else {
                $val = (float)$val;
            }
            if ($filter === reset($filters)) {
                $message = $message . "WHERE " . $col . " " . $cond . " " . $val;
            } else {
                $message = $message . " AND " . $col . " " . $cond . " " . $val;
            }
        }
        $message2 = $this->reports_model->CustReport(13, $message);
        $this->data = $this->reports_model->CustReport(13, $message);

        echo json_encode($message2);
    }

    function customGetModal()
    {
        $id = $this->input->post('id');
        $params = json_decode($this->input->post('params'));
        $this->data['t'] = $params->title;
        $this->data['defaults'] = explode('$$', ltrim($params->data, '$$'));
        if ($id == '0') {
            $type = $params->type;
            $this->data['rtype'] = $type;
            $columns = $this->reports_model->getAllColumnsArray($type);
            $cs = Array();
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
            $filters = explode(',', $params->filters);
            foreach ($filters as $f) {
                $fdata = explode('|', $f);
                $fdata[0] = ltrim($fdata[0], 'c');
                $filter = new stdClass();
                $filter->condition = ($fdata[1]) ? (int)$fdata[1] : 0;
                $filter->column = (int)$fdata[0];
                if (!$cs[$filter->column]) continue;
                $filter->dtype = $cs[$fdata[0]]['type'];
                $this->data['fs'][] = $filter;
            }
        } elseif ($id == '-1') {
            $this->data['settings'] = $params->settings;
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
            $this->data['fs'] = $this->data['settings']->ufilters;
            $this->data['cs'] = $this->data['settings']->custom;
            $columns = $this->reports_model->getAllColumnsArray($this->data['settings']->type);
            $this->data['columns'] = Array();
            if (isset($this->data['settings']->cf))
                foreach ($this->data['settings']->cf as $cf) {
                    $temp = Array();
                    $temp['id'] = $cf->id;
                    $temp['name'] = $cf->name;
                    $this->data['columns'][$temp['id']] = $temp;
                }
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
        } else {
            $this->data['report'] = $this->reports_model->getReport($id);
            $this->data['settings'] = json_decode($this->data['report']->settings);
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
            $this->data['fs'] = $this->data['settings']->ufilters;
            $this->data['cs'] = $this->data['settings']->custom;
            $columns = $this->reports_model->getAllColumnsArray($this->data['settings']->type);
            $this->data['columns'] = Array();
            if (isset($this->data['settings']->cf))
                foreach ($this->data['settings']->cf as $cf) {
                    $temp = Array();
                    $temp['id'] = $cf->id;
                    $temp['name'] = $cf->name;
                    $this->data['columns'][$temp['id']] = $temp;
                }
            foreach ($columns as $c) {
                $cs[$c['id']] = $c;
            }
            $this->data['mtype'] = $_SESSION['allTypes'][$this->data['settings']->type]->modal_type;
        }
        $this->data['columns'] = $cs;
        $this->data['signs'] = Array("equals", "not Equals", "like", "between", "greater than", "less than");
        $this->data['id'] = $id;
        if ($id != '0') $this->data['title'] = $this->data['report']->name . ' ' . $this->data['t']; else
            $this->data['title'] = $this->data['t'];
        $this->load->view('forms/reports/custom_report', $this->data);
    }

    function getAddReportModal()
    {
        $id = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url() . 'reports/addReport';
                $this->data['title'] = 'Create New Report';
                break;
            case 'edit' :
                $this->load->model('leases_model');
                $this->data['target'] = base_url() . 'settings/editLTemplate/' . $id;
                $this->data['title'] = 'Edit Lease Template';
                $this->data['template'] = $this->leases_model->getLeaseTemplate($id);
                break;
        }
        $this->load->view('forms/reports/addreport', $this->data);
    }

    function getRecordSetModal()
    {
        $id = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url() . 'reports/addRecordSet';
                $this->data['title'] = 'Create New Record Set';
                break;
            case 'edit' :
                $this->load->model('leases_model');
                $this->data['target'] = base_url() . 'settings/editLTemplate/' . $id;
                $this->data['title'] = 'Edit Lease Template';
                $this->data['template'] = $this->leases_model->getLeaseTemplate($id);
                break;
        }
        $this->load->view('forms/reports/recordset', $this->data);
    }

    function addFieldModal()
    {
        $id = $this->input->post('id');
        switch ($this->input->post('mode')) {
            case 'add' :
                $this->data['target'] = base_url() . 'reports/addField';
                $this->data['title'] = 'Add custom Field';
                break;
            case 'edit' :
                $params = json_decode($this->input->post('params'));
                $this->data['set'] = $params->data;
                $this->data['target'] = base_url() . 'reports/editField';
                $this->data['title'] = 'Edit custom Field';
                break;
        }
        $this->load->view('forms/reports/cfield', $this->data);
    }

    function addField()
    {
        $data = $this->input->post();
        echo json_encode(array('type' => 'success', 'message' => 'Custom field successfully added.', 'data' => json_encode($data)));
    }

    function editField()
    {
        $data = $this->input->post();
        echo json_encode(array('type' => 'success', 'message' => 'Custom field successfully updated.', 'data' => json_encode($data)));
    }

    function addReport()
    {
        $data = $this->input->post();
        if ($this->reports_model->addReport($data))
            echo json_encode(array('type' => 'success', 'message' => 'Report successfully added.'));
    }

    function addRecordset()
    {
        $data = $this->input->post('r');
        $fields = $this->input->post('field');
        if ($this->reports_model->addRecordset($data, $fields))
            echo json_encode(array('type' => 'success', 'message' => 'Recordset successfully added.'));
    }

    function editReport($id)
    {
        $data = $this->input->post();
        if ($this->reports_model->editReport($data, $id))
            echo json_encode(array('type' => 'success', 'message' => 'Report successfully updated.'));
    }

    function deleteReport($id)
    {
        if ($this->reports_model->deleteReport($id))
            echo json_encode(array('type' => 'success', 'message' => 'Report successfully deleted.'));
    }

    function duplicateReport($id)
    {
        $data = $this->reports_model->getReport($id);
        $data->name .= ' Copy';
        unset($data->id);
        if ($rid = $this->reports_model->addReport((Array)$data))
            echo json_encode(array('type' => 'success', 'message' => 'Report successfully added.', 'redirect' => base_url('reports/edit/'.$rid)));
    }

    function save($id = null)
    {
        $settings = json_encode($this->input->post('data'));
        $name = $this->input->post('data')['name'];
        //print_r($this->input->post('data')['name']);
        $redirect = null;
        if($id == null) {
            $id = $this->reports_model->addReport(Array("name" => $name));
            $redirect = base_url('reports/edit/'.$id);
        }
        if ($this->reports_model->saveReport($id, $settings, $name))
            echo json_encode(array('type' => 'success', 'message' => 'Report successfully saved.', 'redirect' => $redirect));
    }

    function getSettings($id, $type)
    {
        session_write_close();
        if ($id != '0' && $id != '-1') {
            if($id && $id != 'undefined') {
                $report = $this->reports_model->getReport($id);
                $report->settings = $this->dynamicDefaults($report->settings);
                $report->settings = json_decode($report->settings);
            } else {
                $report = (Object)Array('settings'=>(Object)Array('type'=>$type));
            }
            $columns = $this->reports_model->getAllColumnsArray($type);
            $cnames = Array();
            $i = 0;
            foreach ($columns as $c) {
                $temp = new stdClass();
                $temp->id = $c['id'];
                $temp->name = $c['name'];
                $temp->active = $c['active'];
                $temp->type = $c['type'];
                $temp->key = $c['key_column'];
                $temp->source = $c['source'] && $c['key_column'] ? $c['source'] : null;
                $temp->tag = $c['source'];
                $cnames[$c['id']] = $temp;
            }
            if (isset($report->settings->cf))
                foreach ($report->settings->cf as &$cf) {
                    $cf->active = 1;
                    $cnames[$cf->id] = $cf;
                }
            $report->settings->cnames = $cnames;
        } else {
            $report = new stdClass();
            $columns = $this->reports_model->getAllColumnsArray($type);
            $cnames = Array();
            $i = 0;
            foreach ($columns as $c) {
                $temp = new stdClass();
                $temp->id = $c['id'];
                $temp->name = $c['name'];
                $temp->type = $c['type'];
                $temp->active = $c['active'];
                $temp->index = $i++;
                $cnames[$c['id']] = $temp;
            }

            $report->settings->cnames = $cnames;
        }
        //$report->settings->typeNames = Array('2' => 'Leases', '8' => 'Transactions', '1' => 'Properties');
        $report->settings->deftype = $report->settings->type;
        echo json_encode($report->settings);
    }

    function getLeftColumn($type, $id)
    {
        $this->data['columns'] = $this->reports_model->getAllColumns($type);
        $this->data['id'] = $id;
        $this->data['visible'] = $_SESSION['reportSettings']->columns;
        if ($id != 0) $this->data['cf'] = $_SESSION['reportSettings']->cf;
        echo $this->load->view('reports/left_column', $this->data, true);
    }

    function getBottom($type, $id)
    {
        $this->data['columns'] = $this->input->post('columns');
        if ($id != 0) {
            $this->data['grouping'] = $_SESSION['reportSettings']->grouping;
            $this->data['top'] = $_SESSION['reportSettings']->top;
        }
        echo $this->load->view('reports/bottom', $this->data, true);
    }

    function getRight($type, $id)
    {
        //$this->data['columns'] = $this->input->post('columns');
        $this->data['columns'] = $this->reports_model->getAllColumnsArray($type);
        if ($id != 0) {
            $this->data['filters'] = $_SESSION['reportSettings']->filters;
            $this->data['ufilters'] = $_SESSION['reportSettings']->ufilters;
            $this->data['params'] = $_SESSION['reportSettings']->params;
            $this->data['cf'] = $_SESSION['reportSettings']->cf;
            $this->data['cr'] = $_SESSION['reportSettings']->cr;
            $this->data['iscash'] = $_SESSION['reportSettings']->iscash;
        }
        return $this->load->view('reports/right', $this->data, true);
    }

    function getAjaxTable($type)
    {
        session_write_close();
        $this->load->model('reportsTable_model');
        $settings = json_decode($this->input->post('settings'));
        if($settings->deftype) $type = $settings->deftype;
        $this->load->model('logs_model');
        $update_title_log = new Log_General($this->ion_auth->get_user_id(), $settings->name.' Report', $type, "pulled", $settings->filters);
		$this->logs_model->add_log($update_title_log);
        echo $this->reportsTable_model->getReport($type, $settings);
    }

    function getAjaxTableHeader($type, $id = null)
    {
        session_write_close();
        $cf = $this->input->post('cf');
        $order = Array();
        if ($id != null) $order = $_SESSION['reportSettings']->corder;
        echo json_encode(Array('columns' => $this->reports_model->getTableHeader($type, $cf), 'corder' => $order));
    }

    function getGroupingLine()
    {
        $this->data['columns'] = $this->input->post('columns');
        $this->data['ind'] = $this->input->post('ind');
        echo $this->load->view('reports/grouping-line-template', $this->data, true);
    }

    function getSortingLine()
    {
        $this->data['columns'] = $this->input->post('columns');
        $this->data['ind'] = $this->input->post('ind');
        echo $this->load->view('reports/sorting-line-template', $this->data, true);
    }

    function getFiltersLine()
    {
        $this->data['columns'] = $this->input->post('columns');
        $this->data['ind'] = $this->input->post('ind');
        echo $this->load->view('reports/filter-line-template', $this->data, true);
    }

    function test()
    {
        $start = microtime(true);;
        $ts = $this->reports_model->getReportData(1, []);
        $result = array_group_by($ts, "c12", "c6", "c7", "c8");

        print_r((microtime(true)) - $start . " msec");
        print_r($result);
    }

    function excel($return = false)
    {
        $letters = range('A', 'Z');
        $data = json_decode($this->input->post('rows'));
        $columns = $this->input->post('columns');
        $str = "";
        $str.="<table class='reportPrintTable' style='display:table !important;border:none;margin: auto;'>";
        $str.= "<tr style='display: table-row !important;	border-bottom: solid 1px #000;'>";
        foreach ($columns as $row) {
            $str.= '<td style="border:none;padding:10px;">' . $row['name'] . '</td>';
        }
        $str.= "</tr>";
        $cols = array_column($columns, 'field');
        $formatters = array_column($columns, 'strformatter');
        $ranges = Array();
        $lastindent = -1;
        foreach ($data as $j=>&$row) {
            $rid = $j + 2;
            if($row->footer === true) {
                if($row->formula){
                    // if it is the grand total with a special formula
                    $temp = $ranges[$row->indent]->range;
                    $formula = preg_replace_callback('({([0-9]+)})',
                            function ($m) use ($temp) {
                                return '('.($temp[(int)$m[1]-1] ? $temp[(int)$m[1]-1] : 0).')';
                            },
                            $row->formula
                    );
                    $formula = $formula;
                } else {
                    $formula = "SUM(".parseRange($ranges[$row->indent]->range).")";
                }               
                $row->formula = $formula;
            }
            if ($row->indent < $lastindent) {
                for($k=0;$k<$lastindent - $row->indent; $k++) {
                    $formula = "SUM(".parseRange($ranges[$lastindent-$k]->range).")";
                    $ranges[$lastindent-1-$k]->header->formula = $formula;
                    $ranges[$lastindent-1-$k]->header = &$row;
                    $ranges[$lastindent-$k]->range = Array();
                }
            }
            if($row->footer !== true) {
                $ranges[$row->indent]->header = &$row;
                if($row->footer)
                    $ranges[$row->indent]->range[] = 'A' . findFooter($data, $j, $rid);
                else
                    $ranges[$row->indent]->range[] = 'A' . $rid;
            }

            $lastindent = $row->indent;
            //print_r($ranges);
            //echo"<br/><br/>";
        }
        //return;
        unset($row);
        foreach ($data as $j=>$row) {
            $str.= "<tr style='display: table-row !important;	border:none;".($row->footer === true ? "border-top:1px;" : "")."'>";
            foreach ($cols as $i => $column) {
                if($column != 'cempty' && $row->$column && $row->formula){
                    $newformula1 = str_replace('A', $letters[$i], $row->formula);
                    if ($row->Custformula == "Trial"){
                        $debit = $row->formula_params->debit;
                        $credit = $row->formula_params->credit;
                        $secondCol = ($column == $debit) ? array_search($credit, $cols) : array_search($debit, $cols);

                        $newformula1 = str_replace('A', $letters[$i], $row->formula).'-'.str_replace('A', $letters[$secondCol], $row->formula);
                    } 

                    $str.= "<td style='border:none;".($formatters[$i] == 'UsdFormatter' ? 'mso-number-format:"\0022$\0022\#\,\#\#0\.00";' : "")."".($row->footer === true ? "border-top:solid 1px #000;font-weight:bold;" : "")."'>=".$newformula1."</td>";
                }                   
                else
                    $str.= "<td style='border:none;".($formatters[$i] == 'UsdFormatter' ? 'mso-number-format:"\0022$\0022\#\,\#\#0\.00";' : "")."".($row->footer === true ? "border-top:solid 1px #000;font-weight:bold;" : "")."".($column == 'cempty' ? 'padding-left:'.(20*($row->indent == 0 ? $row->indent : $row->indent - (int)($row->footer === true)))."px'" : "padding:0 10px;'").">" . $this->formatCell1($row->{$column}, $formatters[$i] ) . "</td>";
            }
            $str.= "</tr>";
        }
        $str.= "</table>";
        if($return) return $str; else echo $str;
    }

    function print($return = false)
    {

        $data = json_decode($this->input->post('rows'));
        $tr = $this->input->post('tr');
        $columns = $this->input->post('columns');
        $colspan = count($columns);
        $str = "";
        $str.="<table class='reportPrintTable' style='display:table !important;border:none;margin:auto; page-break-inside:auto; '>";
        $str.= "<thead><tr style='display: table-row !important;border:none;'>
                <td style ='border-bottom: 1px solid black; border-top:none; border-right:none; border-left:none; text-align: center;' id='headerInfo' colspan=".$colspan."></td>
             </tr><tr style='display: table-row !important;	border:none;'>";
        $horizontal = false;
        foreach ($columns as $row) {
            $str.= '<td style="border:none;padding:10px;">' . $row['name'] . '</td>';
            if($row['horizontal']) $horizontal = true;
        }
        $str.= "</tr></thead>";
        $cols = array_column($columns, 'field');
        $formatters = array_column($columns, 'strformatter');
        foreach ($data as $row) {
            $str.= "<tr style='display: table-row !important;	border:none;'>";
            foreach ($cols as $i => $column) {
                $str.= "<td style='border-top: 1px solid #f7f0f0; border-bottom: none; border-right: none; border-left: none;text-overflow:ellipsis;overflow:hidden;".($tr == 1 ? "white-space:nowrap;" : "")."max-width:".(int)(1200/count($cols))."px;".($row->footer === true ? "border-top:solid 1px #000;font-weight:bold;" : "")."".($column == 'cempty' ? 'padding-left:'.(20*($row->indent == 0 ? $row->indent : $row->indent - (int)($row->footer===true)))."px'" : "padding:0 10px;'").">" . $this->formatCell($row->$column, $formatters[$i], $row, $horizontal) . "</td>";
            }
            $str.= "</tr>";
        }
        $str.= "<tfoot><tr><td colspan=".$colspan." style ='flex: 0 0 auto; border:none; overflow:visible;'><footer>
        <ul>
            
             <li  style ='width:90%; color:#aea9a9;' ><span style ='float:left'>Simpli-City Software</span>    <span style ='float:right'>".date('m/d/Y h:i:s a', time())."</span></li>
        </ul>
    </footer></td></tr></tfoot></table>";
        if($return) return $str; else echo $str;
    }

    function pdf(){

        $filename = $this->input->post('filename')?$this->input->post('filename'): "report2";


        ini_set('memory_limit', '1024M');
        $company_logo = $this->site->settings->company_logo ? '<img src="'.'uploads/images/'.$this->site->settings->company_logo.'" alt="Logo" width="150" height="150"></img><br/>' :"";
        //$html ="<table class='reportPrintTable' style='display:table !important;border:none;width:100%; page-break-inside:auto; margin-top: -30px;'>";
        $html = $columns = $company_logo.$this->input->post('header');
        $html .= $this->print(true);
        require_once APPPATH.'/libraries/mpdf/vendor/autoload.php';

        $mpdf = new \Mpdf\Mpdf();

        $stylesheet = ' h2,h4{color:#000 !important; margin:0px !important; font-family: Montserrat, Segoe_UI, "Segoe UI", Arial, Helvetica, sans-serif;} tr,th,td{ font-size:13px; font-family: Montserrat, Segoe_UI, "Segoe UI", Arial, Helvetica, sans-serif;} th{font-weight:bold}';
        $mpdf->WriteHTML($stylesheet, 1);
        $i=2;
        while(strlen($html)>0){
            $html1 = substr($html,0, 999999);
            $html = substr($html,1000000);
            $mpdf->WriteHTML($html1, $i++);
        }
        $mpdf->Output('uploads/documents/'.$filename.'.pdf','F');
        echo base_url('uploads/documents/'.$filename.'.pdf');
    }

    function formatCell($value, $format, $row, $horizontal = false) {
        switch($format){
            case 'UsdFormatter':
                if($horizontal && $row->noshow) return '';
                return $value ? '$'.number_format($value, 2) : ($horizontal ? '$0' : '');
            case 'NumFormatter':
                if($horizontal && $row->noshow) return '';
                return $value ? $value : ($horizontal ? '0' : '');
            case 'DateFormatter': return $value ? (new DateTime($value))->format('m/d/Y') : '';
            default: return $value;
        }
    }

    function formatCell1($value, $format) {
        switch($format){
            case 'UsdFormatter': return $value ? $value : '';
            case 'DateFormatter': return $value ? (new DateTime($value))->format('m/d/Y') : '';
            default: return $value;
        }
    }

    function cbtest() {
        $this->load->model('reportsTable_model');
        echo $this->reportsTable_model->generateCashBasisTable(true);
    }

    function filtersToDefaults($id, $data) {
        $report = $this->reports_model->getReport($id);
        $report->settings = json_decode($report->settings);
        $newfilters = $report->settings->ufilters;
        $newparams = $report->settings->params;
        $newc = Array();
        $oldc = Array();
        $temp = $this->reports_model->getAllColumnsArray($report->settings->type);
        foreach ($temp as $c) {
            $newc[$c['id']] = $c;
        }
        $temp = $this->reports_model->getAllColumnsArray($data->type);
        foreach ($temp as $c) {
            $oldc[$c['source']] = $c;
        }
        $defaults = Array();
        
        foreach ($newfilters as $nf) {
            $source = $newc[$nf->column]['source'];
            if($oldc[$source]){
                $oldi = $oldc[$source]['id'];
                $temp = Array();
                $temp = array_filter($data->filters, function($obj) use ($oldi){
                        if($obj->column == $oldi)
                        return true;
                    });
                if(count($temp) > 0) {
                    if($nf->condition == 3 && $temp[0]->condition == 3)
                        $defaults[] = $temp[0]->fields[0]->name1.'|'.$temp[0]->fields[0]->name2;
                    else if($nf->condition == 3 && $temp[0]->condition != 3)
                        $defaults[] = $temp[0]->fields[0]->value.'|'.$temp[0]->fields[0]->value;
                    else if($nf->condition != 3 && $temp[0]->condition == 3)
                        $defaults[] = $temp[0]->fields[0]->name2;
                    else $defaults[] = $temp[0]->fields[0]->value;
                    continue;
                }
            }
            $temp = Array();
            $temp = array_filter($data->params, function($obj) use ($source){
                if($obj->source == $source)
                    return true;
            });
            if(count($temp) > 0) {
                if($nf->condition == 3)
                    $defaults[] = $temp[0]->value.'|'.$temp[0]->value;
                else
                    $defaults[] = $temp[0]->value;
                continue;
            }
            $defaults[] = null;
        }
        foreach ($newparams as $p) {
            $source = $p->source;
            if($oldc[$source]){
                $oldi = $oldc[$source]['id'];
                $temp = Array();
                $temp = array_filter($data->filters, function($obj) use ($oldi){
                    if($obj->column == $oldi)
                        return true;
                });
                if(count($temp) > 0) {
                    if($temp[0]->condition == 3)
                        $defaults[] = $temp[0]->fields[0]->name2;
                    else
                        $defaults[] = $temp[0]->fields[0]->value;
                    continue;
                }
            }
            $temp = Array();
            $temp = array_filter($data->params, function($obj) use ($source){
                if($obj->source == $source)
                    return true;
            });
            if(count($temp) > 0) {
                $defaults[] = $temp[0]->value;
                continue;
            }
            $defaults[] = null;
        }
        return $defaults;
    }


}
function findFooter($data, $i, $default) {
    $indent = $data[$i]->indent;
    $result = $default;
    for($j=$i+1;$j<count($data);$j++) {
        if($data[$j]->indent == $indent+1)
            $result = $j+2;
        else if($data[$j]->indent <= $indent)
            break;
    }
    return $result;
}
function parseRange($range) {
    //print_r($range);
    $opener = $range[0];
    $last = (int)substr($opener, 1);
    $string = $opener;
    $counter = 1;

    //Changed to add individual cells since original formula included subaccount totals twice
    foreach($range as $i=>$r) {
        if ($i == 0) continue;
        $next = (int)substr($r, 1);
        //print_r($next. ' ' . $last.' || ');
        if( $i <= count($range)-1) {
            //if($counter == 1) $string .= ','.$r; else $string .= ':'.$r;
            $string .= ','.$r;
        } else if($next - $last != 1 || $i == count($range)-1) {
            //if($counter == 1) $string .= ','.$r; else $string .= ':'.$range[$i-1].','.$r;
            $string .= ','.$r;
            $counter = 0;
        }
        $last = $next;
        $counter++;
    }
       //this is the original code want to add back the range sum but have to make sure when there is a break when there is subaccounts so it doesnt get calculated twice in the formula
    /* foreach($range as $i=>$r) {
        if ($i == 0) continue;
        $next = (int)substr($r, 1);
        //print_r($next. ' ' . $last.' || ');
        if( $i == count($range)-1) {
            if($counter == 1) $string .= ','.$r; else $string .= ':'.$r;
        } else if($next - $last != 1 || $i == count($range)-1) {
            if($counter == 1) $string .= ','.$r; else $string .= ':'.$range[$i-1].','.$r;
            $counter = 0;
        }
        $last = $next;
        $counter++;
    } */
    //echo" <br/>  endrange <br/>";
    return $string;
}