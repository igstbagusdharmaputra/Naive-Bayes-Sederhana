<?php
require 'connection.php';
class NaiveBayes{
    public $file_data;
    public $data_train;
    public $jum_kelas=0;
    public $kelas_data;
    public $kelas;
    public $total_record;
    public $total_atribut;
    public $atribut;
    public $save_atribut;
    public $jum_atribut; // jumlah atribut setiap class
    public $total_data_atribut;
    public $mean;
    public function __constructor(){
        
    }
    public function getDataTrain(){
        $this->total_record = 0;
        $nama_file = "datatrain.txt";
        $data = fopen($nama_file,"r");
        $file = fread($data,filesize($nama_file));
        $data_file = explode(PHP_EOL,$file);
        foreach ($data_file as $value) {
            $this->data_train[] = explode(",",$value);
            $this->total_record++;
        }
        echo "<table border=\"1\">";
        $this->total_atribut = sizeof($this->data_train[0]);
        foreach($this->data_train as $value){
        echo "<tr>";
           foreach ($value as $key){
                echo "<td>".$key."</td>";
           }
        echo "</tr>";
        }  
        echo "</table>"; 
    }
    public function getKelas(){
        $count=0;
        $data_array = array();
        $data_array = $this->data_train;
        $total_a = $this->total_atribut-1;
        $total = $this->total_record-1;
        for($i=1;$i<$total;$i++){
            for($j=$i+1;$j<$total;$j++){   
                if($this->data_train[$i][$total_a] == $this->data_train[$j][$total_a]){
                    break;
                }
            }
            if($j==$total){
                $this->kelas[$count++] = $this->data_train[$i][$total_a];
                $this->jum_kelas++;
            }
        }
        for($i=0;$i<$this->jum_kelas;$i++){
            $total_data=0;
            for($j=1;$j<=$total;$j++){
                if($this->kelas[$i] == $this->data_train[$j][$total_a]){
                    $this->kelas_data[$i] = ++$total_data;
                }
            }
        }
        
    }
    public function getAtribut(){
        $this->atribut = array();
        for($i=0;$i<$this->total_atribut-1;$i++){
            for($j=1;$j<=$this->total_record-1;$j++){
                array_push($this->atribut,$this->data_train[$j][$i]);
            }
        }
        $data_atribut = array_unique($this->atribut);
        $i=0;
        $count=0;
        foreach ($data_atribut as $value) {
            $this->save_atribut[$i++] = $value;
        }
        for($i=0;$i<sizeof($this->save_atribut);$i++){
            for($j=0;$j<=$this->total_atribut-1;$j++){
                for($k=1;$k<=$this->total_record-1;$k++){
                    if($this->save_atribut[$i] == $this->data_train[$k][$j]){
                        $this->total_data_atribut[$i] = ++$count;
                    }
                }
            }   
            $count=0;
        }
        $total = $this->total_record-1;
        $count = 0;
        $temp = array(array());
        for($i=0;$i<$this->total_atribut-1;$i++){
            $cek=0;
            for($j=0;$j<sizeof($this->save_atribut);$j++){
                for($k=1;$k<=$total;$k++)
                {
                    if($this->save_atribut[$j] == $this->data_train[$k][$i]){
                        $temp[$i][$cek++]=$this->data_train[$k][$i];
                    }
                }
            }
        }
        echo "Atribut dan Total: ";  
        foreach ($this->save_atribut as $key => $value) {
            echo " | ".$value." : ".$this->total_data_atribut[$key];
        }
        
        echo "<br>";
        
        for($i=0;$i<$this->total_atribut-1;$i++){
            $count =0;
            for($j=0;$j<$cek;$j++){
                for($k=$j+1;$k<$cek;$k++){
                    if($temp[$i][$j]==$temp[$i][$k]){
                        break;
                    }
                }
                if($k==$cek){
                    $this->jum_atribut[$i] = ++$count;
                }
            }
        } 
    }
    public function getFirst(){
        echo "Class dan Total: ";
        foreach ($this->kelas as $key => $value) {
            echo " ".$value." "."(".$this->kelas_data[$key].")";
        } 
        echo "<br>";
        echo "Class dan Total: ";
        for($i=0;$i<$this->total_atribut-1;$i++){
            echo " ".$this->data_train[0][$i]." "."(".$this->jum_atribut[$i].")";
        }
    }
    public function hitung(){
       $x = 1;
       $y = 0;
       $total = 0;
       echo "<br>";
       echo "Model Naive Bayes : ";
       echo "<br>";
       for($i=0;$i<$this->jum_kelas;$i++){
            for($j=0;$j<count($this->save_atribut);$j++){
                $this->mean[$j][$i] =0;
            }
        }
       for($i=0;$i<$this->total_atribut-1;$i++){
           for($j=0;$j<count($this->save_atribut);$j++){
               for($k=0;$k<$this->jum_kelas;$k++){
                    $total=0;
                    for($count=1;$count<=$this->total_record-1;$count++){
                        if($this->save_atribut[$j]== $this->data_train[$count][$i] && $this->kelas[$k] ==  $this->data_train[$count][$this->total_atribut-1]){
                            $this->mean[$j][$k] = ++$total/$this->kelas_data[$k]*100;
                        }
                    }
                } 
            }
        }
       echo "<br>";
       echo "<table border=\"1\">";
       echo "<tr>";
       echo "<td>"."Atribut"."</td>";
       foreach ($this->kelas as $key => $value) {
            echo "<td>".$value."</td>";
        } 
        echo "</tr>";
        
        for($j=0;$j<count($this->save_atribut);$j++){
            echo "<tr>";
            echo "<td>".$this->save_atribut[$j]."</td>";
            for($i=0;$i<$this->jum_kelas;$i++){
                echo "<td>".number_format($this->mean[$j][$i])."%"."</td>";
            }
            echo "</tr>";
        }
        echo "</table>"; 
        // for($k=0;$k<$this->jum_kelas;$k++){
    }  
}
$nb = new NaiveBayes();
$nb->getDataTrain();
$nb->getKelas();
$nb->getAtribut();
$nb->getFirst();
$nb->hitung();
$conn = new Connection;

?>
