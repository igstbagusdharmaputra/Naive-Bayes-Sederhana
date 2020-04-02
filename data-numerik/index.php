<?php

class NaiveBayes{
    public $data_train;
    public $data_test;
    public $kelas;
    public $jumlah_kelas;
    public $total_record;
    public $total_atribut;
    public $mean;
    public $sdeviasi;
    public $model;
    public $hasil;
    public $hasil_kelas;
    public function __constructor(){
        
    }
  
    public function getDataTrain(){
        $this->total_record=0;
        $nama_file = "datatrain.txt";
        $data = fopen($nama_file,"r");
        $file = fread($data,filesize($nama_file));
        $data_file = explode(PHP_EOL,$file);
        foreach ($data_file as $value) {
            $this->data_train[] = preg_split('/(,|;)/', $value,-1, PREG_SPLIT_NO_EMPTY);
            $this->total_record++;
        }
        $this->total_atribut = sizeof($this->data_train[0]);
        // echo "<table border=\"1\">";
        // foreach($this->data_train as $value){
        // echo "<tr>";
        //    foreach ($value as $key){
        //         echo "<td>".$key."</td>";
        //    }
        // echo "</tr>";
        // }  
        // echo "</table>"; 
        // echo "Total Record : ".$this->total_record;
    }
    public function getDataTest(){
        $nama_file = "datatesting.txt";
        $data = fopen($nama_file,"r");
        $file = fread($data,filesize($nama_file));
        $data_file = explode(PHP_EOL,$file);
        foreach ($data_file as $value) {
            $this->data_test[] = preg_split('/(,|;)/', $value,-1, PREG_SPLIT_NO_EMPTY);
        }
        // echo "<table border=\"1\">";
        // foreach($this->data_test as $value){
        // echo "<tr>";
        //    foreach ($value as $key){
        //         echo "<td>".$key."</td>";
        //    }
        // echo "</tr>";
        // }  
        // echo "</table>"; 
    }
    public function getKelas(){
        $this->kelas[0] = $this->data_train[0][4];
        $total_kelas=1;
        $this->jumlah_kelas[0] = $total_kelas++;
        $j=0;$k=0;
        for($i=1; $i<$this->total_record;$i++){
            if($this->data_train[$i][$this->total_atribut-1]!=$this->data_train[$i-1][$this->total_atribut-1]){
                $this->kelas[++$j] = $this->data_train[$i][$this->total_atribut-1];
                $total_kelas=1;
            }
            $this->jumlah_kelas[$j] = $total_kelas++;
        }
    }
    public function cetak(){
        echo "Kelas dan Jumlah : "."\n";
        for($i=0;$i<sizeof($this->kelas);$i++){
            echo $this->kelas[$i]." "."(".$this->jumlah_kelas[$i].")"." ";
        }
    }
    public function mean(){
        $total=0;
        $y=0;
        for($i=0;$i<sizeof($this->kelas);$i++){
            for($j=0;$j<$this->total_atribut-1;$j++){
                for($k=0;$k<$this->jumlah_kelas[$i];$k++){
                    $total+=$this->data_train[$k+$y][$j];
                }
                $this->mean[$i][$j]= $total/$this->jumlah_kelas[$i];
                $total=0;
            }
            $y+=$this->jumlah_kelas[$i];
        }
    }
    public function hitung_deviasi(){
        $total = 0;
        $y=0;
        for($i=0;$i<sizeof($this->kelas);$i++){
            for($j=0;$j<$this->total_atribut-1;$j++){
                for($k=0;$k<$this->jumlah_kelas[$i];$k++){
                    $total+=pow(($this->data_train[$k+$y][$j]-$this->mean[$i][$j]),2);
                    
                }
                $this->sdeviasi[$i][$j] = sqrt($total/($this->jumlah_kelas[$i]-1));
                $total=0;
            }
            $y+=$this->jumlah_kelas[$i];
        }
    }
   
    public function probabilitas(){
        $total=1;
        for($i=0;$i<sizeof($this->data_test);$i++){
            for($j=0;$j<sizeof($this->kelas);$j++){
                for($k=0;$k<$this->total_atribut-1;$k++){
                    //rumus
                    $total*=(1/sqrt(2*3.14*$this->sdeviasi[$j][$k])*exp(-(($this->data_test[$i][$k]-$this->mean[$j][$k])^2)/($this->sdeviasi[$j][$k]^2)));
                }
                $this->model[$i][$j] = $total;
                $total=1;
            }
        }
        echo sizeof($this->kelas);
    }
    
    public function hasil_mean_sdev_model(){
        $class = array('Iris-setosa','Iris-versicolor','Iris-virginica');
        echo "<h2>"."Rata-rata dan Standar Deviasi"."</h2>";
        echo "<table border=\"1\" cellspacing=\"0\">";
        echo "<thead>";
            echo "<tr>";
                echo "<td>"."Class"."</td>";
                echo "<th>"."sepal-length"."</th>";
                echo "<th>"."sepal-width"."</th>";
                echo "<th>"."petal-length"."</th>";
                echo "<th>"."petal-width"."</th>";
            echo "</tr>";
        echo "</thead>";
        for($i=0;$i<sizeof($this->kelas);$i++){
            echo "<tr>";
            echo "<td>".$class[$i]."</td>";
            for($j=0;$j<$this->total_atribut-1;$j++){
               echo "<td>"."Rata-rata "."<b>"."(".number_format($this->mean[$i][$j],2,',','').")"."</b>"."<br>"
               ."Standar Deviasi "."<b>"."(".number_format($this->sdeviasi[$i][$j],2,',','').")"."</b>"."</td>";
            }
            echo "</tr>";
        }
        echo "<table border=\"1\" cellspacing=\"0\">";
        echo "<td>"."No."."</td>";
        foreach ($class as $item){
            echo "<td>".$item."</td>";
        }
        for($i=0;$i<sizeof($this->data_test);$i++){
            echo "<tr>";
            echo "<td>".($i+1)."</td>";
            for($j=0;$j<sizeof($this->kelas);$j++){
                echo "<td>".number_format($this->model[$i][$j],3,',','')."</td>";
            }
            echo "</tr>";
        }
        echo "<br>"."<h2>"."PROBABILITAS"."</h2>";
       //data record baris pertama
        // $total1 = (1/sqrt(2*3.14*0.36)*exp(-((5.4-4.97)^2)/(0.36^2)));
        // $total2 = (1/sqrt(2*3.14*0.41)*exp(-((3.7-3.39)^2)/(0.41^2)));
        // $total3 = (1/sqrt(2*3.14*0.15)*exp(-((1.5-1.44)^2)/(0.15^2)));
        // $total4 = (1/sqrt(2*3.14*0.10)*exp(-((0.2-0.23)^2)/(0.10^2)));
        // $total1 = (1/sqrt(2*3.14*0.50)*exp(-((5.4-5.98)^2)/(0.50^2)));
        // $total2 = (1/sqrt(2*3.14*0.29)*exp(-((3.7-2.79)^2)/(0.29^2)));
        // $total3 = (1/sqrt(2*3.14*0.46)*exp(-((1.5-4.28)^2)/(0.46^2)));
        // $total4 = (1/sqrt(2*3.14*0.19)*exp(-((0.2-1.34)^2)/(0.19^2)));
        // $total1 = (1/sqrt(2*3.14*0.62)*exp(-((5.4-6.54)^2)/(0.62^2)));
        // $total2 = (1/sqrt(2*3.14*0.32)*exp(-((3.7-2.96)^2)/(0.32^2)));
        // $total3 = (1/sqrt(2*3.14*0.57)*exp(-((1.5-5.53)^2)/(0.57^2)));
        // $total4 = (1/sqrt(2*3.14*0.25)*exp(-((0.2-2.06)^2)/(0.25^2)));
    }
    public function hasil(){
        echo "<br>"."<h2>"." Hasil Pencarian : "."</h2>" ;
        for($i = 0; $i<sizeof($this->model); $i++){
          
          $kelas = array_search(max($this->model[$i]),$this->model[$i]);
          $this->hasil_kelas[$i] = $this->kelas[$kelas];
        }
        echo "<br>";
        $total = 0;
        for($i=0; $i<sizeof($this->data_test);$i++){
          if($this->data_test[$i][4]==$this->hasil_kelas[$i]){
            $total++;
          }
        }
        $akurasi = $total/(sizeof($this->data_test))*100;
        echo "Akurasi akurasi = $akurasi %";
    }
}
$nb = new NaiveBayes();
$nb->getDataTrain();
$nb->getKelas();
$nb->getDataTest();
$nb->cetak(); 
$nb->mean();
$nb->hitung_deviasi();
$nb->probabilitas();
$nb->hasil();
$nb->hasil_mean_sdev_model();
?>
