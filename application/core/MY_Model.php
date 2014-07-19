<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

abstract class MY_Model extends CI_Model
{
    private $_loaded = false;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    abstract function tableName();

    public function getFields()
    {
        $fields = $this->db->list_fields($this->tableName());
        return array_diff($fields, array('id'));
    }

    public function load($id)
    {
        $query = $this->db->get_where($this->tableName(), array('id' => $id), 1, null);
        $data = $query->row_array();
        if ($data) {
            $model = $this->_loaded ? (clone $this) : $this;
            $this->_loaded = true;
            return $model->setData($data);
        } else {
            return false;
        }
    }

    public function setData($data)
    {
        foreach ($data as $key => $value) {
            if (!property_exists(get_class($this), $key)) continue;
            $this->$key = $value;
        }

        return $this;
    }

    public function save()
    {
        $data = array();
        foreach ($this->getFields() as $field) {
            $data = array_merge($data, array($field => $this->$field));
        }

        if ($this->id) {
            $this->db->update($this->tableName(), $data, array('id' => $this->id));
        } else {
            $this->db->insert($this->tableName(), $data);
        }

        return $this;
    }

    public function delete($id = null)
    {
        if ($id) {
            $this->db->delete($this->tableName(), array('id' => $id));
        } elseif ($this->id) {
            $this->db->delete($this->tableName(), array('id' => $this->id));

        }
    }

    public function listAll($fields = "", $where = "", $order = null)
    {
        if (is_array($fields) && count($fields) > 0) {
            $this->db->select(implode(',', $fields));
        } else if (is_string($fields)) {
            $fields = trim($fields);
            if (!empty($fields)) {
                $this->db->select($fields);
            }
        }
        if (is_array($where) && count($where) > 0) {
            $this->db->where($where);
        } else if (is_string($where)) {
            $where = trim($where);
            if (!empty($where)) {
                $this->db->where($where);
            }
        }
        if ($order) {
            $this->db->order_by($order);
        }
        $query = $this->db->get($this->tableName());
        return $query->result();
    }

    public function record_count($where = array())
    {
        if (count($where) == 0)
            return $this->db->count_all($this->tableName());

        $this->db->where($where);
        return $this->db->count_all_results($this->tableName());
    }
}
