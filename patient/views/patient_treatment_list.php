<?php
if ((isset($patient_treatment_list))&&(!empty($patient_treatment_list))){			
	echo '<div class="panel  panel-default"  style="padding:2px;margin-bottom:1px;" >';
		echo '<div class="panel-heading"  style="background:#ffffff;" ><b>Previous treatments</b></div>';
			echo '<table class="table table-condensed table-hover"  style="font-size:0.95em;margin-bottom:0px;cursor:pointer;">';
			for ($i=0;$i<count($patient_treatment_list); ++$i){
				echo '<tr onclick="self.document.location=\''.site_url("form/edit/patient_treatment/".$patient_treatment_list[$i]["patient_treatment_id"]).'?CONTINUE='.$continue.'\';">';
					echo '<td>';
						echo $patient_treatment_list[$i]["CreateDate"];
					echo '</td>';
					echo '<td>';
						echo $patient_treatment_list[$i]["treatment"];
					echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
	echo '</div>';	
}
?>		