<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_generic extends CI_Model
{

    /************************************************************/
    public function _count($param)
    {
        $table = $param['table'];
        return $this->db->get($table)->num_rows();
    }
    /************************************************************/
    /************************************************************/
    public function _del($param)
    {
        $table = $param['table'];
        // $this->db->where($table.'_id', $param[$table.'_id']);
        $this->db->delete($table);
    }
    /************************************************************/
    public function _cek($param)
    {
        $table = $param['table'];
        // $this->db->where($table.'_id', $param[$table.'_id']);
        return $this->db->get($table)->num_rows();
    }
    /************************************************************/
    // public function _set($param){
    //   $table = $param['table'];
    //   $cek = $this->_cek($param);
    //   unset($param['table']);
    //   if($cek){
    //     // $this->db->where($table.'_id', $param[$table.'_id']);
    //     $this->db->update($table,$param);
    //   }else{
    //     $this->db->insert($table, $param);
    //   }
    // }
    /************************************************************/
    /************************************************************/
    /************************************************************/
    /************************************************************/
    public function _get_array($param)
    {
        $table = $param['table'];
        unset($param['table']);
        $x = $this->db->get($table)->result_array();
        if (false == empty($x)) {
            return $x;
        } else {
            return array();
        }
    }
    /************************************************************/
    /************************************************************/
    public function _get($param, $limit = null, $offset = null)
    {

        $table = $param['table'];
        unset($param['table']);
        // if (isset($param[$table . '_id'])) {
        //   $this->db->where($table . '_id', $param[$table . '_id']);
        // }

        $x = $this->db->get($table, $limit, $offset)->result();
        if (false == empty($x)) {
            $nomor = 1;
            foreach ($x as $key => $value) {
                $value->nomor      = $nomor++;
                $value->nomor_urut = $value->nomor + $offset;
            }
            return $x;
        } else {
            return array();
        }
    }
    /************************************************************/
    /************************************************************/
    public function _insert($param)
    {
        $table = $param['table'];
        unset($param['table']);
        $this->db->insert($table, $param);
    }
    /************************************************************/
    /************************************************************/
    public function _update($param)
    {
        $table = $param['table'];
        unset($param['table']);
        $this->db->update($table, $param);
    }
    /************************************************************/
    /************************************************************/
    public function _action($param = null)
    {
        if ($param) {
            $action    = $param['action'];
            $entity_id = $param['entity_id'];
            unset($param['action']);
            unset($param['entity_id']);

            switch ($action) {
                case 'add':
                case 'create':
                    $this->_insert($param);
                    break;
                case 'edit':
                case 'update':
                    $this->db->where($entity_id, $param[$entity_id]);
                    $_cek = $this->_cek($param);
                    // opn($_cek);exit();
                    if ($_cek) {
                        $this->db->where($entity_id, $param[$entity_id]);
                        $this->_update($param);
                    }
                    break;
                case 'delete':
                    $this->db->where($entity_id, $param[$entity_id]);
                    $_cek = $this->_cek($param);
                    if ($_cek) {
                        $this->db->where($entity_id, $param[$entity_id]);
                        $this->_del($param);
                    }
                    break;
            }
        }
    }
    /************************************************************/
}
