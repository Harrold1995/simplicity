<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    

    public function getDcKey($array)
    {
        if (array_key_exists('debit', $array)) {
            return 'debit';
        }else {return 'credit';}
    }

    public function addHeader($header, $tt, $system = null)
    {
        if (isset($system)){
            $header['last_mod_by'] = 1;
        } else {
            $header['last_mod_by'] = $this->ion_auth->get_user_id();
        }
        //$header['last_mod_by'] = $this->ion_auth->get_user_id();
        $header['last_mod_date'] = date('Y-m-d H:i:s');
        $header['transaction_type'] = $tt;
        unset($header['password']);
        $this->db->insert('transaction_header', $header);
        return $this->db->insert_id();
    }

    public function populateHeader($header, $tt)
    {
        $header['last_mod_by'] = $this->ion_auth->get_user_id();
        $header['last_mod_date'] = date('Y-m-d H:i:s');
        $header['transaction_type'] = $tt;
        return $header;
    }

    public function insertHeader()
    {
        $this->db->insert('transaction_header', $header);
        return $this->db->insert_id();
    }

    public function updateHeader($header, $id)
    {
        $header['last_mod_by'] = $this->ion_auth->get_user_id();
        $header['last_mod_date'] = date('Y-m-d H:i:s');
        unset($header['password']);
        $this->db->update('transaction_header', $header, array('id' => $id));
    }

    public function insertTransId($transactions, $trans_id)
    {
       
        foreach ($transactions as &$transaction)
        { 
            $transaction['trans_id'] = $trans_id;
        }
       
        return $transactions;
    }

    public function removeEmpty($transactions, $trans_id)
    {
        $filled = [];
        $i = 0;
        foreach ($transactions as &$transaction) {
            $i++;
            foreach ($transaction as $value) {
                if ($value != "-1" AND $value != "" AND $value != '0.00') {
                    $transaction['line_number'] = $i;
                    $transaction['trans_id'] = $trans_id;
                    $filled[] = $transaction;
                    continue 2;
                }
            }
        }
        return $filled;
    }

    public function removeEmptyEdit($transactions, $id)
    {
        $filled = [];
        $i = 0;
        foreach ($transactions as &$transaction) {
            $i++;
            foreach ($transaction as $value) {
                if ($value != "-1" AND $value != "" AND $value != '0.00') {
                    $transaction['line_number'] = $i;
                    $transaction['trans_id'] = $id;
                    $filled[] = $transaction;
                    continue 2;
                }
            }
        }
        return $filled;
    }

    public function addDetailsDebit($transactions)
    {
        if (!empty($transactions)) {
            foreach ($transactions as &$transaction) {
                if ($transaction['debit'] > 0) {
                    $transaction['debit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = 0;
                } elseif ($transaction['debit'] < 0) {
                    $transaction['credit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = $transaction['credit'] * -1;
                    
                    $transaction['debit'] = 0;
                } else {
                    $transaction['debit'] = 0;
                    $transaction['credit'] = 0;
                }
            }

            $this->db->insert_batch('transactions', $transactions);
        }
    }

    public function addDetailsPerType($transactions, $type)
    {
        if (!empty($transactions)) {
            if($type === "normal"){
                foreach ($transactions as &$transaction) {
                    if ($transaction['debit'] > 0) {
                        $transaction['debit'] = $transaction['debit'];
                        $transaction['credit'] = 0;
                    } elseif ($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'];
                        $transaction['credit'] = $transaction['credit'] * -1;
                        
                        $transaction['debit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }

            if($type === "credit"){
                foreach ($transactions as &$transaction) {
                    $transaction['credit'] = $transaction['debit'];
                    //unset($transaction['debit']);
                    if ($transaction['credit'] > 0) {
                        $transaction['credit'] = $transaction['credit'];
                        $transaction['debit'] = 0;
                    } elseif ($transaction['credit'] < 0) {
                        $transaction['debit'] = $transaction['credit'];
                        $transaction['debit'] = $transaction['debit'] * -1;
                        
                        $transaction['credit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }
        }
        
            $this->db->insert_batch('transactions', $transactions);
        }
    

    public function addDetails($transactions)
    {
        if (!empty($transactions)) {
            foreach ($transactions as &$transaction) {
                $tt = $this->getDcKey($transaction);
                $ott = $tt === 'debit' ? 'credit' : 'debit';
                if ($transaction[$tt] > 0) {
                    //$transaction[$tt] = str_replace(',', '', $transaction[$tt]);
                    $transaction[$ott] = 0;
                } elseif ($transaction[$tt] < 0) {
                    $transaction[$ott] = $transaction[$tt];
                    $transaction[$ott] = $transaction[$ott] * -1;
                    $transaction[$tt] = 0;
                } else {
                    $transaction[$tt] = 0;
                    $transaction[$ott] = 0;
                }
            }

            $this->db->insert_batch('transactions', $transactions);
        }
    }

    public function addDetailsDebit2($transactions)
    {
        if (!empty($transactions)) {
            foreach ($transactions as &$transaction) {
                if ($transaction['amount'] > 0) {
                    $transaction['debit'] = str_replace(',', '', $transaction['amount']);
                    $transaction['credit'] = 0;
                } elseif ($transaction['amount'] < 0) {
                    $transaction['credit'] = $transaction['amount'] * -1;
                    $transaction['credit'] = str_replace(',', '', $transaction['credit']);
                    $transaction['debit'] = 0;
                } else {
                    $transaction['debit'] = 0;
                    $transaction['credit'] = 0;
                }
                unset($transaction['amount']);
            }

            $this->db->insert_batch('transactions', $transactions);
        }
    }

    public function editDetailsDebit($transactions)
    {
        $updateArray = [];
        $insertrArray = [];

        foreach ($transactions as &$transaction) {
            if (array_key_exists('id', $transaction)) {
                $updateArray[] = $transaction;
            } else {
                $insertArray[] = $transaction;
            }
        }

        if (!empty($updateArray)) {
            foreach ($updateArray as &$transaction) {
                if ($transaction['debit'] > 0) {
                    $transaction['debit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = 0;
                } elseif ($transaction['debit'] < 0) {
                    $transaction['credit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = $transaction['credit'] * -1;
                    
                    $transaction['debit'] = 0;
                } else {
                    $transaction['debit'] = 0;
                    $transaction['credit'] = 0;
                }
            }
            $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
        }
        if (!empty($insertArray)) {
            foreach ($insertArray as &$transaction) {
                //$transaction['trans_id'] = $trans_id;

                if ($transaction['debit'] > 0) {
                    $transaction['debit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = 0;
                } elseif ($transaction['debit'] < 0) {
                    $transaction['credit'] = str_replace(',', '', $transaction['debit']);
                    $transaction['credit'] = $transaction['credit'] * -1;
                   
                    $transaction['debit'] = 0;
                } else {
                    $transaction['debit'] = 0;
                    $transaction['credit'] = 0;
                }
            }
            $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
        }
    }

    public function editDetailsPerType($transactions, $type)
    {
        $updateArray = [];
        $insertrArray = [];

        foreach ($transactions as &$transaction) {
            if (array_key_exists('id', $transaction)) {
                $updateArray[] = $transaction;
            } else {
                $insertArray[] = $transaction;
            }
        }

        if (!empty($updateArray)) {
            if($type === "normal"){
                foreach ($updateArray as &$transaction) {
                    if ($transaction['debit'] > 0) {
                        //$transaction['debit'] = str_replace(',', '', $transaction['debit']);
                        $transaction['credit'] = 0;
                    } elseif ($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'];
                        $transaction['credit'] = $transaction['credit'] * -1;
                        
                        $transaction['debit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }

            if($type === "credit"){
                foreach ($updateArray as &$transaction) {
                    $transaction['credit'] = $transaction['debit'];
                    if ($transaction['credit'] > 0) {
                        //$transaction['credit'] = str_replace(',', '', $transaction['credit']);
                        $transaction['debit'] = 0;
                    } elseif ($transaction['credit'] < 0) {
                        $transaction['debit'] = $transaction['credit'];
                        $transaction['debit'] = $transaction['debit'] * -1;
                        
                        $transaction['credit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }
            
            $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
        }
        if (!empty($insertArray)) {
            if($type === "normal"){
                foreach ($insertArray as &$transaction) {
                    //$transaction['trans_id'] = $trans_id;
    
                    if ($transaction['debit'] > 0) {
                        //$transaction['debit'] = str_replace(',', '', $transaction['debit']);
                        $transaction['credit'] = 0;
                    } elseif ($transaction['debit'] < 0) {
                        $transaction['credit'] = $transaction['debit'];
                        $transaction['credit'] = $transaction['credit'] * -1;
                       
                        $transaction['debit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }
            
            if($type === "credit"){
                foreach ($insertArray as &$transaction) {
                    $transaction['credit'] = $transaction['debit'];
    
                    if ($transaction['credit'] > 0) {
                        //$transaction['credit'] = str_replace(',', '', $transaction['credit']);
                        $transaction['debit'] = 0;
                    } elseif ($transaction['credit'] < 0) {
                        $transaction['debit'] = $transaction['credit'];
                        $transaction['debit'] = $transaction['debit'] * -1;
                       
                        $transaction['credit'] = 0;
                    } else {
                        $transaction['debit'] = 0;
                        $transaction['credit'] = 0;
                    }
                }
            }
            $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
        }
    }

    public function editDetails($transactions)
    {
        $updateArray = [];
        $insertrArray = [];

        foreach ($transactions as &$transaction) {
            if (array_key_exists('id', $transaction)) {
                $updateArray[] = $transaction;
            } else {
                $insertArray[] = $transaction;
            }
        }

        if (!empty($updateArray)) {
            foreach ($updateArray as &$transaction) {
                $tt = $this->getDcKey($transaction);
                $ott = ($tt === 'debit') ? 'credit' : 'debit';
                if ($transaction[$tt] > 0) {
                    //$transaction[$tt] = str_replace(',', '', $transaction[$tt]);
                    $transaction[$ott] = 0;
                } elseif ($transaction[$tt] < 0) {
                    $transaction[$ott] = $transaction[$tt];
                    $transaction[$ott] = $transaction[$ott] * -1;
                    $transaction[$tt] = 0;
                } else {
                    $transaction[$tt] = 0;
                    $transaction[$ott] = 0;
                }
            }
            $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
        }
        if (!empty($insertArray)) {
            foreach ($insertArray as &$transaction) {
                $tt = $this->getDcKey($transaction);
                $ott = ($tt === 'debit') ? 'credit' : 'debit';

                if ($transaction[$tt] > 0) {
                    //$transaction[$tt] = str_replace(',', '', $transaction[$tt]);
                    $transaction[$ott] = 0;
                } elseif ($transaction[$tt] < 0) {
                    $transaction[$ott] = $transaction[$tt];
                    $transaction[$ott] = $transaction[$ott] * -1;
                    
                    $transaction[$tt] = 0;
                } else {
                    $transaction[$tt] = 0;
                    $transaction[$ott] = 0;
                }
            }
            $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
        }
    }
    // public function editDetailsDebit2($transactions)
    // {
    //     $updateArray = [];
    //     $insertrArray = [];

    //     foreach ($transactions as &$transaction) {
    //         if (array_key_exists('id', $transaction)) {
    //             $updateArray[] = $transaction;
    //         } else {
    //             $insertArray[] = $transaction;
    //         }
    //     }

    //     if (!empty($updateArray)) {
    //         foreach ($updateArray as &$transaction) {
    //             if ($transaction['amount'] > 0) {
    //                 $transaction['debit'] = str_replace(',', '', $transaction['amount']);
    //                 $transaction['credit'] = 0;
    //             } elseif ($transaction['amount'] < 0) {
    //                 $transaction['credit'] = $transaction['amount'] * -1;
    //                 $transaction['credit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //             unset($transaction['amount']);
    //         }
    //         $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
    //     }
    //     if (!empty($insertArray)) {
    //         foreach ($insertArray as &$transaction) {
    //             //$transaction['trans_id'] = $trans_id;

    //             if ($transaction['amount'] > 0) {
    //                 $transaction['debit'] = str_replace(',', '', $transaction['amount']);
    //                 $transaction['credit'] = 0;
    //             } elseif ($transaction['amount'] < 0) {
    //                 $transaction['credit'] = $transaction['amount'] * -1;
    //                 $transaction['credit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //             unset($transaction['amount']);
    //         }
    //         $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
    //     }
    // }

    // public function addDetailsCredit($transactions)
    // {
    //     foreach ($transactions as &$transaction) {
    //         if ($transaction['credit'] > 0) {
    //             $transaction['credit'] = str_replace(',', '', $transaction['credit']);
    //             $transaction['debit'] = 0;
    //         } elseif ($transaction['credit'] < 0) {
    //             $transaction['debit'] = str_replace(',', '', $transaction['credit']);
    //             $transaction['debit'] = $transaction['debit'] * -1;
    //             $transaction['credit'] = 0;
    //         } else {
    //             $transaction['debit'] = 0;
    //             $transaction['credit'] = 0;
    //         }
    //     }
    //     if (!empty($transactions)) {
    //         $this->db->insert_batch('transactions', $transactions);
    //     }
    // }

    // public function addDetailsCredit2($transactions)
    // {
    //     foreach ($transactions as &$transaction) {
    //         if ($transaction['amount'] > 0) {
    //             $transaction['credit'] = str_replace(',', '', $transaction['amount']);
    //             $transaction['debit'] = 0;
    //         } elseif ($transaction['amount'] < 0) {
    //             $transaction['debit'] = $transaction['amount'] * -1;
    //             $transaction['debit'] = str_replace(',', '', $transaction['debit']);
    //             $transaction['credit'] = 0;
    //         } else {
    //             $transaction['debit'] = 0;
    //             $transaction['credit'] = 0;
    //         }
    //         unset($transaction['amount']);
    //     }
    //     if (!empty($transactions)) {
    //         $this->db->insert_batch('transactions', $transactions);
    //     }
    // }

    // public function editDetailsCredit($transactions)
    // {
    //     $updateArray = [];
    //     $insertrArray = [];

    //     foreach ($transactions as &$transaction) {
    //         if (array_key_exists('id', $transaction)) {
    //             $updateArray[] = $transaction;
    //         } else {
    //             $insertArray[] = $transaction;
    //         }
    //     }

    //     if (!empty($updateArray)) {
    //         foreach ($updateArray as &$transaction) {
    //             if ($transaction['credit'] > 0) {
    //                 $transaction['credit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = 0;
    //             } elseif ($transaction['credit'] < 0) {
    //                 $transaction['debit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = $transaction['debit'] * -1;
    //                 $transaction['credit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //         }
    //         $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
    //     }

    //     if (!empty($insertArray)) {
    //         foreach ($insertArray as &$transaction) {
    //             //$transaction['trans_id'] = $trans_id;

    //             if ($transaction['credit'] > 0) {
    //                 $transaction['credit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = 0;
    //             } elseif ($transaction['credit'] < 0) {
    //                 $transaction['debit'] = str_replace(',', '', $transaction['credit']);
    //                 $transaction['debit'] = $transaction['debit'] * -1;
    //                 $transaction['credit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //         }
    //         $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
    //     }
    // }

    // public function editDetailsCredit2($transactions)
    // {
    //     $updateArray = [];
    //     $insertrArray = [];

    //     foreach ($transactions as &$transaction) {
    //         if (array_key_exists('id', $transaction)) {
    //             $updateArray[] = $transaction;
    //         } else {
    //             $insertArray[] = $transaction;
    //         }
    //     }

    //     if (!empty($updateArray)) {
    //         foreach ($updateArray as &$transaction) {
    //             if ($transaction['amount'] > 0) {
    //                 $transaction['credit'] = str_replace(',', '', $transaction['amount']);
    //                 $transaction['debit'] = 0;
    //             } elseif ($transaction['amount'] < 0) {
    //                 $transaction['debit'] = $transaction['amount'] * -1;
    //                 $transaction['debit'] = str_replace(',', '', $transaction['debit']);
    //                 $transaction['credit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //             unset($transaction['amount']);
    //         }
    //         $this->db->update_batch('transactions', $updateArray, 'id');//in bills didn't insert batch check why
    //     }

    //     if (!empty($insertArray)) {
    //         foreach ($insertArray as &$transaction) {
    //             //$transaction['trans_id'] = $trans_id;

    //             if ($transaction['amount'] > 0) {
    //                 $transaction['credit'] = str_replace(',', '', $transaction['amount']);
    //                 $transaction['debit'] = 0;
    //             } elseif ($transaction['amount'] < 0) {
    //                 $transaction['debit'] = $transaction['amount'] * -1;
    //                 $transaction['debit'] = str_replace(',', '', $transaction['debit']);
    //                 $transaction['credit'] = 0;
    //             } else {
    //                 $transaction['debit'] = 0;
    //                 $transaction['credit'] = 0;
    //             }
    //             unset($transaction['amount']);
    //         }
    //         $this->db->insert_batch('transactions', $insertArray);  //in bills didn't insert batch check why
    //     }
    // }

    public function deleteLines($deletes)
    {
        if (!empty($deletes)) {
            // $this->db->where_in('id', $deletes); //array_values($deletes)probably don't need whole fancy function just pass array
            // $this->db->delete('transactions');

            $ids = implode(",",$deletes);

            $this->db->trans_complete();

            $sql ='DELETE t, ap
            FROM transactions t
            LEFT JOIN applied_payments ap ON t.id = ap.transaction_id_a OR t.id = ap.transaction_id_b
            WHERE t.id IN(' . $ids . ') '; //for sql injection
            $this->db->query($sql);

            $this->db->trans_complete();

        // if ($this->db->trans_status() === FALSE)
        // {
        //     return false;
        // }
        }
    }

    public function applyPaymentsAdd($applied_payments, $transaction_id_a, $system = null)
    {
        if (!empty($applied_payments)) {
            foreach ($applied_payments as &$applied_payment) {
                unset($applied_payment['applied']);
                $applied_payment['amount'] = str_replace(',', '', $applied_payment['amount']);
                $applied_payment['transaction_id_a'] = $transaction_id_a;
                if (isset($system)){$applied_payment['created_by'] = 1;} else {$applied_payment['created_by'] = $this->ion_auth->get_user_id();}
                //$applied_payment['created_by'] = $this->ion_auth->get_user_id();
                $applied_payment['date_modified'] = date("Y-m-d H:i:s");
            }

            $this->db->insert_batch('applied_payments', $applied_payments);
        }
    }

    public function checkItems($deletes, $delete)//array of arrays or objects with $type_ids and $type_item_ids
    {
        $inTransaction = [];
        $memorizedTransactions = [];

        foreach ($deletes as $type_id => $type_item_ids) {
           

                foreach ($type_item_ids as $type_item_id) {
                    if ($type_id == 13){

                        $this->db->select('t.type_item_id, t.type_id');
                        $this->db->from('transactions t');
                        $this->db->join('leases_profiles lp', 'lp.lease_id = t.lease_id AND lp.profile_id = t.profile_id');
                        $this->db->where('lp.id', $type_item_id);
                        $q = $this->db->get();
                    } else {
                        $this->db->select('t.type_item_id, t.type_id');
                        $this->db->from('transactions t');
                        $this->db->where('t.type_id', $type_id);
                        $this->db->where('t.type_item_id', $type_item_id);
                        $q = $this->db->get();
                    }
    
                    if ($q->num_rows() > 0) {
                        $inTransaction[] = $q->row();
                    } else {
                        $memorizedTransactions[] = $this->checkMemorizedTransactions($type_id, $type_item_id, $delete);
                        if ($delete) $this->deleteItems($type_id, $type_item_id);
                    }
                }

           
            
        }

        //$response = new stdClass();

        if (($delete == null) && empty($inTransaction) && empty($memorizedTransactions[0])) {
            $response = 'Are you sure you want to delete';
            //$response->status = 0;
            return $response;
        }

        if (($delete == null) && !empty($inTransaction) && !empty($memorizedTransactions[0])) {
            $response = 'Some item(s) that you are trying to delete are linked to transactions and can\'t be deleted, other(s) are linked to memorized transactions. Would you like to delete those?';
            //$response->status = 0;
            return $response;
        }

        if (($delete == null) && empty($inTransaction) && !empty($memorizedTransactions[0])) {
            $response = 'Items are linked to memorized transactions. Would you like to delete them?';
            //$response->status = 0;
            return $response;
        }

        if (($delete == null) && !empty($inTransaction) && empty($memorizedTransactions[0])) {
            $response = 'Items you are trying to delete are linked to transactions and can\'t be deleted';
            //$response->status = 1;
            return $response;
        }
    }

    public function checkMemorizedTransactions($type_id, $type_item_id, $delete)
    {
        //$memorizedTransaction; 

        $this->db->select('mt.id AS mt_id');
        $this->db->from('memorized_transactions mt');
        //$this->db->join('memorized_transactions mt', 'p.id = t.profile_id', 'left');
        //$this->db->join('leases_profiles lp', 'p.id = lp.profile_id', 'left');
        $this->db->where('mt.type_id', $type_id);
        $this->db->where('mt.type_item_id', $type_item_id);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            if ($delete == null) {
                return $q->row()->mt_id;
            }
            //else{$memorizedTransactions[] = [];}

            if ($delete) {
                //$this->deletePropertyDetails($type_id, $type_item_id);
                $mt_ids = array_column($q->result_array(), 'mt_id'); 
                $this->deleteMemorized($mt_ids);
            }
        } else {
            return null;
        }
    }

    public function deleteItems($type_id, $type_item_Id)
    {
        $this->db->select('table');
        $this->db->from('document_types');
        $this->db->where('id', $type_id);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            $table = $q->row()->table;
        }
        $this->db->where('id', $type_item_Id);
        $this->db->delete($table);
    }

    public function deleteMemorized($mt_id)
    {
        $this->db->where_in('id', $mt_id);
        $this->db->delete('memorized_transactions');
    }
}
