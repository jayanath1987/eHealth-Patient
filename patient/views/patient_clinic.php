<?php
/*
--------------------------------------------------------------------------------
HHIMS - Hospital Health Information Management System
Copyright (c) 2011 Information and Communication Technology Agency of Sri Lanka
<http: www.hhims.org/>
----------------------------------------------------------------------------------
This program is free software: you can redistribute it and/or modify it under the
terms of the GNU Affero General Public License as published by the Free Software 
Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,but WITHOUT ANY 
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR 
A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License along 
with this program. If not, see <http://www.gnu.org/licenses/> 




---------------------------------------------------------------------------------- 
Date : June 2016
Author: Mr. Jayanath Liyanage   jayanathl@icta.lk

Programme Manager: Shriyananda Rathnayake
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/

	include("header.php");	///loads the html HEAD section (JS,CSS)
?>
<?php echo Modules::run('menu'); //runs the available menu option to that usergroup ?>
<div class="container" style="width:95%;">
	<div class="row" style="margin-top:55px;">
	  <div class="col-md-2 ">
		<?php //echo Modules::run('leftmenu/patient',$id,$patient_questionnaire_list); //runs the available left menu for preferance ?>
	  </div>
	  <div class="col-md-10 ">
		<div class="panel panel-default"  >
			<div class="panel-heading"><b>Patient clinic management </b>
			</div>
			<?php echo Modules::run('patient/banner',$pid); ?>
			<div class="panel panel-default"  >
				<div class="panel-heading"><b>Clinics where this patient is registered</b></div>
				<?php 
					//print_r($clinic_list); 
					if (empty($clinic_list)){
						echo 'No clinic defined in the hospital. <br> <a target="_blank" href="'.site_url("form/create/clinic").'"> Create a clinic </a>';
					}
					else{
						echo '<table class="table">';
							for ($i=0; $i<count($clinic_list); ++$i){
								echo '<tr>';
									echo '<td>';
										echo '<b>'.$clinic_list[$i]["name"].'</b>';
									echo '</td>';
									echo '<td>';
										echo $clinic_list[$i]["applicable_to"];
									echo '</td>';
									echo '<td>';
										echo $clinic_list[$i]["remarks"];
									echo '</td>';
									echo '<td>';
									//print_r($clinic_list[$i]);
										if (isset($clinic_list[$i]["assigned_clinic"]["status"])&& ($clinic_list[$i]["assigned_clinic"]["status"] == "Refered")){
												echo '<span class="label label-success">Registered for this clinic</span>';
												echo '<br><a href="'.site_url("form/edit/clinic_patient_1/".$clinic_list[$i]["assigned_clinic"]["clinic_patient_id"]."/".$clinic_list[$i]["clinic_id"]."?CONTINUE=patient/clinic/".$pid).'"class=""><span class="label label-info">Next appointment:'.$clinic_list[$i]["assigned_clinic"]["next_visit_date"].'</span></a>';
												echo '&#160;<a  href="#" onclick="openWindow(\''.site_url("report/pdf/clinicToken/print/".$clinic_list[$i]["assigned_clinic"]["clinic_patient_id"]."/".$clinic_list[$i]["clinic_id"]).'\')" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-print"></span>Print token</a>';
												echo ' <a href="'.site_url("clinic/view/".$pid."?CONTINUE=".$clinic_list[$i]["clinic_id"]).'"class="btn btn-info btn-sm">Open clinic </a>';
											}else{
												echo ' <a href="'.site_url("form/create/clinic_patient/".$pid."/".$clinic_list[$i]["clinic_id"]."?CONTINUE=patient/clinic/".$pid).'"class="btn btn-default btn-sm">Give an Appointment</a>';
                                                                                                echo ' <a href="'.site_url("clinic/view/".$pid."?CONTINUE=".$clinic_list[$i]["clinic_id"]).'"class="btn btn-info btn-sm">Open clinic </a>';
											}
									echo '</td>';
								echo '</tr>';
							}
						echo '</table>';
						
					}	
				?>
			</div>			
			<center>
			<?php
				if (isset($_GET['CONTINUE'])){
					echo '<a href="'.site_url($_GET["CONTINUE"]).'" class="btn btn-default">Back</a>';
				}	
				else{
					echo '<a href="javascript:window.history.back(0)" class="btn btn-default">Back</a>';
				}
			
			?>	
			</center>
		</div>		
		
		</div>
		
	</div>
</div>
