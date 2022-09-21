<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ofx_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
   
    
    function getOfx($id)//($id)//($id)//$id account id

    {
        
        $this->load->model('encryption_model');
        $this->db->select('cc_num, user_id AS userId, password');
        $this->db->from('credit_cards');
        $this->db->where('account_id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $data = $q->row();
            //$data = $this->encryption_model->decryptThis($data);
            //$cc_num = $data->cc_num;
            //$userId = $data->userId;
            //$password = $data->password;
            // $cc_num = $q->row()->cc_num;
            // $userId = $q->row()->userId;
            // $password = $q->row()->password;
        }
        
    
        $url = 'https://online.americanexpress.com/myca/ofxdl/desktop/desktopDownload.do?request_type=nl_ofxdownload';
        $time = date('YmdHis');
        $file = $time .'.ofx';
        $fid = 3101;
          $cc_num = 'RQ9B09F9AFGZEED|05009'; 
          $userId = 'mordy2432';
          $password = 'rockthehouse232';
        $request = 
            "OFXHEADER:100\n".
            "DATA:OFXSGML\n".
            "VERSION:102\n".
            "SECURITY:NONE\n".
            "ENCODING:USASCII\n".
            "CHARSET:1252\n".
            "COMPRESSION:NONE\n".
            "OLDFILEUID:NONE\n".
            "NEWFILEUID:".$time ."\n".
            "\n".
            "<OFX>\n".
                "<SIGNONMSGSRQV1>\n".
                    "<SONRQ>\n".
                        "<DTCLIENT>".$time ."\n".
                        "<USERID>".$userId ."\n".//$userId
                        "<USERPASS>".$password ."\n".// $password
                        "<LANGUAGE>ENG\n".
                        "<FI>\n".
                            "<ORG>AMEX\n".
                            "<FID>".$fid."\n".
                        "</FI>\n".
                        "<APPID>QBW\n".// QWIN
                        "<APPVER>1800\n".// 2200
                    "</SONRQ>\n".
                "</SIGNONMSGSRQV1>\n".
                "	<CREDITCARDMSGSRQV1>\n".
                        "		<CCSTMTTRNRQ>\n".
                        "			<TRNUID>". $time ."\n".
                        "			<CCSTMTRQ>\n".
                        //"			<INCTRANIMG>Y\n".
                        "				<CCACCTFROM>\n".
                        "					<ACCTID>".$cc_num."\n".
                      //  "                    <CLTCOOKIE>1\n" .
                        "				</CCACCTFROM>\n".
                        "				<INCTRAN>\n".
                        "               <DTSTART>20190515000000\n".
                        "					<DTEND>20190607155708\n".
                        "					<INCLUDE>Y\n".
                        "				</INCTRAN>\n".
                        "			</CCSTMTRQ>\n".
                        "		</CCSTMTTRNRQ>\n".
                        "	</CREDITCARDMSGSRQV1>\n".
                        "</OFX>";
                $c = curl_init();
                curl_setopt($c, CURLOPT_URL,$url );
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/x-ofx'));
                curl_setopt($c, CURLOPT_POSTFIELDS, $request);
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($c, CURLOPT_SSL_VERIFYPEER, FALSE);
                $cont = curl_exec($c);
                curl_close ($c);
                file_put_contents($file, $cont);
                preg_match('/<MESSAGE\>(.*?)<\/STATUS\>/', $cont, $match); 
                $message = ($match[1]);
                $codeStartsAt = strrpos($cont, '<CODE>') + strlen('<CODE>');
                $codeEndsAt = strrpos($cont, '<SEVERITY>');
                $code = substr($cont, $codeStartsAt, $codeEndsAt - $codeStartsAt);
                if($cont && ($code == 0)){
                $this->parseOfx($file, $id);
                //$empty = "";
                return true; 
                }
                else{if($cont){
                    //log_message('error', "code is $code and message is $message");

                    $error = ($cont === 'Forbidden') ? 'Financial Institution responded - Forbidden - Login failed please check your login info' : "Financial Institution responded code is $code and message is $message";
                    // switch($code){
                    //     case 15500:
                    //          echo 'bla bla bla';
                    //          break;
                    //     case 15500:
                    //          echo 'bla bla bla';
                    //          break;
                    //     case 15500:
                    //          echo 'bla bla bla';
                    //          break;
                    //     case 15500:
                    //          echo 'bla bla bla';
                    //          break;
                    // }
                      return $error;
                    }
                      else{
                          $error2 =  'no response from FI please make sure you have correct url';
                          return $error2;
                      }
                }
                //return 'result.ofx';

                

    }   

    function parseOfx($file, $id)
    {
        //require_once '../../vendor/autoload.php';

        //$MyFile = file_get_contents("app_sc/controllers/chase.ofx");
        //$fp=file_get_contents(APPPATH . 'controllers/chase.ofx');
        //file_get_contents('Chase.ofx');
        //var_dump($MyFile);
        $ofxParser = new \OfxParser\Parser();
        //$ofx = $ofxParser->loadFromFile('app_sc/models/' . $file);
        $ofx = $ofxParser->loadFromFile($file);
        $account = $ofx->signOn->institute->name;
        $accountNumber = $ofx->bankAccounts[0]->accountNumber;
        //var_dump($ofx);
        //print_r($ofx);
        $bankAccount = reset($ofx->bankAccounts);

        // Get the statement start and end dates
        $startDate = $bankAccount->statement->startDate;
        $endDate = $bankAccount->statement->endDate;
        //echo $startDate->date;
        // Get the statement transactions for the account
        //var_dump($bankAccount);
        $transactions = $bankAccount->statement->transactions;
        //echo $account;echo"<br>";
        //echo $accountNumber;echo"<br>";
        // foreach($transactions as $transaction)
        // {
        //     //var_dump($transaction);   
        //     echo date_format($transaction->date,'m/d/Y')."<br>";
        //     echo "$transaction->type<br>";
        //     echo "$transaction->amount<br>";
        //     echo "$transaction->name<br>";
        //     echo "$transaction->memo<br>";
        //     echo "$transaction->uniqueId<br>";
        // } 

        $trans = [];
        foreach($transactions as $transaction)
        {   
            $memoInfo = $transaction->memo;
            $info = explode ( '-' , $memoInfo , 3 );
            $trans['date'] = date_format($transaction->date,'Y-m-d');
            $trans['transaction_type'] =$transaction->type;
            $trans['amount'] = $transaction->amount;
            $trans['name'] = $transaction->name;
            $trans['card_member'] = $info[0];
            $trans['cc_num'] = $info[1];
            $trans['memo'] = $info[2];
            $trans['uniqueId'] = $transaction->uniqueId;
            $trans['account_id'] = $id;
            $transac[] = $trans;
            
        }
        
        foreach($transac as $tran)
        {
            $transactionsArray = [];
            foreach ($tran as $key => $value)
            {
                $transactionsArray[] = $key . '= VALUES(' . $key .')';
            }
                                
            $sql = $this->db->insert_string('ofx_imports', $tran) . ' ON DUPLICATE KEY UPDATE ' .
            implode(', ', $transactionsArray);
            $this->db->query($sql);

        }  

         
    }

       }
    
    

