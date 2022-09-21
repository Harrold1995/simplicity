       
       <?php defined('BASEPATH') OR exit('No direct script access allowed');

class PropertyTaxes_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('encryption_model');
    }

        public function getAllPropertyTaxes()
        {
            $this->db->select('pt.id, p.name AS property, pt.borough, pt.block, pt.payment_acct as "Payment acct", pt.lot, f.name AS frequency, pt.start_date as "start date", pt.last_pay_date as "pay date", pt.amount');
            $this->db->from('property_tax pt');
            $this->db->join('frequencies f','pt.frequency = f.id');
            $this->db->join('properties p','p.id = pt.property_id');
            $q = $this->db->get();
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }

        function editPropertyTaxes($propertyTax){
            //foreach ($propertyTaxes as &$propertyTax) {
                if(array_key_exists('id', $propertyTax)){
                    $this->db->update('property_tax', $propertyTax, array('id' => $propertyTax['id']));
                    $this->load->model('memorizedTransactions_model');
                    $this->memorizedTransactions_model->taxes($propertyTax);
                }else{
                    return false;
                }
            //}
            return true;

        }

        function getAccounts(){
            $this->db->where('active', 1);
            $q = $this->db->get('accounts');
            if ($q->num_rows() > 0) {
                foreach (($q->result()) as &$row) {
                    $data[] = $row;
                }
                return $data;
            }
            return null;
        }
}