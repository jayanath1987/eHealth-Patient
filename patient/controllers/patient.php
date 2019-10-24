<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
__________________________________________________________________________________
SNOMED Modification :

Date : July 2015		ICT Agency of Sri Lanka (www.icta.lk), Colombo
Author : Laura Lucas
Programme Manager: Shriyananda Rathnayake
Supervisors : Jayanath Liyanage, Erandi Hettiarachchi
URL: http://www.govforge.icta.lk/gf/project/hhims/
----------------------------------------------------------------------------------
*/

/*

*/
class Patient extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->checkLogin();
        $this->load->library('session');
                $this->load->library('encrypt');
		if(isset($_GET["mid"])){
			$this->session->set_userdata('mid', $_GET["mid"]);
		}
    }

    public function index()
    {
        //$this->load->view('patient');
		echo "nothing here";
    }

    public function create()
    {
       
      /*  if (!Modules::run('security/haveAccess',$this->session->userdata('UGID'),'patient_New')){
			$data["error"] =" User group '".$this->session->userdata('UserGroup')."' have no rights to edit this data";
			$this->load->vars($data);
			$this->load->view('patient_error');
			exit;
		} */
        echo Modules::run('form/create', 'patient');
    }
	
	public function banner($id){
		if(!isset($id) ||(!is_numeric($id) )){
			$data["error"] = "Patien not found";
			$this->load->vars($data);
			$this->load->view('patient_error');	
			return;
		}
		$this->load->model('mpersistent');
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
		if (empty($data["patient_info"])){
			$data["error"] ="Patient not found";
			$this->load->vars($data);
			$this->load->view('patient_error');
		}
		if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);

		$this->load->vars($data);
        $this->load->view('patient_banner');
	}	


	public function banner_full($id){
		if(!isset($id) ||(!is_numeric($id) )){
			$data["error"] = "Patien not found";
			$this->load->vars($data);
			$this->load->view('patient_error');	
			return;
		}
		$this->load->model('mpersistent');
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
		if (empty($data["patient_info"])){
			$data["error"] ="Patient not found";
			$this->load->vars($data);
			$this->load->view('patient_error');
		}
		if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);

		$this->load->vars($data);
        $this->load->view('patient_banner_full');
	}	
	
	public function get_hin($s) {
                    $this->load->model("mhospital");
                    $data = $this->mhospital->get_poi();     
                    
		  //$hospital = $this->session->userdata("hospital_info");
        	  //$h_code = $hospital["Code"];
                  $h_code = $data[0]["Code"];                   
		  $pid = sprintf("%06s",$s);
		  $hin = $h_code.$pid;
		  $hin_number = $hin;
		  $hin=$hin."0";
		  $sum=0;
		  $i=strlen($hin);     // Find the last character
		  $odd_length = $i%2;
		  while ($i-- > 0) { // Iterate all digits backwards
			$sum+=$hin[$i];    // Add the current digit
			// If the digit is even, add it again. Adjust for digits 10+ by subtracting 9.
			($odd_length==($i%2)) ? ($hin[$i] > 4) ? ($sum+=($hin[$i]-9)) : ($sum+=$hin[$i]) : false;
		  }
		  return $hin_number.(10-($sum%10))%10; //returns the luhn check digit
	}
	
	public function get_previous_injection_list($pid){
		$this->load->model("mpatient");
		$data["previous_injection_list"] = $this->mpatient->get_previous_injection_list($pid);
		$this->load->vars($data);
                $this->load->view('patient_previous_injection_list');
	}
        public function get_previous_opd_prescription_list($pid){
		$this->load->model("mpatient");
		$data = array();
		$data["prescription_list"] = $this->mpatient->get_opd_prescription_list($pid);
		return $data["prescription_list"];
	}
	public function get_previous_notes_list($pid){
		$this->load->model("mpatient");
		$data["previous_notes_list"] = $this->mpatient->get_previous_notes_list($pid);
		$this->load->vars($data);
        $this->load->view('patient_previous_notes_list');
	}
	public function get_previous_exams($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_exams_list"] = $this->mpatient->get_exams_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_exam');
		}
		else{
			return $data["patient_exams_list"];
		}
			//$data["patient_prescription_list"] = $this->mpatient->get_prescription_list($opdid);
			//$data["patient_treatment_list"] = $this->mpatient->get_treatment_list($opdid);
	}
	
	
	
	public function get_previous_injection($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["previous_injection_list"] = $this->mpatient->get_previous_injection_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_injection');
		}
		else{
			return $data["previous_injection_list"];
		}
	}	
	
	public function get_previous_history($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_history_list"] = $this->mpatient->get_history_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_history');
		}
		else{
			return $data["patient_history_list"];
		}
	}
	
	public function get_previous_allergy($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_allergy_list"] = $this->mpatient->get_allergy_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_allergy');
		}
		else{
			return $data["patient_allergy_list"];
		}
	}	
        
  	public function get_pomr($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_pomr_list"] = $this->mpatient->get_pomr_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_pomr');
		}
		else{
			return $data["patient_pomr_list"];
		}
	}
        
        public function get_treatment($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_treatment_list"] = $this->mpatient->get_patient_treatment_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_treatment_list');
		}
		else{
			return $data["patient_treatment_list"];
		}
	}
	

	public function get_previous_lab($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_lab_order_list"] = $this->mpatient->get_lab_order_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_lab');
		}
		else{
			return $data["patient_lab_order_list"];
		}
	}		
    public function open_model($id)
    {
        $this->load->model('mpersistent');
        $this->mpersistent->load('patient');
        $this->mpersistent->open_id($id);
    }
	
	public function reffer_to_clinic($id=null){
		if (!Modules::run('security/check_edit_access','clinic_patient','can_edit')){
			$data["error"] =" User group '".$this->session->userdata('UserGroup')."' have no rights to edit this data";
			$this->load->vars($data);
			$this->load->view('patient_error');
			exit;
		}
        $this->load->model('mpersistent');
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
		
		if (empty($data["patient_info"])){
			$data["error"] ="Patient not found";
			$this->load->vars($data);
			$this->load->view('patient_error');
		}
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);
        $data["id"] = $id;		
		$this->load->vars($data);
        $this->load->view('patient_clinic');
	}	
	
	
	public function clinic($id=null){
		if (!Modules::run('security/check_edit_access','clinic_patient','can_edit')){
			$data["error"] =" User group '".$this->session->userdata('UserGroup')."' have no rights to edit this data";
			$this->load->vars($data);
			$this->load->view('patient_error');
			exit;
		}
        $this->load->model('mpersistent');
        $this->load->model('mclinic');
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
	
		if (empty($data["patient_info"])){
			$data["error"] ="Patient not found";
			$this->load->vars($data);
			$this->load->view('patient_error');
		}
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);
		
        $data["pid"] = $id;	
        $data["clinic_list"] = $this->mclinic->get_clinic_list($data["patient_info"]["Gender"]);	
		if (!empty($data["clinic_list"])){	
			for ($i=0; $i<count($data["clinic_list"]);++$i){
				$data["clinic_list"][$i]["assigned_clinic"] = $this->mclinic->is_patient_assigned($id,$data["clinic_list"][$i]["clinic_id"]);	
			}
		}
		$this->load->vars($data);
        $this->load->view('patient_clinic');
	}
	
	public function notes($id = NULL)
	{
		if (!is_numeric($id)) {
            die("Patient ID not valid");
        }
        $this->load->model('mpersistent');
		$this->load->model('mpatient');
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
		 if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);
		$data["patient_notes_list"] = $this->mpatient->get_notes_list($id,"patient");
		$data["opd_notes_list"] = $this->mpatient->get_notes_list($id,"opd");
		//print_r($data["opd_notes_list"]);
		$this->load->vars($data);
		$this->load->view('patient_notes');
	}
	
    public function view($id = NULL, $admid = NULL)
    {
		if (!Modules::run('security/check_view_access','patient','can_view')){
			$data["error"] =" User group '".$this->session->userdata('UserGroup')."' have no rights to view this data";
			$this->load->vars($data);
			$this->load->view('patient_error');
			exit;
		}
         
		
        if (!$id) {
            echo Modules::run("patient/search/");
        }

        if (!is_numeric($id)) {
            die("Patient ID not valid");
        }
    
        
        $this->load->model('mpersistent');
       	$this->load->model('mquestionnaire');
        $this->load->helper('file');
        $this->session->set_userdata('jpid', $id);
       

       
        $data["patient_info"] = $this->mpersistent->open_id($id, "patient", "PID");
        $hin = $data["patient_info"]['HIN'];
	$this->session->set_userdata('jpdob', $data["patient_info"]['DateOfBirth']);
        
        if($this->session->userdata('UserGroup') == 'Doctor'){
        
        $now = array("Triage_Level"=>NULL,"Triage_Visit_Date"=>  date("Y-m-d H:i:s"));
        $this->mpersistent->update('patient','PID',$id,$now);
            
        }
        //Laura
        if (isset($admid) && is_numeric($admid)){
        	$data["admission_info"] = $this->mpersistent->open_id($admid,"admission","ADMID");
        }
        $data["admission_visits"] = $this->mpersistent->open_id($admid,"admission_visit","ADMID");
        
		//
		if (empty($data["patient_info"])){
			$data["error"] ="Patient not found";
			$this->load->vars($data);
			$this->load->view('patient_error');
		}
        if (isset($data["patient_info"]["DateOfBirth"])) {
            $data["patient_info"]["Age"] = $this->get_age($data["patient_info"]["DateOfBirth"]);
        }
        
		if (get_file_info('./attach/'.$data["patient_info"]["HIN"].'/'.$data["patient_info"]["HIN"].'_portrait.jpg')){
			$data["image"] = base_url().'attach/'.$data["patient_info"]["HIN"].'/'.$data["patient_info"]["HIN"].'_portrait.jpg';
		}
		else{
			$data["image"] = base_url().'/images/patient.jpg';
		}
		$data["patient_info"]["HIN"] = $this->print_hin($data["patient_info"]["HIN"]);
		$data["patient_questionnaire_list"] = null;
		$data["patient_questionnaire_list"] = $this->mquestionnaire->get_questionnaire_list("patient");
        $data["id"] = $id;
        
        
        
		if ($this->config->item('purpose') =="PC"){ // if for pain clinic

			$data["exams"] = $this->loadExam($id);
			$data["history"] = $this->loadHistory($id);
			$data["allergy"] = $this->loadAlergy($id);
			$data["lab_orders"] = $this->loadLabOrder($id);
			$data["prescriptions"] = $this->loadPrescription($id);
			$data["attachments"] = $this->loadAttachment($id);
			$data["notes"] = $this->loadNotes($id);
			$data["clinics"] = $this->loadClinics($id);
			$this->load->vars($data);
			$this->load->view('patient_view');
			
		}
		else if ($this->config->item('purpose') =="PP"){ // if for private practice
			
			$data["previous_visits"] = $this->previousVisits($id);
		//	$data["admissions"] = $this->loadAdmission($id);
			$data["exams"] = $this->loadExam($id);
			$data["history"] = $this->loadHistory($id);
			$data["allergy"] = $this->loadAlergy($id);
			//$data["lab_orders"] = $this->loadLabOrder($id);
			$data["prescriptions"] = $this->loadPrescription($id);
			$data["injections"] = $this->loadInjections($id); 
			$data["attachments"] = $this->loadAttachment($id);
			$data["notes"] = $this->loadNotes($id);
			//$data["clinics"] = $this->loadClinics($id);
			$this->load->vars($data);
			$this->load->view('patient_view');
			
		}else{
			$data["previous_visits"] = $this->previousVisits($id);
			$data["admissions"] = $this->loadAdmission($id);
			$data["exams"] = $this->loadExam($id);
			$data["history"] = $this->loadHistory($id);
			$data["allergy"] = $this->loadAlergy($id);
			$data["lab_orders"] = $this->loadLabOrder($id);
			$data["prescriptions"] = $this->loadPrescription($id);
			$data["injections"] = $this->loadInjections($id); 
			$data["attachments"] = $this->loadAttachment($id);
			$data["notes"] = $this->loadNotes($id);
			$data["clinics"] = $this->loadClinics($id);
                        $data["pacs"] = $this->loadPacs($id,$hin);
			$data["trauma"] = $this->loadTrauma($id);
                      
                        
			$this->load->vars($data);
			$this->load->view('patient_view');
		}
		
    }

    
 private function loadInjections($pid)
    {
        $qry
            = "SELECT patient_injection.patient_injection_id ,
	SUBSTRING(patient_injection.CreateDate,1,10) as dte,
	injection.name,
	injection.dosage,
	patient_injection.status
	FROM patient_injection, injection
	where (injection.injection_id=patient_injection.injection_id) and (patient_injection.PID ='" . $pid . "') and (patient_injection.Active = 1)";
          //  die(print_r($qry));
        $this->load->model('mpager','injections_page');
        $injections_page = $this->injections_page;
        $injections_page->setSql($qry);
        $injections_page->setDivId("inj_cont"); //important
        $injections_page->setDivClass('');
        $injections_page->setRowid('patient_injection_id');
        $injections_page->setCaption("Injections");
        $injections_page->setShowHeaderRow(false);
        $injections_page->setShowFilterRow(false);
        $injections_page->setColNames(array("ID", "", "", "", ""));
        $injections_page->setRowNum(25);
        $injections_page->setColOption("patient_injection_id", array("search" => false, "hidden" => true));
        $injections_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 70));
        $injections_page->gridComplete_JS = "function() {
        $('#inj_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/patient_injection_update")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $injections_page->setOrientation_EL("L");
        return $injections_page->render(false);
    }
////////////////////////////////
	
	private function loadClinics($pid){
        $qry
            = "SELECT clinic_visits.clinic_visits_id,
			clinic_visits.DateTimeOfVisit, clinic.name,
			concat(user.Title,' ',user.OtherName)as Doctor,
			if (clinic_visits.Status='close','Closed','Opened') Status
	FROM clinic_visits
	 LEFT JOIN `clinic` ON clinic.clinic_id = clinic_visits.clinic 
	LEFT JOIN `user` ON user.UID = clinic_visits.Doctor 
	where (clinic_visits.PID ='" . $pid . "') and (clinic_visits.Active=1) 	";
        $this->load->model('mpager','clinic_page');  
        $clinic_page = $this->clinic_page;
        $clinic_page->setSql($qry);
        $clinic_page->setDivId("clinc_cont"); //important
        $clinic_page->setDivClass('');
        $clinic_page->setRowid('clinic_visits_id');
        $clinic_page->setCaption("Clinic visits");
        $clinic_page->setShowHeaderRow(false);
        $clinic_page->setShowFilterRow(false);
        $clinic_page->setShowPager(false);
        $clinic_page->setColNames(array("ID", "","","","" ));
        $clinic_page->setRowNum(25);
        $clinic_page->setColOption("clinic_visits_id", array("search" => false, "hidden" => true));
        $clinic_page->setColOption("DateTimeOfVisit", array("search" => false, "hidden" => false, "width" => 75));
        $clinic_page->setColOption("name", array("search" => false, "hidden" => false, "width" => 75));
        $clinic_page->setColOption("Doctor", array("search" => false, "hidden" => false, "width" => 220));
        $clinic_page->gridComplete_JS
            = "function() {
        $('#clinc_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            window.location='".site_url("clinic/visit_view")."/'+rowId;
        });
        }";
        $clinic_page->setOrientation_EL("L");
        return $clinic_page->render(false);		
	}
	
    private function previousVisits($pid)
    {
        $qry
            = "SELECT opd_visits.OPDID , SUBSTRING(opd_visits.DateTimeOfVisit,1,10) as dte,visit_type.Name,opd_visits.Complaint,
	CONCAT(user.Title,user.OtherName )
	FROM opd_visits
	 LEFT JOIN `user` ON user.UID = opd_visits.Doctor 
	LEFT JOIN `visit_type` ON visit_type.VTYPID = opd_visits.VisitType
	where (opd_visits.PID ='" . $pid . "') and (user.UID = opd_visits.Doctor) 	";
        $this->load->model('mpager','visit_page');
        $visit_page = $this->visit_page;
        $visit_page->setSql($qry);
        $visit_page->setDivId("opd_cont"); //important
        $visit_page->setDivClass('');
        $visit_page->setRowid('OPDID');
        $visit_page->setCaption("Previous OPD visits");
        $visit_page->setShowHeaderRow(false);
        $visit_page->setShowFilterRow(false);
        $visit_page->setShowPager(false);
        $visit_page->setColNames(array("ID", "", "", "", ""));
        $visit_page->setRowNum(25);
        $visit_page->setColOption("OPDID", array("search" => false, "hidden" => true));
        $visit_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 75));
        $visit_page->gridComplete_JS
            = "function() {
        $('#opd_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            window.location='".site_url("opd/view")."/'+rowId;
        });
        }";
        $visit_page->setOrientation_EL("L");
        return $visit_page->render(false);
    }

    public function  loadAdmission($pid)
    {
        $qry
            = "SELECT admission.ADMID , SUBSTRING(admission.AdmissionDate,1,10) as dte,admission.Complaint,admission.OutCome,
	CONCAT(user.Title,user.OtherName )
	FROM admission,user
	where (admission.PID ='" . $pid . "') and (user.UID = admission.Doctor) 	";
        $this->load->model('mpager','admission_page');
        $admission_page = $this->admission_page;
        $admission_page->setSql($qry);
        $admission_page->setDivId("adm_cont"); //important
        $admission_page->setDivClass('');
        $admission_page->setRowid('ADMID');
        $admission_page->setCaption("Previous admissions");
        $admission_page->setShowHeaderRow(false);
        $admission_page->setShowFilterRow(false);
        $admission_page->setColNames(array("ID", "", "", "", ""));
        $admission_page->setRowNum(25);
        $admission_page->setColOption("ADMID", array("search" => false, "hidden" => true));
        $admission_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 75));
        $admission_page->gridComplete_JS
            = "function() {
        $('#adm_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("admission/view")."/'+rowId+'';        });
        }";
        $admission_page->setOrientation_EL("L");
        return $admission_page->render(false);
    }

    public function loadExam($pid)
    {
        $qry
            = "SELECT patient_exam.PATEXAMID ,
	SUBSTRING(patient_exam.ExamDate,1,10) as dte,
	CONCAT(patient_exam.sys_BP,' / ',patient_exam.diast_BP) as bp,
	CONCAT(patient_exam.Weight,'Kg.') as weight,
	CONCAT(patient_exam.Height,'m') as height,
	CONCAT(patient_exam.Temprature,'`C')
	FROM patient_exam
	where (patient_exam.PID ='" . $pid . "') and(patient_exam.Active = 1)";
        $this->load->model('mpager','exam_page');
        $exams_page = $this->exam_page;
        $exams_page->setSql($qry);
        $exams_page->setDivId("exam_cont"); //important
        $exams_page->setDivClass('');
        $exams_page->setRowid('PATEXAMID');
        $exams_page->setCaption("Examinations");
        $exams_page->setShowHeaderRow(false);
        $exams_page->setShowFilterRow(false);
        $exams_page->setColNames(array("ID", "", "", "", "", ""));
        $exams_page->setRowNum(25);
        $exams_page->setColOption("PATEXAMID", array("search" => false, "hidden" => true));
        $exams_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 75));
        $exams_page->setColOption("bp", array("search" => false, "hidden" => false, "width" => 100));
        $exams_page->setColOption("weight", array("search" => false, "hidden" => false, "width" => 70));
        $exams_page->gridComplete_JS = "function() {
        $('#exam_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/patient_exam")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $exams_page->setOrientation_EL("L");
        return $exams_page->render(false);
    }

    public function loadHistory($pid)
    {
        $qry
            = "SELECT patient_history.PATHISTORYID ,
	SUBSTRING(patient_history.HistoryDate,1,10) as dte,
	patient_history.ICD_Text,
	patient_history.Remarks
	FROM patient_history
	where (patient_history.PID ='" . $pid . "') and(patient_history.Active = 1)";
        $this->load->model('mpager','history_page');
        $history_page = $this->history_page;
        $history_page->setSql($qry);
        $history_page->setDivId("his_cont"); //important
        $history_page->setDivClass('');
        $history_page->setRowid('PATHISTORYID');
        $history_page->setCaption("History");
        $history_page->setShowHeaderRow(false);
        $history_page->setShowFilterRow(false);
        $history_page->setColNames(array("ID", "", "", ""));
        $history_page->setRowNum(25);
        $history_page->setColOption("PATHISTORYID", array("search" => false, "hidden" => true));
        $history_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 70));
        $history_page->gridComplete_JS = "function() {
        $('#his_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/patient_history")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $history_page->setOrientation_EL("L");
        return $history_page->render(false);
    }

    private function loadTrauma($pid)
    {
        $qry
            = "SELECT t.TRA_ID,SUBSTRING(t.CreateDate,1,10) as dte , i.TRA_CINJ_Name, v.TRA_TRAP_Name
	FROM trauma_surveillance as t
	 LEFT JOIN `trauma_cause_injury` i ON i.TRA_CINJ_ID = t.TRA_CINJ_ID 
	 LEFT JOIN `trauma_transport_patient` v ON v.TRA_TRAP_ID = t.TRA_TRAP_ID
	where (t.PID ='" . $pid . "') ";
        $this->load->model('mpager','trauma_surveillance');
        $trauma_surveillance = $this->trauma_surveillance;
        $trauma_surveillance->setSql($qry);
        $trauma_surveillance->setDivId("div_trauma"); //important
        $trauma_surveillance->setDivClass('');
        $trauma_surveillance->setRowid('TRA_ID');
        $trauma_surveillance->setCaption("Trauma");
        $trauma_surveillance->setShowHeaderRow(false);
        $trauma_surveillance->setShowFilterRow(false);
        $trauma_surveillance->setColNames(array("ID", "", "", ""));
        $trauma_surveillance->setRowNum(25);
        $trauma_surveillance->setColOption("TRA_ID", array("search" => false, "hidden" => true));
        $trauma_surveillance->setColOption("dte", array("search" => false, "hidden" => false, "width" => 70));
        $trauma_surveillance->gridComplete_JS = "function() {
        $('#div_trauma .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/trauma_surveillance")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $trauma_surveillance->setOrientation_EL("L");
        return $trauma_surveillance->render(false);
    }
    
    public function loadAlergy($pid)
    {
        $qry
            = "SELECT patient_alergy.ALERGYID ,
	SUBSTRING(patient_alergy.CreateDate,1,10) as dte,
	patient_alergy.Name,
	patient_alergy.Status,
	patient_alergy.Remarks
	FROM patient_alergy
	where (patient_alergy.PID ='" . $pid . "') and (patient_alergy.Active = 1)";
        $this->load->model('mpager','alergy_page');
        $alergy_page = $this->alergy_page;
        $alergy_page->setSql($qry);
        $alergy_page->setDivId("alergy_cont"); //important
        $alergy_page->setDivClass('');
        $alergy_page->setRowid('ALERGYID');
        $alergy_page->setCaption("Allergies");
        $alergy_page->setShowHeaderRow(false);
        $alergy_page->setShowFilterRow(false);
        $alergy_page->setColNames(array("ID", "", "", "", ""));
        $alergy_page->setRowNum(25);
        $alergy_page->setColOption("ALERGYID", array("search" => false, "hidden" => true));
        $alergy_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 70));
        $alergy_page->gridComplete_JS = "function() {
        $('#alergy_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/patient_alergy")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $alergy_page->setOrientation_EL("L");
        return $alergy_page->render(false);
    }
    
    private function loadPacs($pid,$hin){ 

              $qry= "SELECT 
	  d.did,	  
          d.CreateDate,
	  dc.dct_name,
          d.Status

	  
	  from dicom d 
          LEFT JOIN dicom_category dc ON dc.dctid = d.dctid 
	where (d.PID ='" . $pid . "') ";

        $this->load->model('mpager','pacs_page');
        $pacs_page = $this->pacs_page;
        $pacs_page->setSql($qry);
        $pacs_page->setDivId("pacs_cont"); //important
        $pacs_page->setDivClass('');
        $pacs_page->setRowid('did');
        $pacs_page->setCaption("Picture Archiving and Communication System Digital Results");
        $pacs_page->setShowHeaderRow(false);
        $pacs_page->setShowFilterRow(false);
        $pacs_page->setColNames(array("ID", "", "","")); 
        $pacs_page->setRowNum(25);
        $pacs_page->setColOption("did", array("search" => false, "hidden" => true));
        $pacs_page->setColOption("CreateDate", array("search" => false, "hidden" => false));
 $pacs_page->gridComplete_JS
            = "function() {
		$('div[id ^= \"pager\"]').hide();
        $('#pacs_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            var params = 'menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,width='+screen.availWidth+',height='+screen.availHeight;
		    var url = '".$this->config->item('PACS_URL').":".$this->config->item('PACS_PORT')."/".$this->config->item('PACS_PATH')."".$hin."';
                   var url2 = '".$this->config->item('PACS_URL').":".$this->config->item('PACS_PORT')."/".$this->config->item('PACS_oviyam_PATH')."".$hin."';    
			window.open('' + url + '', 'lookUpW', params);
        });
    }"; 


        $pacs_page->setOrientation_EL("L");
        return $pacs_page->render(false); 
    }
 private function loadNotes($pid)
    {
        $qry
            = "SELECT patient_notes.patient_notes_id ,
	SUBSTRING(patient_notes.CreateDate,1,10) as dte,
	patient_notes.notes
	FROM patient_notes
	where (patient_notes.PID ='" . $pid . "') and (patient_notes.Active = 1) ";
        $this->load->model('mpager','patient_notes');
        $patient_notes = $this->patient_notes;
        $patient_notes->setSql($qry);
        $patient_notes->setDivId("notes_cont"); //important
        $patient_notes->setDivClass('');
        $patient_notes->setRowid('patient_notes_id');
        $patient_notes->setCaption("Patient nursing notes");
        $patient_notes->setShowHeaderRow(false);
        $patient_notes->setShowFilterRow(false);
        $patient_notes->setColNames(array("ID","",""));
        $patient_notes->setRowNum(25);
        $patient_notes->setColOption("patient_notes_id", array("search" => false, "hidden" => true));
        $patient_notes->setColOption("dte", array("search" => false, "hidden" => false, "width" => 70));
        $patient_notes->setColOption("notes", array("search" => false, "hidden" => false, "width" => 70));
        $patient_notes->gridComplete_JS = "function() {
        $('#notes_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
           window.location='".site_url("form/edit/patient_notes")."/'+rowId+'?CONTINUE=patient/view/".$pid."';
        });
        }";
        $patient_notes->setOrientation_EL("L");
        return $patient_notes->render(false);
    }

    private function loadLabOrder($pid)
    {
        $qry
            = "SELECT lab_order.LAB_ORDER_ID ,
	SUBSTRING(lab_order.OrderDate,1,10) as dte,
	lab_order.TestGroupName,
	lab_order.Status,
	lab_order.Remarks
	FROM lab_order
	where (lab_order.PID ='" . $pid . "') and (lab_order.Active = 1)";
        $this->load->model('mpager','lab_order_page');
        $lab_order_page = $this->lab_order_page;
        $lab_order_page->setSql($qry);
        $lab_order_page->setDivId("lab_cont"); //important
        $lab_order_page->setDivClass('');
        $lab_order_page->setRowid('LAB_ORDER_ID');
        $lab_order_page->setCaption("Latest lab results");
        $lab_order_page->setShowHeaderRow(false);
        $lab_order_page->setShowFilterRow(false);
        $lab_order_page->setColNames(array("ID", "", "", "", ""));
        $lab_order_page->setRowNum(25);
        $lab_order_page->setColOption("LAB_ORDER_ID", array("search" => false, "hidden" => true, "width" => 30));
        
        $lab_order_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 80));
        $lab_order_page->setColOption("TestGroupName", array("search" => false, "hidden" => false, "width" => 120));
        $lab_order_page->setColOption("Status", array("search" => false, "hidden" => false, "width" => 70));

        $lab_order_page->gridComplete_JS = "function() {
		$('div[id ^= \"pager\"]').hide();
        $('#lab_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            window.location='".site_url("laboratory/order")."/'+rowId+'?CONTINUE=patient/view/$pid';
        });
        }";
        $lab_order_page->setOrientation_EL("L");
        return $lab_order_page->render(false);
    }

    private function loadPrescription($pid)
    {
             $qry
            = "SELECT SUBSTRING(pri.CreateDate,1,10) as dte ,d.Name,pri.HowLong,pri.Dosage,pri.Frequency FROM
			prescribe_items as pri,
			opd_presciption as pr,
			who_drug as d
			where (pri.PRES_ID = pr.PRSID) and (pri.Active = 1)and (pr.Active = 1) and (pr.Status = 'Dispensed') and (pr.PID = " . $pid
            . ") and (d.wd_id = pri.DRGID)";
   
        $this->load->model('mpager','prescription_page');
        $prescription_page = $this->prescription_page;
        $prescription_page->setSql($qry);
        $prescription_page->setDivId("pre_cont"); //important
        $prescription_page->setDivClass('');
        //$lab_order_page->setRowid('LAB_ORDER_ID');
        $prescription_page->setCaption("Medication history");
        $prescription_page->setShowHeaderRow(false);
        $prescription_page->setShowFilterRow(false);
        $prescription_page->setColNames(array("ID", "", "", "", ""));
        $prescription_page->setRowNum(25);
        //$lab_order_page->setColOption("LAB_ORDER_ID",array("search"=>false,"hidden" => false,"width"=>30));
        $prescription_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 50));
        $prescription_page->setColOption("Name", array("search" => false, "hidden" => false, "width" => 190));
        $prescription_page->setColOption("HowLong", array("search" => false, "hidden" => false, "width" => 70));
        $prescription_page->setColOption("Dosage", array("search" => false, "hidden" => false, "width" => 30));
        $prescription_page->setColOption("Frequency", array("search" => false, "hidden" => false, "width" => 40));


        $prescription_page->gridComplete_JS
            = "function() {
		$('div[id ^= \"pager\"]').hide();
        }";
        $prescription_page->setOrientation_EL("L");
        return $prescription_page->render(false);
    }

    private function loadAttachment($pid)
    {
        $qry
            = "SELECT attachment.ATTCHID ,
	SUBSTRING(attachment.CreateDate,1,10) as dte,
	attachment.Attach_Type,
	attachment.Attach_Description
	FROM attachment
	where (attachment.PID ='" . $pid . "') and (attachment.Active = 1)";
        $this->load->model('mpager','attach_page');
        $attach_page = $this->attach_page;
        $attach_page->setSql($qry);
        $attach_page->setDivId("attach_cont"); //important
        $attach_page->setDivClass('');
        $attach_page->setRowid('ATTCHID');
        $attach_page->setCaption("Files attached to the patient record");
        $attach_page->setShowHeaderRow(false);
        $attach_page->setShowFilterRow(false);
        $attach_page->setColNames(array("ID", "", "", ""));
        $attach_page->setRowNum(25);
        $attach_page->setColOption("ATTCHID", array("search" => false, "hidden" => true, "width" => 30));
        $attach_page->setColOption("dte", array("search" => false, "hidden" => false, "width" => 60));
        //$attach_page->setColOption("Attach_Name", array("search" => false, "hidden" => TRUE, "width" => 70));
        $attach_page->setColOption("Attach_Type", array("search" => false, "hidden" => false, "width" => 60));
        $attach_page->gridComplete_JS
            = "function() {
		$('div[id ^= \"pager\"]').hide();
        $('#attach_cont .jqgrow').mouseover(function(e) {
            var rowId = $(this).attr('id');
            $(this).css({'cursor':'pointer'});
        }).mouseout(function(e){
        }).click(function(e){
            var rowId = $(this).attr('id');
            var params = 'menubar=no,location=no,resizable=yes,scrollbars=yes,status=no,width='+screen.availWidth+',height='+screen.availHeight;
		    var url = '".site_url("attach/view/")."/'+rowId;
			window.open('' + url + '', 'lookUpW', params);
        });
    }";
        $attach_page->setOrientation_EL("L");
        return $attach_page->render(false);
    }

    public function search()
    {
        $this->load->model('mpager');
        $pager2 = $this->mpager;
        $pager2->setSql(
            "select p.PID,p.HIN, p.Full_Name_Registered, p.Personal_Used_Name, p.DateOfBirth, p.Gender, p.Personal_Civil_Status, p.NIC, p.Address_Village,p.Triage_Level"
                . ",c.name as clinic_name, cp.next_visit_date "
                . "from patient as p "
                . "LEFT JOIN clinic_patient cp ON p.PID = cp.PID "
                . "LEFT JOIN clinic c ON cp.clinic_id = c.clinic_id "
                ." where p.Active = 1 "
                . "Group by p.PID "
               // . "Order by cp.next_visit_date" 

                
        );
        $pager2->setDivId('tablecont1'); //important
        $pager2->setDivStyle('width:95%;margin:0 auto;');
        $pager2->setRowid('PID');
//        $pager2->setWidth("95%");
        $tools = "<input class=\'formButton\' onclick=getSearchText(); type=\'button\' ID=\'spid\' value=\'Search Patient by ID\'>";
        $pager2->setCaption($tools);
        $pager2->setSortname("CreateDate");
        $pager2->setColNames(
            array("Id","HIN", "Name", "Initials", "Date of Birth", "Gender", "Civil Status", "NIC", "Village","C Date","Clinic","Triage_Level")
        );
        $pager2->setColOption("PID", array("search" => true, "hidden" => true, "height" => 100,"width"=>50));
        //$pager2->setColOption("LPID", array("search" => true, "width" => 50));
        $pager2->setColOption("Full_Name_Registered", array("search" => true, "width" => 300));
        $pager2->setColOption("Personal_Used_Name", array("search" => true, "width" => 50));
        //$pager2->setColOption("DateOfBirth", array("stype" => "text", "searchoptions" => array("dataInit_JS" => "datePicker_REFID","defaultValue"=>"")));
        $pager2->setColOption(
            "Gender", array("stype" => "select", "searchoptions" => array("value" => ":All;Male:Male;Female:Female"))
        );
        //"Single","Married","Divorced","Widow","UnKnown"
        $pager2->setColOption(
            "Personal_Civil_Status", array("stype"         => "select",
                                           "searchoptions" => array("value"        => ":All;Single:Single;Married:Married;Divorced:Divorced;Widow:Widow;UnKnown:UnKnown",
                                                                    "defaultValue" => "All"))
        );
        $pager2->setColOption("Triage_Level", array("search" => true,  "height" => 100,"width"=>30 ,"css"=>"text-align:left"));
        //$pager2->setColOption("CreateDate", array("stype" => "text", "searchoptions" => array("dataInit_JS" => "datePicker_REFID","defaultValue"=>"")));
        $pager2->setSortname('PID');
        $pager2->gridComplete_JS
            = "function() {
            var c = null;
            $('.jqgrow').mouseover(function(e) {
                var rowId = $(this).attr('id');
                c = $(this).css('background');
                $(this).css({'background':'#FFFFFF','cursor':'pointer'});
            }).mouseout(function(e){
            $(this).css('background',c);
            }).mousedown(function(e){
                var rowId = $(this).attr('id');
                window.location='" . base_url() . "index.php/patient/view/'+rowId;
            });
                
            }";
        $pager2->setOrientation_EL("L");
        $data['pager'] = $pager2->render(false);
        $this->load->vars($data);
        $this->load->view('patient_search', 1);
    }
	public function print_hin($hin){
		return substr($hin, 0, 4).'-'.substr($hin, 4, 6)."-".substr($hin, 10, 1);
	}
        public function print_phn($hin){
		return $hin;
	}
        
	public function update_hin(){
		if ($this->session->userdata("UserGroup")!="Programmer"){
			echo "-NO ACCESS-";
			return;
		}
		$this->load->model("mpatient");
		$this->load->model("mpersistent");
		
		
		$data["Patient_list"] = $this->mpatient->get_all_patient();
		echo "UPDATING HIN ".count($data["Patient_list"])."<hr>";
		echo "<table border=1>";
		for ($i=0;$i<count($data["Patient_list"]);++$i){
			echo "<tr>";
				echo "<td>";
					echo $data["Patient_list"][$i]["PID"];
				echo "</td>";
				echo "<td>";
					$HIN = $this->get_hin($data["Patient_list"][$i]["PID"]);
					$hstatus = $this->mpersistent->update("patient", "PID", $data["Patient_list"][$i]["PID"], array("HIN"=>$HIN));
					//echo chunk_split($HIN, 4, '-');
					//echo $HIN."--";
					echo $this->print_hin($HIN);
					 // substr($HIN, 9);
				echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
    public function save()
    {
        //print_r($_POST);
        $frm = 'patient';
        if (!file_exists('application/forms/' . $frm . '.php')) {
            die("Form " . $frm . "  not found");
        }
        include 'application/forms/' . $frm . '.php';
        $data["form"] = $form;
        //print_r($data);
        $this->load->helper('form');
        $this->load->library('form_validation');
        $this->load->database();
        $this->load->model("mpersistent");
        $this->form_validation->set_error_delimiters('<span class="field_error">', '</span>');
        for ($i = 0; $i < count($form["FLD"]); ++$i) {
            $this->form_validation->set_rules(
                $form["FLD"][$i]["name"], '"' . $form["FLD"][$i]["label"] . '"', $form["FLD"][$i]["rules"]
            );
        }
        $this->form_validation->set_rules($form["OBJID"]);
        $this->form_validation->set_rules("year", "Age", "numeric|xss_clean");
        $this->form_validation->set_rules("month", "Age", "numeric|xss_clean");
        $this->form_validation->set_rules("day", "Age", "numeric|xss_clean");

        if ($this->form_validation->run() == FALSE) {
            $this->load->vars($data);
            echo Modules::run('form/create', 'patient');
        } else {
            //$sve_data = array();
            //for ( $i=0; $i < count($form["FLD"]); ++$i ){
            //$sve_data[$form["FLD"][$i]["name"]] = $this->input->post($form["FLD"][$i]["name"]);
            //}
            $year = $this->input->post("year");
            $month = $this->input->post("month");
            $day = $this->input->post("day");

            if ($this->input->post("DateOfBirth") == "") {
                $dob = date('Y-m-d', mktime(0, 0, 0, date("m") - $month, date("d") - $day, date("Y") - $year));
            } else {
                $dob = $this->input->post("DateOfBirth");
            }
            $sve_data = array(
                'Personal_Title'        => $this->input->post("Personal_Title"),
                'Full_Name_Registered'  => strtoupper($this->input->post("Full_Name_Registered")),
                'Personal_Used_Name'    => strtoupper($this->input->post("Personal_Used_Name")),
                'Gender'                => $this->input->post("Gender"),
                'Personal_Civil_Status' => $this->input->post("Personal_Civil_Status"),
                'DateOfBirth'           => $dob,
                'NIC'                   => $this->input->post("NIC"),
                'Ref_No'                => $this->input->post("Ref_No"),
                'Telephone'             => $this->input->post("Telephone"),
                'occupation'             => $this->input->post("occupation"),
                'Address_Street'        => $this->input->post("Address_Street"),
                'Address_Street1'       => $this->input->post("Address_Street1"),
                'Address_Village'       => $this->input->post("Address_Village"),
                'Address_District'      => $this->input->post("Address_District"),
                'Address_DSDivision'    => $this->input->post("Address_DSDivision"),
                'guardian_address'      => $this->input->post("guardian_address"),
                'guardian_contact'      => $this->input->post("guardian_contact"),
                'guardian_relationship' => $this->input->post("guardian_relationship"),
                'guardian_name'         => $this->input->post("guardian_name"),
                'Remarks'               => $this->input->post("Remarks"),
                'HID'                   => $this->session->userdata('HID'),
		'Guardian_Mobile'           => $this->input->post("Guardian_Mobile"),
                'Guardian_Tel'              => $this->input->post("Guardian_Tel"),
                'Guardian_Address_Street'   => $this->input->post("Guardian_Address_Street"),
                'Guardian_Address_Village'  => $this->input->post("Guardian_Address_Village")
                

            );
            $id = $this->input->post($form["OBJID"]);
            $status = false;
			
            if ($id > 0) {
                $status = $this->mpersistent->update($frm, $form["OBJID"], $id, $sve_data);
                $this->session->set_flashdata(
                    'msg', 'REC: ' . ucfirst(strtolower($this->input->post("Full_Name_Registered"))) . ' Updated'
                );
				if ( $status){
					header("Status: 200");
					if (isset($_POST["CONTINUE"])){
						header("Location: ".site_url($_POST["CONTINUE"])); 
						return;
					}
					else{
						header("Location: ".site_url($form["NEXT"].'/'.$status));
						return;
					}
				}
            } else {
                $sve_data ['LPID'] = $this->get_unique_id($this->input->post("DateOfBirth"));
                $status = $this->mpersistent->create($frm, $sve_data);
				$HIN = $this->get_hin($status);
				$hstatus = $this->mpersistent->update($frm, "PID", $status, array("HIN"=>$HIN));
                $this->session->set_flashdata(
                    'msg', 'REC: ' . ucfirst(strtolower($this->input->post("Full_Name_Registered"))).$HIN . ' created'
                );
				if ( $status>0){
					//echo Modules::run($form["NEXT"], $status);
					header("Status: 200");
					if (isset($_POST["CONTINUE"]) && $_POST["CONTINUE"]!=''){
						header("Location: ".site_url($_POST["CONTINUE"]));
						return;
					}
					else{
						header("Location: ".site_url($form["NEXT"].'/'.$status));
						return;
					}
				}
            }
            echo "ERROR in saving";
        }
    }

    public function nic_check($nic)
    {
        if ($nic == "") {
            return TRUE;
        }
        $reg = '/^(\d\d\d\d\d\d\d\d\d[vVxX0-9])|(\d{1,12})$/';
        if (preg_match($reg, $nic) == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


    public function get_unique_id($dob)
    {
        $yyyy = substr($dob, 0, 4);
        $mm = substr($dob, 5, 2);
        $dd = substr($dob, 8, 2);
        //echo $yyyy.$mm.$dd.substr(number_format(str_replace(".","",microtime(true)*rand()),0,'',''),0,14);
        //echo $yyyy.$mm.$dd.time();
        //echo $yyyy.$mm.$dd.substr(number_format(str_replace(".","",microtime(true)*rand()),0,'',''),0,8);
        return
            //$yyyy . $mm . $dd . substr(number_format(str_replace(".", "", microtime(true) * rand()), 0, '', ''), 0, 8);
              $yyyy . $mm . $dd .( substr(number_format(str_replace(".", "", microtime(true) * rand(0,9999999)), 0, '', ''), 0, 8));
    }

    public function get_initial()
    {
        return ucwords($this->mpersistent->get_value("Personal_Used_Name"));
    }

    public function get_name()
    {
        return ucfirst($this->mpersistent->get_value("Full_Name_Registered"));
    }

    public function get_address()
    {
        $address = "";
        if (ucfirst($this->mpersistent->get_value("Address_Street")) != "") {
            $address
                .= ucfirst($this->mpersistent->get_value("Address_Street")) . "<br>";
        }
        if (ucfirst($this->mpersistent->get_value("Address_Street1")) != "") {
            $address
                .= ucfirst($this->mpersistent->get_value("Address_Street1")) . "<br>";
        }
        if (ucfirst($this->mpersistent->get_value("Address_Village")) != "") {
            $address
                .= ucfirst($this->mpersistent->get_value("Address_Village")) . "<br>";
        }
        if (ucfirst($this->mpersistent->get_value("Address_DSDivision")) != "") {
            $address
                .= ucfirst($this->mpersistent->get_value("Address_DSDivision")) . "<br>";
        }
        if (ucfirst($this->mpersistent->get_value("Address_District")) != "") {
            $address
                .= ucfirst($this->mpersistent->get_value("Address_District")) . "<br>";
        }
        return $address;
    }

    public function get_full_name()
    {
        $fName = "";
        $fName .= ucwords(
            $this->mpersistent->get_value("Personal_Title") . " " . $this->mpersistent->get_value("Personal_Used_Name")
        );
        $fName .= " " . $this->mpersistent->get_value("Full_Name_Registered") . " ";
        return $fName;
    }

    public function get_civil_status()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return ucwords($this->mpersistent->get_value("Personal_Civil_Status"));
    }

    public function get_date_of_birth()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("DateOfBirth");
    }

    public function get_Passport()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Passport");
    }
    public function get_NIC()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("NIC");
    }

    public function get_gender()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Gender");
    }

    public function get_Guardian_Name()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Guardian_Name");
    }
    public function get_Guardian_Mobile()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Guardian_Mobile");
    }
    public function get_Guardian_Tel()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Guardian_Tel");
    }
    public function get_Guardian_Address_Street()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Guardian_Address_Street");
    }
    public function get_Guardian_Address_Village()
    {
        //if (!$this->Fields[$this->ObjField]) return NULL;
        return $this->mpersistent->get_value("Guardian_Address_Village");
    }    
    
    public function get_age($dob)
    {
       /* $date1 = $dob;
        $date2 = date('Y/m/d');

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        return array('years' => $years, 'months' => $months, 'days' => $days);*/
        
        $bday = new DateTime($dob);
        $today = new DateTime('00:00:00'); //for the current date


        $diff = $today->diff($bday);

        return array('years' => $diff->y, 'months' => $diff->m, 'days' => $diff->d);
        //printf('%d years, %d month, %d days', $diff->y, $diff->m, $diff->d);
        
    }
    
    	public function get_previous_pacs($pid,$continue,$mode='HTML'){
		$this->load->model("mpatient");
		$data = array();
		$data["patient_pacs_list"] = $this->mpatient->get_pacs_list($pid);
		$data["continue"] = $continue;
		if ($mode == "HTML"){
			$this->load->vars($data);
			$this->load->view('patient_previous_pacs');
		}
		else{
			return $data["patient_pacs_list"];
		}
	}
        
        	public function encryptor(){
		if ($this->session->userdata("UserGroup")!="Programmer"){
			echo "-NO ACCESS-";
			return;
		}
                
            if($_FILES){    
            $allowed = array('text/x-log');
            $mime = mysql_real_escape_string($_FILES['file']['type']);
            if (!in_array($mime, $allowed)) {
                echo 'File Type Not Allowed';
                return FALSE;
            }

            $file = file_get_contents($_FILES['file']['tmp_name']); //SQL Injection defence!
            $image_name = mysql_real_escape_string($_FILES['file']['name']);
            $size = intval($_FILES['file']['size']);
                

                
                //$handle = fopen("/home/icta/Desktop/log-2017051611.log", "r");                
                $handle = fopen($_FILES["file"]["tmp_name"], "r");
                //print_r($handle);
                if ($handle) {
                    while (($line = fgets($handle)) !== false) {
                        $key=$this->config->item('encryption_key');
                        $dec = $this->encrypt->decode(trim($line),$key);
                        echo $dec;
                        echo "<br>";
                    }

                    fclose($handle);
                } else {
                    echo "error opening the file";
                }
                }else{
                $this->load->view('encryptor');
                }

	}
}


function date_difference($startDate, $endDate)
{

    $startDate = strtotime($startDate);
    $endDate = strtotime($endDate);

    $years = $months = $days = 0;

    $two = $startDate;
    $one = $endDate;
    $invert = false;
    if ($one > $two) {
        list($one, $two) = array($two, $one);
        $invert = true;
    }

    $key = array("y", "m", "d", "h", "i", "s");
    $a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
    $b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

    $result = array();
    $result["y"] = $b["y"] - $a["y"];
    $result["m"] = $b["m"] - $a["m"];
    $result["d"] = $b["d"] - $a["d"];
    $result["h"] = $b["h"] - $a["h"];
    $result["i"] = $b["i"] - $a["i"];
    $result["s"] = $b["s"] - $a["s"];
    $result["invert"] = $invert ? 1 : 0;
    $result["days"] = intval(abs(($one - $two) / 86400));

    return array($result["y"], $result["m"], $result["d"]);
}


//////////////////////////////////////////

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */