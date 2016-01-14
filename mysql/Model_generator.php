<?php
/*
** Unified CodeIgniter Model Wrapper
** AUTHOR: Maxim Titovich
** 2015
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_generator extends CI_Controller {

    public function index()
    {
        $query = $this->db->query("select table_name from information_schema.tables where table_schema = ".$this->db->escape($this->db->database));
        $tables = $query->result();
        echo "**Unified CodeIgniter Model Wrapper**<br/>";
        echo "**AUTHOR: Maxim Titovich**<br/>";
        echo "**2015**<br/><br/>";
        echo "---------------------------------------------<br/>";
        echo "Total tables found: ".count($tables)."<br/>";
        echo "---------------------------------------------<br/>";
        foreach($tables as $table)
        {
            $class_name = ucfirst(strtolower($table->table_name)).'_model';
            $data = "<?php\n";
            $data .= "/*\n";
            $data .= "** Unified CodeIgniter Model Wrapper\n";
            $data .= "** AUTHOR: Maxim Titovich\n";
            $data .= "** 2015\n";
            $data .= "*/\n\n";
            $data .= 'if ( ! defined("BASEPATH")) exit("No direct script access allowed");'."\n\n";
            $data .= 'class '.$class_name.' extends Model_wrapper'."\n{\n\n";
            $query = $this->db->query("SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ".$this->db->escape($this->db->database)." AND TABLE_NAME = ".$this->db->escape($table->table_name));
            $columns = $query->result();
            foreach($columns as $column)
                $data .= "\t".'var $'.$column->column_name.';'."\n";
            $query = $this->db->query("select column_name, constraint_name, referenced_table_name, referenced_column_name from INFORMATION_SCHEMA.KEY_COLUMN_USAGE where TABLE_SCHEMA = ".$this->db->escape($this->db->database)." and TABLE_NAME = ".$this->db->escape($table->table_name)." and referenced_column_name is not NULL;");
            $constraints = $query->result();
            foreach($constraints as $constraint)
            {
                $referenced_class = strtolower($constraint->referenced_table_name)."_model";
                $function = "\n\t".'public function load_'.$constraint->referenced_table_name.'()'."\n\t{\n";
                $function .= "\t\t".'$this->CI->load->model("'.$referenced_class.'");'."\n";
                $function .= "\t\t".'return $this->CI->'.$referenced_class.'->getById($this->'.$constraint->column_name.');'."\n";
                $function .= "\t}\n";
                $data .= $function;
            }
            $data .= "\n}";
            $data = iconv('', 'UTF-8', $data);
            file_put_contents(APPPATH.'/models/'.$class_name.'.php', $data);
            echo "Model ".$class_name." successfully generated!<br/>";
        }
    }
}