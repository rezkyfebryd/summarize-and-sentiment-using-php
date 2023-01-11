<?php
$basepath = true;
$data['title'] = "Summarization";
$data['menu'] = 2;
$data['submenu'] = 22;
include "header.php";

require("core/spreadsheet-reader/php-excel-reader/excel_reader2.php");
require("core/spreadsheet-reader/SpreadsheetReader.php");

require_once 'includes/summarizer.php';
require_once 'includes/html_functions.php';


?>
<br>
<h1>Summarization</h1>

<form action="" method="post" enctype="multipart/form-data">
	<div class="well">
		<input type="file" name="file_batch" class="form-control" accept=".xls, .xlsx, .csv">
		<button name="btn" style="background-color:#66CDAA; padding: 7px 10px; border-radius: 4px; border: none;" >Submit</button>
	</div>
</form>

<div class="well">
<?php


?>
</div>

<?php
if(isset($_POST['btn'])){
	$start = microtime(true);

	$file = $_FILES['file_batch'];
	if($file['error'] > 0){
		create_alert("error","Mohon mengupload file dalam format Excel untuk diolah oleh sistem");
	} 
	else{
		$filename = $file['name'];
		$ext = get_extension($filename);
		if(!in_array($ext, array("xls", "xlsx", "csv"))){
			create_alert("error","Mohon mengupload file dalam format Excel untuk diolah");
		}
		else{
			$skrg = date("YmdHis");
			$loc = "temp/".$skrg.".".$ext;

			$save = move_uploaded_file($file['tmp_name'], $loc);
			//akses file yang sudah diupload
			$sp = new SpreadsheetReader($loc);
			$sheet = $sp->Sheets();
}


			$text = array();
			
			//$text = normalizeHtml($isi);
			foreach($sheet as $index=>$name){

				$sp->ChangeSheet($index);
				foreach($sp as $key=>$row){
					if(!isset($row[0])){
						break;
					}
					if(strlen($row[0]) > 0){
						$text[] = $row[0]; //simpan ke array
						
					}
				}
			}

			
			$r = 0;
			foreach($text as $key=>$value){
				
				$summarizer = new Summarizer();
				$out_text[$r] = $value;
				
				$isi1 = $out_text[$r];
						$rez = $summarizer->summary($isi1);
						//print_r($rez);
	
						//$rez is an array of sentences. Turn it into contiguous text by using implode().
						$summary1 = implode(' ',$rez);
				
						
				
				$stem[$r] = $summarizer->word_tokenize($summary1);

				$imploded = implode(",",$stem[$r]);
				$now = date("Y-m-d H:i:s");

				$r++;
			}
			if($r == 0){
				//tidak ada data kalimat yg diolah
				create_alert("error","File excel tidak berisi kalimat komentar apapun. Mohon diperiksa kembali");
			}
			?>

			<div class="well">
				<h2>Summarization Result</h2>
				<table class="data table table-sm pmd-table">
					<thead>
						<tr>
							<th>#</th>
							<th>Ulasan</th>
							<th>Ringkasan</th>
							
						</tr>
					</thead>
					<tbody>
					<?php
					$n = 1;
					for($i=0; $i<$r;$i++){	
						
						$summarizer = new Summarizer();
						$isi = $out_text[$i];
						$rez = $summarizer->summary($isi);
						//print_r($rez);
	
						//$rez is an array of sentences. Turn it into contiguous text by using implode().
						$summary = implode(' ',$rez);
						
						$summary = mb_convert_case($summary, MB_CASE_LOWER, "UTF-8");
						
						$stemmed = "";
						
						if(count($stem[$i]) > 0){
							$stemmed = "<span class='label label-primary'>".implode("</span> <span class='label label-primary'>",$stem[$i])."</span>";
						}
						
						echo "
						<tr>
							<td>$n</td>
							<td>$out_text[$i]</td>
							<td>$summary</td>
							
							
						</tr>
						";
						$n++;
					}
					?>
					</tbody>
				</table>
			</div>


			<?php
		}
	//}
	$end = microtime(true);
	$eksekusi = $end - $start;
	echo "<em>Dieksekusi dalam waktu ".number_format($eksekusi,2,".",",")." detik</em>";

}


include "footer.php";
?>