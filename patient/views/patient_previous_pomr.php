<?php
if ((isset($patient_pomr_list))&&(!empty($patient_pomr_list))){			
	echo '<div class="panel  panel-default"  style="padding:2px;margin-bottom:1px;" >';
		echo '<div class="panel-heading"  style="background:#ffffff;" ><b>Working diagnoses</b></div>';
			echo '<table class="table table-condensed table-hover"  style="font-size:0.95em;margin-bottom:0px;cursor:pointer;">';
			for ($i=0;$i<count($patient_pomr_list); ++$i){
				echo '<tr onclick="self.document.location=\''.site_url("form/edit/pomr/".$patient_pomr_list[$i]["pomr_id"]).'?CONTINUE='.$continue.'\';">';
					echo '<td>';
						echo $patient_pomr_list[$i]["priority"];
					echo '</td>';
					echo '<td>';
						echo $patient_pomr_list[$i]["start_date"];
					echo '</td>';
					echo '<td>';
						echo $patient_pomr_list[$i]["ICD_Text"];
					echo '</td>';
					echo '<td>';
						echo $patient_pomr_list[$i]["remarks"];
					echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
	echo '</div>';	
}
?>		