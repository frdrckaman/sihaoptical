<?php
class OverideData{
    private $_pdo;
    function __construct(){
        try {
            $this->_pdo = new PDO('mysql:host='.config::get('mysql/host').';dbname='.config::get('mysql/db'),config::get('mysql/username'),config::get('mysql/password'));
        }catch (PDOException $e){
            $e->getMessage();
        }
    }
    public function getNo($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $num = $query->rowCount();
        return $num;
    }
    public function getCount($table,$field,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value'");
        $num = $query->rowCount();
        return $num;
    }
    public function countData($table,$field,$value,$field1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1'");
        $num = $query->rowCount();
        return $num;
    }
    public function getCounted($table,$field,$value,$field1,$value1,$field2,$value2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $field2 = '$value2'");
        $num = $query->rowCount();
        return $num;
    }
    public function getData($table){
        $query = $this->_pdo->query("SELECT * FROM $table");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getDataOrderBy($table){
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY id DESC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
	public function record($table,$orderBy){
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $orderBy DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function range($table,$field1,$value1,$field2,$value2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 >= '$value1' AND $field2 <= '$value2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function rangeCount($table,$field1,$value1,$field2,$value2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 >= '$value1' AND $field2 <= '$value2'");
        $num = $query->rowCount();
        return $num;
    }
    public function getRange($table,$field1,$value1,$field2,$value2,$field3,$value3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 = '$value1' AND $field2 >= '$value2' AND $field3 <= '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRangeD($table,$param,$field1,$value1,$field2,$value2,$field3,$value3){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $field1 = $value1 AND $field2 >= '$value2' AND $field3 <= '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRange2($table,$field1,$value1,$field2,$value2,$field3,$value3,$field4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 = '$value1' AND $field2 = '$value2' AND $field3 >= '$value3' AND $field4 <= '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function countRange($table,$field1,$value1,$field2,$value2,$field3,$value3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 = $value1 AND $field2 >= '$value2' AND $field3 <= '$value3'");
        $num = $query->rowCount();
        return $num;
    }
    public function countRang($table,$field1,$value1,$field2,$value2,$field3,$value3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 = '$value1' AND $field2 >= '$value2' AND $field3 <= '$value3'");
        $num = $query->rowCount();
        return $num;
    }
    public function getSum($table,$field,$field1,$value1,$field2,$value2,$field3,$value3){
        $query = $this->_pdo->query("SELECT SUM($field) FROM $table WHERE $field1 = '$value1' AND $field2 >= '$value2' AND $field3 <= '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSum2($table,$field,$field1,$value1,$field2,$value2,$field3,$value3,$field4,$value4){
        $query = $this->_pdo->query("SELECT SUM($field) FROM $table WHERE $field1 = '$value1' AND $field2 = '$value2' AND $field3 >= '$value3' AND $field4 <= '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function countRange2($table,$field1,$value1,$field2,$value2,$field3,$value3,$field4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field1 = $value1 AND $field2 = $value2 AND $field3 >= '$value3' AND $field4 <= '$value4'");
        $num = $query->rowCount();
        return $num;
    }
    public function getDataDesc($table,$orderBy){
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $orderBy DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getDataAsc($table,$orderBy){
        $query = $this->_pdo->query("SELECT * FROM $table ORDER BY $orderBy ASC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function get($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getLike($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where LIKE '%$id%'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSort($table,$where,$id,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSort2($table,$where,$id,$where1,$id1,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'AND $where1 = '$id1' ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSort3($table,$where,$id,$where1,$id1,$where2,$id2,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id'AND $where1 = '$id1' AND $where2 = '$id2'  ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRecord($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY checkup_date DESC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getRecOrderBy($table,$where,$id){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' ORDER BY id DESC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getMedicine($table,$where){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where > 0");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNews($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNewsOr($table,$where,$id,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' OR $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNewsSort($table,$where,$id,$where2,$id2,$value){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' ORDER BY $value DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectOrN($table,$where,$id,$field1,$value1,$field2,$value2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where <> $id AND $field1 = $value1 OR $field2 = $value2");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectOr($table,$where,$id,$field1,$value1){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $field1 <> '$value1'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNewsNoRepeat($table,$param,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function rowCounted($table,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $rowCounted = $query->rowCount();
        return $rowCounted;
    }
    public function getLensPowerNoRepeat($table,$param,$where,$id,$where2){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$id' AND $where2  > 0");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData($table,$field,$value,$field1,$value1,$value2,$field2){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData4($table,$field,$value,$field1,$value1,$value2,$field2,$field3,$value3){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' AND $field3 = '$value3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function selectData5($table,$field,$value,$field1,$value1,$value2,$field2,$field3,$value3,$field4,$value4){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' AND $field3 = '$value3' AND $field4 = '$value4'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function delete($table,$field,$value){
        return $this->_pdo->query("DELETE FROM $table WHERE $field = $value");
    }

    public function getExamType($table,$field,$value,$field1,$value1,$value2,$field2){
        $query = $this->_pdo->query("SELECT DISTINCT exam_id FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function countValue($value){
        $query = $this->_pdo->query("SELECT * FROM $value");
        $result = $query->rowCount();
        return $result;
    }
    public function getDataTable($table,$value){
        $query = $this->_pdo->query("SELECT DISTINCT $value FROM $table ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function dataNoRepeat($table){
        $query = $this->_pdo->query("SELECT DISTINCT patient_id FROM $table ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNoRepeat($table,$param,$where,$id){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNoRepeatD3($table,$param,$param1,$param2,$where,$id){
        $query = $this->_pdo->query("SELECT DISTINCT $param,$param1,$param2 FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNoRepeat2($table,$param,$param1,$where,$id){
        $query = $this->_pdo->query("SELECT DISTINCT $param,$param1 FROM $table WHERE $where = '$id'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNoRepeat3($table,$param,$where1,$id1,$where2,$id2,$where3,$id3){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $where1 = '$id1' AND $where2 = '$id2' AND $where3 = '$id3'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getNoRepeat4($table,$param,$field,$value,$field1,$value1,$value2,$field2,$field3,$value3){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' AND $field3 = $value3");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSelectNoRepeat($table,$value,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT DISTINCT $value FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSelectNoRepeat2($table,$value,$value1,$where,$id,$where2,$id2){
        $query = $this->_pdo->query("SELECT DISTINCT $value,$value1 FROM $table WHERE $where = '$id' AND $where2 = '$id2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSelectDataNoRepeat($table,$param,$param2,$param3,$field,$value,$field1,$value1,$value2,$field2,$field3,$value3){
        $query = $this->_pdo->query("SELECT DISTINCT $param,$param2,$param3 FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' AND $field3 = $value3");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSelectDataNoRepeat1($table,$param,$field,$value,$field1,$value1,$value2,$field2,$field3,$value3){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2' AND $field3 = $value3");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getSelectData($table,$param,$field,$value,$field1,$value1,$value2,$field2){
        $query = $this->_pdo->query("SELECT DISTINCT $param FROM $table WHERE $field = '$value' AND $field1 = '$value1' AND $value2 = '$field2'");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getStudPosition($table,$where,$id,$where2,$id2,$where3,$id3,$where4,$id4,$where5,$id5){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3='$id3' AND $where4='$id4' AND $where5='$id5' ORDER BY score DESC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function checkRepeatExam($table,$where,$id,$where2,$id2,$where3,$id3,$where4,$id4,$where5,$id5,$where6,$id6){
        $query = $this->_pdo->query("SELECT * FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3='$id3' AND $where4='$id4' AND $where5='$id5' AND $where6 = '$id6' ORDER BY score DESC ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getStudAvg($table,$where,$id,$where2,$id2,$where3,$id3,$where4,$id4,$where5,$id5){
        $query = $this->_pdo->query("SELECT AVG(score) FROM $table WHERE $where = '$id' AND $where2 = '$id2' AND $where3='$id3' AND $where4='$id4' AND $where5='$id5' ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getDate($today,$date){
        $query = $this->_pdo->query("SELECT DATEDIFF('$date', '$today') AS endDate FROM contracts ");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}