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
with this program. If not, see <http://www.gnu.org/licenses/> or write to:
Free Software  HHIMS
ICT Agency,
160/24, Kirimandala Mawatha,
Colombo 05, Sri Lanka
---------------------------------------------------------------------------------- 
Author: Author: Mr. Jayanath Liyanage   jayanathl@icta.lk
                 
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/

	include("header.php");	///loads the html HEAD section (JS,CSS)
?>
<?php echo Modules::run('menu'); //runs the available menu option to that usergroup ?>
<div class="container" style="width:95%;">
	<div class="row" style="margin-top:55px;">
	  <div class="col-md-2 ">
		<?php echo Modules::run('leftmenu/patient_pc',$id,$patient_questionnaire_list); //runs the available left menu for preferance ?>
	  </div>
	  <div class="col-md-10 ">
			<?php echo Modules::run('patient/banner_full',$id); ?>
					<div class="well" style="padding:2px;">
			
			<table border=0 width=100%>
				<tr>
					<td width=50% valign=top>
						<div id="clinc_cont"  style='padding:5px;'>
							<?php echo $clinics; ?>
						</div>	
						<div id="exam_cont"  style='padding:5px;'>
							<?php echo $exams; ?>
						</div>
						<div id="attach_cont"   style='padding:5px;'>
							<?php echo $attachments; ?>
						</div>		
						<div id="ref_cont"   style='padding:5px;'>
							<?php echo $ref; ?>
						</div>	
						
					</td>
					<td width=50% valign=top>

						<div id="his_cont"   style='padding:5px;'>
							<?php echo $history; ?>
						</div>
						  <div id="alergy_cont"   style='padding:5px;'>
							  <?php echo $allergy; ?>
						  </div>						
						  <div id="pre_cont" style='padding:5px;'>
								<?php echo $prescriptions;  ?>
						  </div>	
							<div id="notes_cont" style='padding:5px;'>
								<?php 
								//echo $notes ;  
								?>
						  </div>							  
					</td>
				</tr>
			</table>	
		</div>
		</div>

		</div>
</div>
