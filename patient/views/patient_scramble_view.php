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

<div class="container" style="width:95%;">
	<div class="row" style="margin-top:55px;">
	  <div class="col-md-2 ">
	  </div>
	  <div class="col-md-9 ">
			  <div class="alert alert-danger">
					<h3 class="text-center"><u>WARNING</u></h3>
					
					This function will scramble all patient data and you cant recover back.<br>
					You can only recover from last saved back up.<br>
					NEVER RUN THIS ON RUNNING HOSPITALS!.<hr>
					<b>
						<Ul>
							<li>1. It will delete the NIC number.</li>
							<li>2. It will delete the contact/mobile number.</li>
							<li>3. It will change the hospital code to 9999.</li>
							<li>4. Patient's HIN will be changed.</li>
							<li>5. Patient's Name and Initials will be randomized like (REED,STELLA,THOMAS).</li>
							<li>6. Patient's day of birth will be changed by -+5 days.</li>
							<li>7. Patient's village will be randomized.</li>
						</Ul>
						<div id="result">
								<input type="checkbox" class="" id="i_agree" name="i_agree">&nbsp;&nbsp;I <?php echo $this->session->userdata('FullName'); ?> agreed to all above mentioned points.
								<input type="submit" class="btn btn-danger hidden" id="scramble"  value="Scramble patient data" >
						</div>
					</b>
			  </div>
		</div>
	</div>
</div>
<script>
$(
	function(){
		$("#i_agree").click(function(){
			if($(this).attr("checked") == "checked"){
				$("#scramble").removeClass("hidden");
			}
			else{
				$("#scramble").addClass("hidden");
			}
		});
		
		$("#scramble").click(function(){
				$("#scramble").addClass("hidden");
				$("#i_agree").addClass("hidden");
				$("#result").append('<div id="res"><hr>RESULT:<br>Wait data scrambling =</div>');
				var request = $.ajax({
					xhr:function(){
						var xhr = new window.XMLHttpRequest();
					   xhr.addEventListener("progress", function(evt) {
							$("#res").append("=>");
					   }, false);
					   return xhr;
				  },				  
				  url: "<?php echo site_url('patient/scramble'); ?>",
				  type: "POST",
				  data: { i_agree : $("#i_agree").attr("checked") }
				});
				 
				request.done(function( msg ) {
					$("#res").append(msg);
				});
		});
	}
)
</script>
