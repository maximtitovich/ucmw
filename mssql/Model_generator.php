<?php
/*
** Unified CodeIgniter Model Wrapper
** AUTHOR: Maxim Titovich
** 2015-2016
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Model_generator extends CI_Controller {

    public function index()
    {
        $tables = $this->db->list_tables();
        echo "**Unified CodeIgniter Model Wrapper**<br/>";
        echo "**AUTHOR: Maxim Titovich**<br/>";
        echo "**2015-2016**<br/><br/>";
        echo "---------------------------------------------<br/>";
        echo "Total tables found: ".count($tables)."<br/>";
        echo "---------------------------------------------<br/>";
        foreach($tables as $table)
        {
            $class_name = ucfirst(strtolower($table)).'_model';
            $data = "<?php\n";
            $data .= "/*\n";
            $data .= "** Unified CodeIgniter Model Wrapper\n";
            $data .= "** AUTHOR: Maxim Titovich\n";
            $data .= "** 2015-2016\n";
            $data .= "*/\n\n";
            $data .= 'if ( ! defined("BASEPATH")) exit("No direct script access allowed");'."\n\n";
            $data .= 'class '.$class_name.' extends MY_Model'."\n{\n\n";
            $columns = $this->db->list_fields($table);
            foreach($columns as $column)
                $data .= "\t" . 'public $' . $column . ';' . "\n";
            $query = $this->db->query("SELECT *
                                          FROM
                                            (
                                            SELECT
                                            T1.constraint_name ConstraintName,
                                            T2.COLUMN_NAME ColumnName,
                                            T2.TABLE_NAME TableName,
                                            T3.TABLE_NAME RefTableName,
                                            T3.COLUMN_NAME RefColumnName
                                            FROM
                                            INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS T1
                                            INNER JOIN
                                            INFORMATION_SCHEMA.KEY_COLUMN_USAGE T2
                                            ON T1.CONSTRAINT_NAME = T2.CONSTRAINT_NAME
                                            INNER JOIN
                                            INFORMATION_SCHEMA.KEY_COLUMN_USAGE T3
                                            ON T1.UNIQUE_CONSTRAINT_NAME = T3.CONSTRAINT_NAME
                                            AND T2.ORDINAL_POSITION = T3.ORDINAL_POSITION) A
                                            WHERE A.TableName = ".$this->db->escape($table));
            $constraints = $query->result();
            foreach($constraints as $constraint)
            {
                $referenced_class = strtolower($constraint->RefTableName)."_model";
                $function = "\n\t".'public function load_'.$constraint->RefTableName.'()'."\n\t{\n";
                $function .= "\t\t".'$this->CI->load->model("'.$referenced_class.'");'."\n";
                $function .= "\t\t".'return $this->CI->'.$referenced_class.'->getById($this->'.$constraint->ColumnName.');'."\n";
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