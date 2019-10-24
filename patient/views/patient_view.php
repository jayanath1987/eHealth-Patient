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
<div class="container" style="width: 99%;">
	<div class="row" style="margin-top: 55px; padding-bottom: 10px; padding-top: 15px;">
	<?php 
	$admid="";
	if ($this->uri->segment (4)){//Laura
		$admid= $this->uri->segment (4);
	}
		?>
            <table border="0" width="100%" >
                    <tr >
                        <td valign="top" class="leftmaintable">
		<div ><?php echo Modules::run('leftmenu/patient',$id,$patient_questionnaire_list,$admid); //runs the available left menu for preferance ?>
		</div>
                        </td>
                        <td valign="top" class="rightmaintable">
		<div ><?php echo Modules::run('patient/banner_full',$id); ?>
		</div>
   
            <?php  if($this->session->userdata('UserGroup')!='Admission'){ ?>
		<div class="well" style="padding: 2px;">
		
			<table border=0 width=100%>
				<tr>
					<td width=50% valign=top>

						<div id="opd_cont" style='padding: 5px;'><?php echo $previous_visits; ?></div>
					        <div id="adm_cont" style='padding: 5px;'><?php echo $admissions; ?> </div>		
						<div id="clinc_cont" style='padding: 5px;'><?php echo $clinics; ?></div>							
						<div id="exam_cont" style='padding: 5px;'><?php echo $exams;  ?></div>
                                                <?php if ($this->config->item('PACS') =="YES"){ ?>
                                                <div id="pacs_cont" style='padding: 5px;'><?php echo $pacs;  ?></div>
                                                
                                                <?php } ?>
					</td>
					<td width=50% valign=top>
						<div id="his_cont" style='padding: 5px;'><?php echo $history; ?></div>
						<div id="alergy_cont" style='padding: 5px;'><?php echo $allergy; ?></div>
						<div id="pre_cont" style='padding: 5px;'><?php echo $prescriptions;  ?></div>
						<div id="lab_cont" style='padding: 5px;'><?php echo $lab_orders; ?></div>						
						<div id="notes_cont" style='padding: 5px;'><?php print_r($notes);  ?></div>
						<div id="attach_cont" style='padding: 5px;'><?php echo $attachments; ?></div>
                                                <div id="inj_cont" style='padding: 5px;'><?php echo $injections;  ?></div>
						<div id="div_trauma" style='padding: 5px;'><?php echo $trauma;  ?></div>
					</td>
				</tr>
			</table>

            </div>  <?php }?>
                                          </td>
                </tr>
                </table>           
	</div>
</div>
</div>
