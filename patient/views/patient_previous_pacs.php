<?php
if ((isset($patient_pacs_list))&&(!empty($patient_pacs_list))){			
	echo '<div class="panel  panel-default"  style="padding:2px;margin-bottom:1px;" >';
		echo '<div class="panel-heading"  ><b>PACS</b></div>';
			echo '<table class="table table-condensed table-hover"  style="font-size:0.95em;margin-bottom:0px;cursor:pointer;">';
			for ($i=0;$i<count($patient_pacs_list); ++$i){
				echo '<tr onclick="self.document.location=\''.site_url("form/edit/dicom/".$patient_pacs_list[$i]["did"]).'?CONTINUE='.$continue.'\';">';
					echo '<td>';
						echo $patient_pacs_list[$i]["CreateDate"];
					echo '</td>';
					echo '<td>';
						echo $patient_pacs_list[$i]["dct_name"];
					echo '</td>';
					echo '<td>';
						if ($patient_pacs_list[$i]["Status"]=="Pending"){
							echo '<span class="label label-danger">'.$patient_pacs_list[$i]["Status"].'</span>';
						}
						else{
							echo '<span class="label label-warning">'.$patient_pacs_list[$i]["Status"].'</span>';
						}
					echo '</td>';
					echo '<td>';
						echo $patient_pacs_list[$i]["Order_Remarks"];
					echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
	echo '</div>';	
}
?>		