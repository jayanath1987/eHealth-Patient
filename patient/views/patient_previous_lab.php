<?php
if ((isset($patient_lab_order_list))&&(!empty($patient_lab_order_list))){			
	echo '<div class="panel panel-default"  style="padding:2px;margin-bottom:1px;" >';
	echo '<div class="panel-heading" ><b>Previous lab Orders</b></div>';

		echo '<table class="table table-condensed table-hover"  style="font-size:0.95em;margin-bottom:0px;">';
		for ($i=0;$i<count($patient_lab_order_list); ++$i){
			echo '<tr >';
			echo '<td width=10px>';
			echo '<a title="Click here to open the related record" class="btn btn-xs ';
			if ($this->uri->segment(3) ==$patient_lab_order_list[$i]["OBJID"]){
				echo ' btn-warning"';
			}
			else{
				echo ' btn-default"';
			}
			if ($patient_lab_order_list[$i]["Dept"] =="OPD"){
				echo ' href="'.site_url("opd/view/".$patient_lab_order_list[$i]["OBJID"]).'" ';
			}
			if ($patient_lab_order_list[$i]["Dept"] =="ADM"){
				echo ' href="'.site_url("admission/view/".$patient_lab_order_list[$i]["OBJID"]).'" ';
			}
			else{
				echo ' href="#" ';
			}
			echo '>'.$patient_lab_order_list[$i]["Dept"].'</a>';
			echo '</td>';
			echo '<td>';
					echo $patient_lab_order_list[$i]["OrderDate"];
			echo '</td>';
			echo '<td>';
				echo '<a title="Click here to view the lab test result" href="'.site_url("laboratory/order/".$patient_lab_order_list[$i]["LAB_ORDER_ID"]).'?CONTINUE='.$continue.' " >';
					echo $patient_lab_order_list[$i]["TestGroupName"];
				echo '</a>';		
			echo '</td>';
			echo '<td>';
			if ($patient_lab_order_list[$i]["Status"] =="Pending"){
			
				echo '<span class=" blink_me" style="color:red">'.$patient_lab_order_list[$i]["Status"].'...</span>';

                               		}
			else{
				echo '<span class=" label label-success">'.$patient_lab_order_list[$i]["Status"].'</span>';
			}
			echo '</td>';
                        if ($patient_lab_order_list[$i]["Status"] =="Pending" ){
                            if (($this->session->userdata('UserGroup') === 'Programmer') || ($this->session->userdata('UserGroup') === 'Doctor')){
			echo '<td>';
                        
                                echo '<a title="Click here to Enter the lab test result" href="'.site_url("laboratory/process_result/".$patient_lab_order_list[$i]["LAB_ORDER_ID"]).'?CONTINUE='.$continue.' " >';
                                echo '[Enter result]';
				echo '</a>';

			echo '</td>';
                        }
                        }
                                                if ($patient_lab_order_list[$i]["Status"] =="Pending" && $patient_lab_order_list[$i]["Collection_Status"] =="Pending"){
                            if (($this->session->userdata('UserGroup') == 'Programmer') || ($this->session->userdata('FullName') ==$patient_lab_order_list[$i]["OrderBy"])){
			echo '<td>';
                        
                                echo '<a title="Click here to Cancel the lab test" href="'.site_url("laboratory/cancel_test/".$patient_lab_order_list[$i]["LAB_ORDER_ID"]).'?CONTINUE='.$continue.' " >';
                                echo '<p style="color:red">[Cancel Test]</p>';
				echo '</a>';

			echo '</td>';
                        }
                        }
                        
			echo '</tr>';
		}
		echo '</table>';
	echo '</div>';	
}
?>