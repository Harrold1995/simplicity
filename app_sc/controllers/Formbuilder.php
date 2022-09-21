<?php defined('BASEPATH') OR exit('No direct script access allowed');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
class Formbuilder extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function printForm($id){
        $this->test($id);
    }
    function test($id){
        $this->load->model('formbuilder_model');
        $template = $this->formbuilder_model->getTemplateFromLeaseId($id);
        require_once APPPATH.'/libraries/mpdf/vendor/autoload.php';
        $fdata = $this->formbuilder_model->getFillData($id);
        //print_r($fdata);
        $data = json_decode($template->data);

        $test = 0;

		$html = $this->generateHTML($data, $fdata, -1);
        if($test) {
			echo '<br/>'.$html;
			return;
		}
        $stylesheet = 'html, input, textarea {
			font-family: \'Roboto\', sans-serif;
			line-height: 1.4;
		
		}
		
		body {
			background-color: #F2F3F4;
		}
		
		#root {
			max-width: 700px;
			margin: auto;
			background-color: white;
		}
		
		.main {
			margin-top: 50px;
			padding: 38px;
			box-sizing: content-box;
		}
		
		blockquote {
			border-left: 2px solid #ddd;
			margin-left: 0;
			margin-right: 0;
			padding-left: 10px;
			color: #aaa;
			font-style: italic;
		}
		
		table {
			border-spacing: 0;
			width:100%;
			border-collapse: collapse;
		}
		
		td {
			border: 1px solid #ccc !important;
			max-width: 0;
			padding:5px;
		}
		
		';
        $mpdf = new \Mpdf\Mpdf([
			'mode' => 'utf-8',
			'format' => [190, 236],
			'orientation' => 'P',
			'useActiveForms' => true
		]);
		$mpdf->debug = true;
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output();
    }

    public function generateHTML($data, $fdata, $counter) {
    	$html = '';
    	if($counter >= 0) {
    		$reset = false;
    		$noinput = true;
		}
    	foreach($data as $node) {
    		$start = '';
			$middle = '';
			$end = '';
			foreach($node->children as &$text){
				$text->text = nl2br($text->text);
			}
			
			switch($node->type) {
				case 'div':
					$start = '<div style="';
					if($node->style->textAlign) $start .= 'text-align: '.$node->style->textAlign.';';
					if($node->style->fontSize) $start .= 'font-size: '.$node->style->fontSize.';';
					if($node->style->fontFamily) $start .= 'font-family: '.$node->style->fontFamily.';';
					$start .= '">';
					$end = '</div>';
					break;
				case 'paragraph':
					$start = '<div>';
					$end = '</div>';
					break;
				case 'input':
					$noinput = false;
					$parts = explode('.', $node->customField);

					if($counter < 0)
						$value = is_array($fdata->{$parts[0]}) ? $fdata->{$parts[0]}[0]->{$parts[1]} : $fdata->{$parts[0]}->{$parts[1]};
					else {
						$value = is_array($fdata->{$parts[0]}) ? (count($fdata->{$parts[0]}) >= $counter ? $fdata->{$parts[0]}[$counter]->{$parts[1]} : $fdata->{$parts[0]}[0]->{$parts[1]}) : $fdata->{$parts[0]}->{$parts[1]};
						if(is_array($fdata->{$parts[0]}) && count($fdata->{$parts[0]}) >= $counter+2) {
							$this->reset = true;
							//echo count($fdata->{$parts[0]})."  ".$counter."//";
							$reset = true;
						}
						if(!is_array($fdata->{$parts[0]})) $reset = $this->reset;

					}
					//$start = '<input name="input'.$this->counter++.'" value="'.$value.'" size="'.(strlen($value)*2).'"/>';
					$start = '<b>'.$value.'</b>';
					break;
				case 'repeat':
					$this->reset = true;
					$counter = 0;
					while ($this->reset) {
						$middle .= $this->generateHTML($node->children, $fdata, $counter);
						$counter++;
					}
					$counter = -1;
					break;
				case 'image':
					$start = '<img src = "'.$node->url.'" width="'.$node->width.'" height="'.$node->height.'"/>';
					break;
				case 'bulleted-list':
					$start = '<ul>';
					$end = '</ul>';
					break;
				case 'numbered-list':
					$start = '<ol>';
					$end = '</ol>';
					break;
				case 'list-item':
					$start = '<li>';
					$end = '</li>';
					break;
				case 'table':
					$start = '<table>';
					$end = '</table>';
					break;
				case 'table-row':
					$start = '<tr>';
					$end = '</tr>';
					break;
				case 'table-cell':
					$start = '<td>';
					$end = '</td>';
					break;
				case 'block-quote':
					$start = '<blockquote>';
					$end = '</blockquote>';
					break;
				default:
					$text = $node->text;
					if($text) {
						$middle = $text;
						if($text == " ") $middle = '&nbsp;';
						
						if(preg_match_all("/\{.*}\/s", $text, $matches)) {
							$field = $matches[0];
						};
						$start = '<span style="';
						if($node->italic) $start .= 'font-style: italic;';
						if($node->bold) $start .= 'font-weight: bold;';
						if($node->underline) $start .= 'text-decoration: underline;';
						if($node->code) $middle = htmlentities($text);
						$start .= '">';
						$end = '</span>';
					}
					break;
			}
			if($node->type == 'repeat')
				$html .= $start.$middle.$end;
			else
				$html .= $start.$middle.$this->generateHTML($node->children, $fdata, $counter).$end;
		}
    	if($counter >= 0) {
    		//echo ($reset*1)."  ".$counter."//";
    		$this->reset = $noinput ? $this->reset : $reset;
		}
    	return $html;
	}

}
