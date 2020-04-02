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
    public $data_test;
    public $total_record_data_test;
    public $mean_data_test;
    public $class_prediction;
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
                            $this->mean[$j][$k] = ++$total/$this->kelas_data[$k];
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
                echo "<td>".number_format($this->mean[$j][$i]*100,'2','.','')."%"."</td>";
            }
            echo "</tr>";
        }
        echo "</table>"; 
    }
    public function getTest(){
        $this->total_record_data_test = 0;
        $nama_file = "test.txt";
        $data = fopen($nama_file,"r");
        $file = fread($data,filesize($nama_file));
        $data_file = explode(PHP_EOL,$file);
        foreach ($data_file as $value) {
            $this->data_test[] = explode(",",$value);
            $this->total_record_data_test++;
        }
        echo "Data Testing : "."<br>";
        echo "<table border=\"1\">";
        foreach($this->data_test as $value){
        echo "<tr>";
           foreach ($value as $key){
                echo "<td>".$key."</td>";
           }
        echo "</tr>";
        }  
        echo "</table>";  
        $counter=0;
        $total_r=$this->total_record-1;
        for($i=0;$i<$this->total_record_data_test;$i++){
            for($j=0;$j<$this->jum_kelas;$j++){
                $total=1;
                for($k=0;$k<$this->total_atribut-1;$k++){
                    for($l=0;$l<count($this->save_atribut);$l++){
                        if($this->data_test[$i][$k] == $this->save_atribut[$l]){
                            $total*=$this->mean[$l][$j];
                            $temp = $this->kelas_data[$j]/$total_r;
                            $this->mean_data_test[$i][$j]=$total*$temp;
                        }
                    }
                }
            }
        }
    }
    public function print_test(){
        echo "Hasil : "."<br>";
        echo "<table border=\"1\">";
        echo "<tr>";
       foreach ($this->kelas as $key => $value) {
            echo "<td>".$value."</td>";
        } 
        echo "</tr>";
        for($i=0;$i<$this->total_record_data_test;$i++){
            echo "<tr>";
            for($j=0;$j<$this->jum_kelas;$j++){
                echo "<td>".number_format($this->mean_data_test[$i][$j]*100,'2','.','')."%"."</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        $save_class_prediction = array();
        for($i=0;$i<$this->total_record_data_test;$i++){
            $cek = 0;
            $max =  $this->mean_data_test[$i][0];
            for($j=0;$j<$this->jum_kelas;$j++){
                if($this->mean_data_test[$i][$j]>$max){
                    $max = $this->mean_data_test[$i][$j];
                    $cek++;
                }
            }
            $save_class_prediction[$i] = $this->kelas[$cek];
            $this->class_prediction[$i] = $max;
        }
       for($i=0;$i<$this->total_record_data_test;$i++){
           echo "<b>".$save_class_prediction[$i]."</b>". " ".$this->class_prediction[$i]."<br>";
       }
       $count=0; 
       $cek = array(array());
       $visit = array();
       for($i=0;$i<$this->total_record_data_test;$i++){
        $visit[$i]=0;
        for($j=0;$j<$this->jum_kelas;$j++){
            $cek[$i][$j]=1;
            if($j==0){
                $simpan[$i][$j] = $this->data_test[$i][5];
            }
            else{
                $simpan[$i][$j] = $save_class_prediction[$i];
            }
        }
       }
        $temp = $simpan[0][0];
        for($i=0;$i<$this->total_record_data_test;$i++){
            for($j=0;$j<$this->jum_kelas;$j++){
                if($simpan[$i][$j]!=$temp){
                    $cek[$i][$j]=0;
                }
            }
        }
        for($i=0;$i<$this->jum_kelas;$i++){
            $total=0;
            for($j=0;$j<$this->total_record_data_test;$j++){
                $counter=0;
                for($k=0;$k<$this->jum_kelas;$k++){
                    if($cek[$j][$k]!=$i){
                        $counter++;
                    }
                }
                if($counter==2){
                    $visit[$j]=1;
                    $simpan[$j][$counter]=++$total;
                }
                
            }
        }
        for($i=0;$i<$this->total_record_data_test;$i++){
            if($visit[$i] == 0){
                $count++;
            }
        }
        $nilai = array();  
        for($j=0;$j<$this->total_record_data_test;$j++){
            $max = 999;
            if($visit[$j]==1 && $this->kelas[0] == $simpan[$j][0] && $max > $simpan[$j][2]){
                $nilai[0] = $simpan[$j][2];
                $max = $nilai[0];
            } 
        }
        for($j=0;$j<$this->total_record_data_test;$j++){
            $max = 999;
            if($visit[$j]==1 && $this->kelas[1] == $simpan[$j][0] && $max > $simpan[$j][2]){
                $nilai[1] = $simpan[$j][2];
                $max = $nilai[1];
            } 
        }  
        $hasil = $nilai[0]+$nilai[1];
        $avg = $hasil/($hasil+$count);
        echo "<b>HASIL AKURASI ADALAH : <b>".($avg*100)."%";
    }
}
$nb = new NaiveBayes();
$nb->getDataTrain();
$nb->getKelas();
$nb->getAtribut();
$nb->getFirst();
$nb->hitung();
$nb->getTest();
$nb->print_test();
$conn = new Connection;
?>
