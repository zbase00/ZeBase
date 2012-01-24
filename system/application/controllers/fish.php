<?php
/* 
  ZeBase 
  Copyright 2010 Purdue University

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
 
   @version    1.0.3, 2011-10-01
 */
class fish extends Controller { 
	function fish()
	{
		parent::Controller();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('session');
	} 
	function login(){  
		$this->load->library('SimpleLoginSecure');
		if($this->session->userdata('logged_in')) {  
			redirect('/fish','refresh');
		}else{
			$data['attempt'] = $this->uri->segment(3); 
			$this->load->view('login_view',$data); 
			return; 
		} 
	} 
	function send_login(){ 
		$this->load->library('SimpleLoginSecure'); 
		if($this->simpleloginsecure->login($_POST['username'], $_POST['pass'])) {
			redirect('/fish','refresh');
			// 'passed';	
		}else{
			redirect('/fish/login/f','refresh');			
		}
	}
	function logout(){
		$this->load->library('SimpleLoginSecure');
		// logout
		$this->simpleloginsecure->logout();
		$this->load->view('login_view',$data);
	}
	function edit_mut_trans(){
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url);
		$batch_ID = $this->uri->segment(3);
		$attributes = array('id' => 'form_ID','name' => 'record_form');
		$html .=  form_open('fish/db_update_mut_trans', $attributes);
	 		$html .=  '<h2 style="margin-left:20px;">Batch Number: ' . $batch_ID . '</h2>
			<div id="standard_box" style=" margin:20px;" >';
			$this->db->where('batch_ID', $batch_ID);
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left');
			$query = $this->db->get('mutant_assoc'); 
			$html .=  '<div style="float:left; padding-right:60px;"><h4>Mutants</h4></div><div><a href="#" onclick="document.record_form.submit();" class="jq_buttons" style=" font-size:12px;">Update</a></div><br>';
			if ($query->num_rows() > 0){				
				foreach ($query->result() as $row){ 
					$html .=  '<div style=" width:380px;"><input type="hidden" name="mutantassoc_' . $row->mutant_assoc_ID . '" value="' . $row->mutant_assoc_ID . '">
					<div id="plain_box"><h4 style=" font-size:.8em">' . $row->mutant;	
					$html .= '</h4>';
					$html .= ' +/+ <input type="checkbox" name="' . $row->mutant_assoc_ID . '_mutant_genotype_wildtype" value="1"';
					 if($row->mutant_genotype_wildtype != ""){
						 $html .= ' checked ';
					 }
					$html .= '>'; 
					$html .= ' +/- <input type="checkbox" name="' . $row->mutant_assoc_ID . '_mutant_genotype_heterzygous" value="1" ';
				 	if($row->mutant_genotype_heterzygous != ""){
						 $html .= ' checked ';
					 }
					$html .= '>'; 
					$html .= ' -/- <input type="checkbox" name="' . $row->mutant_assoc_ID . '_mutant_genotype_homozygous"  value="1"';
					if($row->mutant_genotype_homozygous != ""){
						 $html .= ' checked ';
					 }
					$html .= '>'; 
					$html .=  '</div><br>';
				}
			}
		 $html .=  '<h4>Transgenes</h4>';
		 $this->db->where('batch_ID', $batch_ID);
		$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left');
		$query = $this->db->get('transgene_assoc');
		if ($query->num_rows() > 0){				
			foreach ($query->result() as $row){ 
				$html .=  '<div style=" width:380px;"><input type="hidden" name="transgeneassoc_' . $row->transgene_assoc_ID . '" value="' . $row->transgene_assoc_ID . '">
				<div id="plain_box"><h4 style=" font-size:.8em">' . $row->transgene;	
				$html .= '</h4>';
				$html .= ' +/+ <input type="checkbox" name="' . $row->transgene_assoc_ID . '_transgene_genotype_wildtype" value="1"';
				 if($row->transgene_genotype_wildtype != ""){
					 $html .= ' checked ';
				 }
				$html .= '>'; 
				$html .= ' +/- <input type="checkbox" name="' . $row->transgene_assoc_ID . '_transgene_genotype_heterzygous" value="1" ';
				if($row->transgene_genotype_heterzygous != ""){
					 $html .= ' checked ';
				 }
				$html .= '>'; 
				$html .= ' -/- <input type="checkbox" name="' . $row->transgene_assoc_ID . '_transgene_genotype_homozygous"  value="1"';
				if($row->transgene_genotype_homozygous != ""){
					 $html .= ' checked ';
				 }
				$html .= '>'; 
				$html .=  '</div><br>';
			}
		} 
		$html .= '</div></form>';
		
		echo $html;
	}
	function modify_line(){  
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url);
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$batch_ID = $direct_array[1];
			$this->db->where('batch_ID', $batch_ID);
			$query = $this->db->get('fish');
			$data['batch_found']  = "";
			if ($query->num_rows() > 0){
  				$selected_batch = $query->row_array();
				$data['batch_found'] = 1;
			} 
			$this->db->select("room");
			$this->db->distinct(); 	
			$this->db->where("room not like ", ""); 	
			$data['all_rooms'] = $this->db->get('tank');
			
			$this->db->where('batch_ID', $selected_batch['father_ID']);
			$data['father'] = $this->db->get('fish');
			$this->db->where('batch_ID', $selected_batch['mother_ID']);
			$data['mother'] = $this->db->get('fish'); 
			$this->db->order_by("strain", "asc"); 
			$data['all_strains'] = $this->db->get('strain');			
			$this->db->order_by("promoter", "asc"); 
			$data['all_transgenes'] = $this->db->get('transgene'); 
			
			$this->db->where('batch_ID', $batch_ID);
			$this->db->join('transgene', 'transgene.transgene_ID = transgene_assoc.transgene_ID','left');
			$data['selected_transgenes'] = $this->db->get('transgene_assoc');  
			$this->db->where('batch_ID', $batch_ID);
			$this->db->join('mutant', 'mutant.mutant_ID = mutant_assoc.mutant_ID','left');
			$data['selected_mutants'] = $this->db->get('mutant_assoc'); 
			
			$this->db->order_by("mutant", "asc"); 
			$data['all_mutants'] = $this->db->get('mutant');  
			$this->db->order_by("last_name", "asc");  
			
			$data['all_users'] = $this->db->get('users');  
			//$data['all_tanks'] = $this->db->get('tank'); 
			$this->db->select('(\'empty\'),tank_ID,location');
			$this->db->from('tank');
			$_SESSION['datatables_select'] = "";
			$_SESSION['datatables_from'] = "";
			$_SESSION['datatables_where'] = "";
			$_SESSION['datatables_fields'] = "";
			$_SESSION['datatables_buttons']  = "";
			$_SESSION['datatables_field_wtables'] = "";
			$from = $this->db->ar_from[0];  
			$select = "";  
			foreach ($this->db->ar_select as $selectvar){
				$select[] = $selectvar;	
			}  
			$_SESSION['datatables_select'] = $select; 
			$_SESSION['datatables_field_wtables'] =  explode(',', '(\'empty\'),tank_ID,location');
			$_SESSION['datatables_from'] =  $from;
			$_SESSION['datatables_fields'] = array("('empty')",'location');
			$_SESSION['datatables_buttons']  = '<a href="#"  onclick="displayVals(\'xxxxxxtank_IDxxxxxxx\',\'xxxxxxlocationxxxxxxx\',\'add_tank\');"><img border=0 src="' . $url . 'assets/Pics/Symbol-Add_48.png" width="16" ></a>';
			$this->db->limit(1);
			$this->db->get(); 
						
			$this->db->select("tank.*, cast(mid(location,1,LOCATE('-',location)-1) as UNSIGNED) as sort_1,
		  cast(mid(location,LOCATE('-',location)+1,LOCATE('-',location,LOCATE('-',location))-1) as UNSIGNED) as sort_2,
		  cast(mid(location,LOCATE('-',location,LOCATE('-',location)+1)+1) as UNSIGNED) as sort_3",FALSE);
			$this->db->from('tank');
			$this->db->join('tank_assoc', 'tank.tank_ID = tank_assoc.tank_ID','left');
			$this->db->where('batch_ID', $selected_batch['batch_ID']);
			$this->db->order_by("sort_1,sort_2,sort_3", 'asc');
			$query = $this->db->get(); 
			if ($query->num_rows() > 0){
				$index = "0";
				foreach ($query->result_array() as $temp){	 
					$data['current_tanks'][$index] = $temp;  
					$this->db->where('tank_ID', $temp['tank_ID']);
					$tank_count_check = $this->db->get('tank_assoc');
					if ($tank_count_check->num_rows() > 0){
						foreach ($tank_count_check->result_array() as $temp_c){	 
							$data['current_tanks'][$index]['multiple_batch'][] = $temp_c['batch_ID'];
						}
					} 
					$index++;
				}
			}
			  
			$url = $this->config->item('base_url');
			output_fields($selected_batch, $batch_ID,$data,$url);
		}elseif ($direct_array[0] == "n"){		 	 
			 $this->db->order_by("strain", "asc"); 
			$data['all_strains'] = $this->db->get('strain');			
			$this->db->order_by("promoter", "asc"); 
			$data['all_transgenes'] = $this->db->get('transgene');
			$this->db->order_by("mutant", "asc"); 
			$data['all_mutants'] = $this->db->get('mutant');
			$this->db->order_by("last_name", "asc"); 
			$data['all_users'] = $this->db->get('users');
	 		
			$this->db->select("room");
			$this->db->distinct(); 	
			$this->db->where("room not like ", ""); 	
			$data['all_rooms'] = $this->db->get('tank');
			
			$this->CI =& get_instance();
			$data['loggedin_user_ID'] = $this->CI->session->userdata('user_ID'); 
			output_fields_new($selected_batch, $batch_ID,$data);
		}elseif ($direct_array[0] == "r"){
			$batch_ID = $direct_array[1];
			$this->db->where('batch_ID', $batch_ID);
			$query = $this->db->get('fish');
			if ($query->num_rows() > 0){
  				$selected_batch = $query->row_array();
			}	
			output_fields_remove($selected_batch, $batch_ID);			
		}
	}
	function modify_line_wq(){  
	require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url);
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$entryID = $direct_array[1]; 
			$this->db->where('entry_ID', $entryID);
			$query = $this->db->get('water_quality');
			if ($query->num_rows() > 0){
  				$selected_entry = $query->row_array();
			}	
			output_wq_fields($selected_entry,$url);
		}elseif ($direct_array[0] == "n"){
			output_wq_fields_new();
		}elseif ($direct_array[0] == "r"){
			$entry_ID = $direct_array[1];
			$this->db->where('entry_ID', $entry_ID);
			$query = $this->db->get('water_quality');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			output_fields_wq_remove($selected, $entry_ID);	
		}
	}
	function modify_users(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url);
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$user_ID = $direct_array[1];
			$this->db->where('user_ID', $user_ID);
			$query = $this->db->get('users');
			if ($query->num_rows() > 0){
  				$selected_user = $query->row_array();
			}
			$this->db->order_by("lab_name", "asc"); 
			$labs = $this->db->get('labs');
			output_user_fields($selected_user,$url,$labs);
		}elseif ($direct_array[0] == "n"){		 	 
			$this->db->order_by("lab_name", "asc"); 
			$labs = $this->db->get('labs');			 
			output_user_fields_new($url,$labs);
		}elseif ($direct_array[0] == "r"){
			$user_ID = $direct_array[1];
			$this->db->where('user_ID', $user_ID);
			$this->db->from('users');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$query = $this->db->get();
			if ($query->num_rows() > 0){
  				$selected_user = $query->row_array();
			}	
			user_fields_remove($selected_user);			
		}
	} 
	function modify_mutant(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$mutant_ID = $direct_array[1];
			$this->db->where('mutant_ID', $mutant_ID);
			$query = $this->db->get('mutant');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			} 
			output_mutant_fields($selected,$url);
		}elseif ($direct_array[0] == "n"){ 
			output_mutant_fields_new($url);
		}elseif ($direct_array[0] == "r"){
			$mutant_ID = $direct_array[1];
			$this->db->where('mutant_ID', $mutant_ID);
			$query = $this->db->get('mutant');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			mutant_fields_remove($selected);			
		}
	} 
	function modify_strain(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$strain_ID = $direct_array[1];
			$this->db->where('strain_ID', $strain_ID);
			$query = $this->db->get('strain');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			} 
			output_strain_fields($selected,$url);
		}elseif ($direct_array[0] == "n"){ 
			output_strain_fields_new($url);
		}elseif ($direct_array[0] == "r"){
			$strain_ID = $direct_array[1];
			$this->db->where('strain_ID', $strain_ID);
			$query = $this->db->get('strain');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			strain_fields_remove($selected);			
		}
	} 
	function modify_lab(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$lab_ID = $direct_array[1];
			$this->db->where('lab_ID', $lab_ID);
			$query = $this->db->get('labs');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}  
			output_lab_fields($selected,$url);
		}elseif ($direct_array[0] == "n"){ 
			output_lab_fields_new($url);
		}elseif ($direct_array[0] == "r"){
			$lab_ID = $direct_array[1];
			$this->db->where('lab_ID', $lab_ID);
			$query = $this->db->get('labs');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			lab_fields_remove($selected);			
		}
	} 
	function modify_tank(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$tank_ID = $direct_array[1];
			$this->db->where('tank_ID', $tank_ID);
			$query = $this->db->get('tank');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			} 
			output_tank_fields($selected,$url);
		}elseif ($direct_array[0] == "n"){ 
			output_tank_fields_new($url);
		}elseif ($direct_array[0] == "r"){
			$tank_ID = $direct_array[1];
			$this->db->where('tank_ID', $tank_ID);
			$query = $this->db->get('tank');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			tank_fields_remove($selected);			
		}
	} 
	function modify_search(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3)); 
		$search_ID = $direct_array[1];
		$this->db->where('search_ID', $search_ID);
		$query = $this->db->get('saved_searches');
		if ($query->num_rows() > 0){
			$selected = $query->row_array();
		}	
		search_fields_remove($selected);			
		 
	} 
	function modify_transgene(){		 
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$direct_array = explode('_',$this->uri->segment(3));
		if ($direct_array[0] == "u"){			
			$transgene_ID = $direct_array[1];
			$this->db->where('transgene_ID', $transgene_ID);
			$query = $this->db->get('transgene');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			} 
			output_transgene_fields($selected,$url);
		}elseif ($direct_array[0] == "n"){ 
			output_transgene_fields_new($url);
		}elseif ($direct_array[0] == "r"){
			$transgene_ID = $direct_array[1];
			$this->db->where('transgene_ID', $transgene_ID);
			$query = $this->db->get('transgene');
			if ($query->num_rows() > 0){
  				$selected = $query->row_array();
			}	
			transgene_fields_remove($selected);			
		}
	} 
	function add_tanks(){
		require 'function.php';
		$temp = $_POST['tanks'];
		$tanks = array_unique($temp);
			if (is_array($tanks)){
				foreach ($tanks as $value){
					$data = "";
					$data = array(
						'batch_ID' => $_POST['batch_ID'],
						'tank_ID' => $value,
						'description' => ''					 
					);	  
					$this->db->insert('tank_assoc', $data);	
					//echo $this->db->_error_message();	  
				}
			}	
			redirect('fish/modify_line/u_' . $_POST['batch_ID'], 'refresh');
	}
	function remove_tanks(){
		require 'function.php';
		$temp = $_POST['tanks'];
		$tanks = array_unique($temp);
			if (is_array($tanks)){
				foreach ($tanks as $value){	 
					$this->db->where('tank_ID', $value);
					$this->db->delete('tank_assoc'); 
				}
			} 
			redirect('fish/modify_line/u_' . $_POST['batch_ID'], 'refresh');
	}
	function moreinfo(){
		$line_ID =  $this->uri->segment(3) ;
		$this->db->where('line_ID', $line_ID);
		$query = $this->db->get('line_item');
		if ($query->num_rows() > 0){
			$line_item = $query->row_array();
		}
		$html .=  '<div style="padding-top:50px; padding-left:50px; background-color:#F5EEDE; height:630px;">';
		$html .= '<h3>Description:</h3> ' . $line_item['description'];
		$html .= '<h3>Comment:</h3> ' . $line_item['comment'];
		$html .='</div>';
		echo $html;
	}
	function db_update_mut_trans(){  
			if (is_array($_POST)){
				foreach ($_POST as $key => $value){  
					if (strstr($key,'mutantassoc')){  
						$temparray = explode('_',$_POST[$key]);
						$assoc_ID = $temparray[0];  
						if ($assoc_ID != ""){
							$mutant_geno = array(
								'mutant_genotype_wildtype' => $_POST[$assoc_ID . '_mutant_genotype_wildtype'],
								'mutant_genotype_heterzygous' => $_POST[$assoc_ID . '_mutant_genotype_heterzygous'],
								'mutant_genotype_homozygous' => $_POST[$assoc_ID . '_mutant_genotype_homozygous']							 
							);
							$this->db->update('mutant_assoc', $mutant_geno, "mutant_assoc_ID = " . $assoc_ID);
						}
					}
					if (strstr($key,'transgeneassoc')){  
						$temparray = explode('_',$_POST[$key]);
						$assoc_ID = $temparray[0];  
						if ($assoc_ID != ""){
							$transgene_geno = array(
								'transgene_genotype_wildtype' => $_POST[$assoc_ID . '_transgene_genotype_wildtype'],
								'transgene_genotype_heterzygous' => $_POST[$assoc_ID . '_transgene_genotype_heterzygous'],
								'transgene_genotype_homozygous' => $_POST[$assoc_ID . '_transgene_genotype_homozygous']							 
							);
							$this->db->update('transgene_assoc', $transgene_geno, "transgene_assoc_ID = " . $assoc_ID);
						}
					}
				}
			} 
			echo '<script language="javascript">
			self.parent.window.location.reload()
			self.parent.Shadowbox.close();
			</script>';
				
	}
	function db_update_user(){
		$type = $this->uri->segment(3);
		if($type == "u"){  
				 $user = array(
					'lab' => $_POST['lab'],
					'office_location' => $_POST['office_location'],
					'lab_location' => $_POST['lab_location'],
					'lab_phone' => $_POST['lab_phone'],
					'emergency_phone' => $_POST['emergency_phone'],
					'email' => $_POST['email'],
					'first_name' => $_POST['first_name'],
					'middle_name' => $_POST['middle_name'],
					'last_name' => $_POST['last_name'],
					'username' => $_POST['username'],
					'admin_access' => $_POST['admin_access'] 
				);
				if ($_POST['passcheck']){ 
					$this->load->library('SimpleLoginSecure');
					//get the password hash from the simpleloginsecure library
					$user['user_pass'] = $this->simpleloginsecure->get_pass_hash(); 
				}
				$this->db->update('users', $user, "user_ID = " . $_POST['user_ID']);				 
			}elseif ($type == "i"){ 
				$this->load->library('SimpleLoginSecure');
				$this->simpleloginsecure->create($_POST['username'], $_POST['user_pass']);
				$this->CI =& get_instance();
				$user_ID = $this->session->userdata('last_user_ID'); 
			}elseif ($type == "r"){ 
				$this->db->where('user_ID', $_POST['user_ID']);
				$this->db->delete('users');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_mutant(){
		$type = $this->uri->segment(3);
		if($type == "u"){ 
				 $mutant = array(
					'mutant' => $_POST['mutant'],
					'allele' => $_POST['allele'],
					'gene' => $_POST['gene'],
					'reference' => $_POST['reference'],
					'strain' => $_POST['strain'],
					'cross_ref' => $_POST['cross_ref'] 
				);
				$this->db->update('mutant', $mutant, "mutant_ID = " . $_POST['mutant_ID']);				 
			}elseif ($type == "i"){ 
				 $mutant = array(
					'mutant' => $_POST['mutant'],
					'allele' => $_POST['allele'],
					'gene' => $_POST['gene'],
					'reference' => $_POST['reference'],
					'strain' => $_POST['strain'],
					'cross_ref' => $_POST['cross_ref'] 
				);
				$this->db->insert('mutant', $mutant);	
			}elseif ($type == "r"){ 
				$this->db->where('mutant_ID', $_POST['mutant_ID']);
				$this->db->delete('mutant');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3/2";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_strain(){
		$type = $this->uri->segment(3);
		if($type == "u"){ 
				 $strain = array(
					'strain' => $_POST['strain'],
					'source' => $_POST['source'],
					'source_contact_info' => $_POST['source_contact_info'],
					'comments' => $_POST['comments'] 
				);
				$this->db->update('strain', $strain, "strain_ID = " . $_POST['strain_ID']);				 
			}elseif ($type == "i"){ 
				$strain = array(
					'strain' => $_POST['strain'],
					'source' => $_POST['source'],
					'source_contact_info' => $_POST['source_contact_info'],
					'comments' => $_POST['comments'] 
				);
				$this->db->insert('strain', $strain);	
			}elseif ($type == "r"){ 
				$this->db->where('strain_ID', $_POST['strain_ID']);
				$this->db->delete('strain');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3/2";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_transgene(){
		$type = $this->uri->segment(3);
		if($type == "u"){ 
				 $transgene = array(
				    'transgene' => $_POST['transgene'],
					'promoter' => $_POST['promoter'],
					'gene' => $_POST['gene'],
					'reference' => $_POST['reference'],
					'strain' => $_POST['strain'],
					'allele' => $_POST['allele'],
					'comment' => $_POST['comment'] 
				);
				$this->db->update('transgene', $transgene, "transgene_ID = " . $_POST['transgene_ID']);				 
			}elseif ($type == "i"){ 
				$transgene = array(
				    'transgene' => $_POST['transgene'],
					'promoter' => $_POST['promoter'],
					'gene' => $_POST['gene'],
					'reference' => $_POST['reference'],
					'strain' => $_POST['strain'],
					'allele' => $_POST['allele'],
					'comment' => $_POST['comment'] 
				);
				$this->db->insert('transgene', $transgene);	
			}elseif ($type == "r"){ 
				$this->db->where('transgene_ID', $_POST['transgene_ID']);
				$this->db->delete('transgene');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3/2";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_lab(){
		$type = $this->uri->segment(3);
		if($type == "u"){ 
				 $lab = array(
					'lab_ID' => $_POST['lab_ID'] ,
					'lab_name' => $_POST['lab'] 					 
				);   
				$this->db->update('labs', $lab, 'lab_ID = "' . $_POST['lab_ID'] . '"');				 
			}elseif ($type == "i"){ 
				$lab = array(
					'lab_ID' => '' ,
					'lab_name' => $_POST['lab'] 
				);
				$this->db->insert('labs', $lab);	
			}elseif ($type == "r"){ 
				$this->db->where('lab_ID', $_POST['lab_ID']);
				$this->db->delete('labs');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3/2";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_tank(){
		$type = $this->uri->segment(3);
		if($type == "u"){ 
				 $tank = array(
					'size' => $_POST['size'], 
					'location' => $_POST['location'], 
					'room' => $_POST['room'], 	
					'comments' => $_POST['comments']				 
				);
				$this->db->update('tank', $tank, "tank_ID = " . $_POST['tank_ID']);				 
			}elseif ($type == "i"){ 
				$tank = array(
					'size' => $_POST['size'], 
					'location' => $_POST['location'], 
					'room' => $_POST['room'],	
					'comments' => $_POST['comments']	
				);
				$this->db->insert('tank', $tank);	
			}elseif ($type == "r"){ 
				$this->db->where('tank_ID', $_POST['tank_ID']);
				$this->db->delete('tank');
			}			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/3/1";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update_recipients(){
			$type = $this->uri->segment(3);
			$this->db->empty_table('report_recipients'); 
			foreach ($_POST['users'] as $value){
				$recipients = array(
					'user_ID' => $value,
					'report_ID' => $_POST['report']  
				);
				$this->db->insert('report_recipients', $recipients);	
			} 		 			 
			echo '<script language="javascript">
			location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/4"; 
			</script>';
	}
	function db_update_search(){ 
			$this->db->where('search_ID', $_POST['search_ID']);
			$this->db->delete('saved_searches');		 			 
			echo '<script language="javascript">
			parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/2";
			self.parent.Shadowbox.close();
			</script>';
	}
	function db_update(){		 
			$this->load->library('SimpleLoginSecure');  
		 	$type = $this->uri->segment(3); 
			if($type == "u"){
				$this->db->where('strain_ID', $_POST['strain_ID']);			
				$query = $this->db->get('strain');			
				if ($query->num_rows() > 0){
					$strain = $query->row_array();
				}
				$this->db->where('mutant_ID', $_POST['mutant_ID']);			
				$query = $this->db->get('mutant');			
				if ($query->num_rows() > 0){
					$mutant = $query->row_array();
				}
				if ($_POST['birthday'] != ""){	
					$birthday_array = explode('/',$_POST['birthday']);
					$birthday = mktime(0,0,0,$birthday_array[0],$birthday_array[1],$birthday_array[2]);
				}
				if ($_POST['death_date'] != ""){
					$death_date_array = explode('/',$_POST['death_date']);
					$death_date = mktime(0,0,0,$death_date_array[0],$death_date_array[1],$death_date_array[2]); 
				}
				$fish = array(
					'room' => $_POST['room'],
					'gender' => $_POST['gender'],
					'name' => $_POST['name'],
					'status' => $_POST['status'],
					'birthday' => $birthday,
					'death_date' => $death_date,
					'mother_ID' => trim($_POST['mother_ID']),
					'father_ID' => trim($_POST['father_ID']),
					'user_ID' => $_POST['user_ID'],
					'comments' => $_POST['comments'],
					'strain_ID' => $_POST['strain_ID'], 
					'generation' => $_POST['generation'], 
					'starting_nursery' => $_POST['starting_nursery'],
					'current_adults' => $_POST['current_adults'], 
					'starting_adults' => $_POST['starting_adults'],
					'current_nursery' => $_POST['current_nursery'] 
				);
				$this->db->update('fish', $fish, "batch_ID = " . $_POST['batch_ID']);
				$log_message = "\n" . "Update Batch " . "\n" . "Batch Number: " . $_POST['batch_ID'] . "\n" .
				'Username: ' . $this->session->userdata('username') . "\n";				 
				log_message('error', $log_message); 
				
				$this->db->where('batch_ID', $_POST['batch_ID']);			
				$query = $this->db->get('mutant_assoc');			
				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						$geno_array['mutant'][$row->mutant_ID]['mutant_genotype_wildtype'] = $row->mutant_genotype_wildtype;
						$geno_array['mutant'][$row->mutant_ID]['mutant_genotype_heterzygous'] = $row->mutant_genotype_heterzygous;
						$geno_array['mutant'][$row->mutant_ID]['mutant_genotype_homozygous'] = $row->mutant_genotype_homozygous;
					}
				} 
				$this->db->where('batch_ID', $_POST['batch_ID']);			
				$query = $this->db->get('transgene_assoc');			
				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						$geno_array['transgene'][$row->transgene_ID]['transgene_genotype_wildtype'] = $row->transgene_genotype_wildtype;
						$geno_array['transgene'][$row->transgene_ID]['transgene_genotype_heterzygous'] = $row->transgene_genotype_heterzygous;
						$geno_array['transgene'][$row->transgene_ID]['transgene_genotype_homozygous'] = $row->transgene_genotype_homozygous;
					}
				} 
				if (is_array($_POST['mutants'])){
					$this->db->delete('mutant_assoc', array('batch_ID' => $_POST['batch_ID'])); 
					foreach ($_POST['mutants'] as $mutant){
						$mutant_assoc = array(
							'mutant_assoc_ID' => "",
							'mutant_ID' => $mutant,
							'batch_ID' => $_POST['batch_ID'] ,
							'mutant_genotype_wildtype' => $geno_array['mutant'][$mutant]['mutant_genotype_wildtype'],
							'mutant_genotype_heterzygous' => $geno_array['mutant'][$mutant]['mutant_genotype_heterzygous'] ,
							'mutant_genotype_homozygous' => $geno_array['mutant'][$mutant]['mutant_genotype_homozygous']  
						);
						$this->db->insert('mutant_assoc',$mutant_assoc); 
					}
				}
				if (is_array($_POST['transgenes'])){
					$this->db->delete('transgene_assoc', array('batch_ID' => $_POST['batch_ID'])); 
					foreach ($_POST['transgenes'] as $transgene){
						$transgene_assoc = array(
							'transgene_assoc_ID' => "",
							'transgene_ID' => $transgene,
							'batch_ID' => $_POST['batch_ID'],
							'transgene_genotype_wildtype' => $geno_array['transgene'][$transgene]['transgene_genotype_wildtype'],
							'transgene_genotype_heterzygous' => $geno_array['transgene'][$transgene]['transgene_genotype_heterzygous'] ,
							'transgene_genotype_homozygous' => $geno_array['transgene'][$transgene]['transgene_genotype_homozygous'] 
						);
						$this->db->insert('transgene_assoc',$transgene_assoc); 
					}
				}
				redirect("fish/modify_line/u_" . $_POST['batch_ID'], "refresh");			 
			}elseif ($type == "i"){
				$this->db->where('strain_ID', $_POST['strain_ID']);			
				$query = $this->db->get('strain');			
				if ($query->num_rows() > 0){
					$strain = $query->row_array();
				}
				$this->db->where('mutant_ID', $_POST['mutant_ID']);			
				$query = $this->db->get('mutant');			
				if ($query->num_rows() > 0){
					$mutant = $query->row_array();
				}
				if ($_POST['birthday'] != ""){	
					$birthday_array = explode('/',$_POST['birthday']);
					$birthday = mktime(0,0,0,$birthday_array[0],$birthday_array[1],$birthday_array[2]); 
				}
				if ($_POST['death_date'] != ""){
					$death_date_array = explode('/',$_POST['death_date']);
					$death_date = mktime(0,0,0,$death_date_array[0],$death_date_array[1],$death_date_array[2]);
				}
				$fish = array(
					'room' => $_POST['room'],
					'gender' => $_POST['gender'],
					'name' => $_POST['name'],
					'status' => $_POST['status'],
					'birthday' => $birthday,
					'death_date' => $death_date,
					'mother_ID' => trim($_POST['mother_ID']),
					'father_ID' => trim($_POST['father_ID']),
					'user_ID' => $_POST['user_ID'],
					'comments' => $_POST['comments'],
					'strain_ID' => $_POST['strain_ID'], 
					'generation' => $_POST['generation'],					 
					'starting_nursery' => $_POST['starting_nursery'],
					'current_nursery' => $_POST['current_nursery'],
					'current_adults' => $_POST['current_adults'], 
					'starting_adults' => $_POST['starting_adults']					 
				);
				$this->db->insert('fish', $fish);
				echo '<script language="javascript">
				alert("Batch Number ' . mysql_insert_id() . ' has been added!");
				</script>';	
				echo '<script language="javascript">
						if (self.parent.document.getElementById("tabs") == null){
							parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/show_all";
						}else{
							parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/1";;
						} 
						self.parent.Shadowbox.close();
						</script>'; 
			}elseif ($type == "r"){
				$this->db->where('batch_ID', $_POST['batch_ID']);
				$this->db->delete('fish');
				$this->db->where('batch_ID', $_POST['batch_ID']);
				$this->db->delete('tank_assoc');
				$log_message = "\n" . "Delete Batch " . "\n" . "Batch Number: " . $_POST['batch_ID'] . "\n" .
				'Username: ' . $this->session->userdata('username') . "\n";				 
				log_message('error', $log_message); 
				echo '<script language="javascript">
						if (self.parent.document.getElementById("tabs") == null){
							parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/show_all";
						}else{
							parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/1";;
						} 
						self.parent.Shadowbox.close();
						</script>'; 
			} 
	}
	function db_update_wq(){		 
			$this->load->library('SimpleLoginSecure');  
		 	$type = $this->uri->segment(3); 
			if($type == "u"){
				if ($_POST['record_date'] != ""){
					$temp = explode('/',$_POST['record_date']);
					$record_date = mktime(0,0,0,$temp[0],$temp[1],$temp[2]); 
				}
				$entry = array(
					'system_name' => $_POST['system_name'],
					'location' => $_POST['location'],
					'nitrate' => $_POST['nitrate'],
					'nitrite' => $_POST['nitrite'], 
					'ph' => $_POST['ph'],
					'conductivity' => $_POST['conductivity'],
					'do' => $_POST['do'], 
					'temperature' => $_POST['temperature'],					 
					'record_date' => $record_date			 
				);
				$this->db->update('water_quality', $entry, "entry_ID = " . $_POST['entry_ID']);
				echo '<script language="javascript"> 
						parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/5";;
				 		self.parent.Shadowbox.close();
						</script>'; 
			}elseif ($type == "n"){ 
				$entry = array(
					'system_name' => $_POST['system_name'],
					'location' => $_POST['location'],
					'nitrate' => $_POST['nitrate'],
					'nitrite' => $_POST['nitrite'], 
					'ph' => $_POST['ph'],
					'conductivity' => $_POST['conductivity'],
					'do' => $_POST['do'], 
					'temperature' => $_POST['temperature'],					 
					'record_date' => time() 				 
				);
				$this->db->insert('water_quality', $entry);
				echo '<script language="javascript">
				alert("Water quality entry ID ' . mysql_insert_id() . ' has been added!");
				</script>';	
				echo '<script language="javascript"> 
						parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/5";;
					 
						self.parent.Shadowbox.close();
						</script>'; 
			}elseif ($type == "r"){ 
				$this->db->where('entry_ID', $_POST['entry_ID']);
				$this->db->delete('water_quality');
				echo '<script language="javascript"> 
						parent.location.href = "' . $this->config->item('base_url') . 'index.php/fish/index/blank/5";;
					 
						self.parent.Shadowbox.close();
						</script>'; 
			}
	}
	function show_all(){
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
 		$this->db->select('(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,labs.lab_name, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery');
		$this->db->from('fish'); 
		$this->db->join('users', 'fish.user_ID = users.user_ID','left outer'); 
		$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('status like', 'Alive');
		//put part of the sql statement into variables for the datatables plugin for display   
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options']['datatables_select'] =  $select;
		$data['search_options']['datatables_field_wtables'] =  '(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,labs.lab_name, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery';
		$data['search_options']['datatables_from'] =  str_replace("`","",$from);
		$data['search_options']['datatables_where'] = $this->db->ar_where[0];
		$data['search_options']['datatables_fields'] = "('empty'),batch_ID,name,birthday,username,lab_name,strain,generation,current_adults,starting_nursery";
		$data['search_options']['datatables_buttons']  = $url;
		$all_fish = $this->db->get(); 
		$this->CI =& get_instance();
		$admin_access = $this->CI->session->userdata('admin_access'); 
		show_all_fish($url,$all_fish,$admin_access,$data);
	}
	function index(){    
		$url = $this->config->item('base_url');
		$this->load->library('SimpleLoginSecure');  
		$this->simpleloginsecure->login($_SESSION['username'], '');  
		$this->CI =& get_instance();
		$data['admin_access'] = $this->CI->session->userdata('admin_access'); 
		$_SESSION['username'] = $this->CI->session->userdata('username');  
	 	require 'function.php';	  
		$data['url_var_3'] = $this->uri->segment(3);
		$data['url_var_4'] = $this->uri->segment(4);
		$data['url_var_5'] = $this->uri->segment(5);
		if ($data['url_var_3'] == "showt"){
			$_SESSION['show_tanks'] = true;	
		}elseif ($data['url_var_3'] == "hidet"){
			$_SESSION['show_tanks'] = "";	
		}
		if ($data['url_var_3'] == "nsearch"){ 
			$birthday_array = explode('/',$_POST['birthday']);
			$birthday = mktime(0,0,0,$birthday_array[0],$birthday_array[1],$birthday_array[2]); 
			$saved_search = array(
					'search_name' => $_POST['search_name'], 
					'batch_ID' => $_POST['batch_ID'], 
					'mylab' => $_POST['mylab'], 
					'gender' => $_POST['gender'], 
					'name' => $_POST['batch_name'],
					'status' => $_POST['status'],
					'birthday' => $birthday,
					'mother_ID' => $_POST['mother_ID'],
					'father_ID' => $_POST['father_ID'],
					'user_ID' => $_POST['user_ID'],
					'comments' => $_POST['comments'],
					'strain_ID' => $_POST['strain_ID'],
					'mutant_ID' => $_POST['mutant_ID'],
					'generation' => $_POST['generation'],
					'mutant_genotype_wildtype' => $_POST['mutant_genotype_wildtype'],
					'mutant_genotype_heterzygous' => $_POST['mutant_genotype_heterzygous'],
					'mutant_genotype_homozygous' => $_POST['mutant_genotype_homozygous'], 
					'transgene_ID' => $_POST['transgene_ID'], 
					'transgene_genotype_wildtype' => $_POST['transgene_genotype_wildtype'],
					'transgene_genotype_heterzygous' => $_POST['transgene_genotype_heterzygous'],
					'transgene_genotype_homozygous' => $_POST['transgene_genotype_homozygous'],
					'lab' => $_POST['lab'],
					'tank_ID' => $_POST['tank_ID'],
					'mutant_allele' => $_POST['mutant_allele'],
					'transgene_allele' => $_POST['transgene_allele']
			);
			$this->db->insert('saved_searches', $saved_search); 
			redirect('/fish/index/blank/2','refresh');
		} 
		
		$this->db->where('username', $this->session->userdata('username'));
		$query = $this->db->get('users');	
		if ($query->num_rows() > 0){
			$data['loggedin_user'] = $query->row_array();
		}	
	 	$this->db->select('(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,strain.strain, fish.generation, fish.current_adults, fish.starting_nursery');
		$this->db->from('fish');
	 	$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
		$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('lab_ID like', $data['loggedin_user']['lab']);
		$this->db->where('status like', 'Alive');  
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options']['datatables_select'] =  $select;
		$data['search_options']['datatables_field_wtables'] =  '(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username, strain.strain,fish.generation, fish.current_adults, fish.starting_nursery';
		$data['search_options']['datatables_from'] =  str_replace("`","",$from);
		$data['search_options']['datatables_where'] = $this->db->ar_where[0] . ' ' . $this->db->ar_where[1];
		$data['search_options']['datatables_fields'] = "('empty'),batch_ID,name,birthday,username,strain,generation,current_adults,starting_nursery";
		$data['search_options']['datatables_buttons']  = $url;
	 	$this->db->limit(1);
		$this->db->get();  
		
		$this->db->select('lab_ID,lab_name');
		$data['all_labs'] = $this->db->get('labs');		
		$this->db->order_by("last_name", "asc");
		$this->db->from('users');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer'); 
		$data['all_users'] = $this->db->get(); 
		$this->db->order_by("strain", "asc"); 
		$data['all_strains'] = $this->db->get('strain');			
		$this->db->order_by("promoter", "asc"); 
		$data['all_transgenes'] = $this->db->get('transgene');
		$this->db->distinct('allele');
		$this->db->order_by("allele", "asc"); 
		$data['all_transgene_allele'] = $this->db->get('transgene');
		
		$this->db->order_by("mutant", "asc"); 
		$data['all_mutants'] = $this->db->get('mutant');  
		$this->db->distinct('allele');
		$this->db->order_by("allele", "asc"); 
		$data['all_mutant_allele'] = $this->db->get('mutant');   
		$data['all_searches'] = $this->db->get('saved_searches'); 
		$data['all_report_recipients'] = $this->db->get('report_recipients');
		foreach ($data['all_labs']->result() as $row){			
			$this->db->select('sum(current_adults) as fish_count,lab_name');
			$this->db->from('fish');
			$this->db->join('users', 'fish.user_ID = users.user_ID');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID =', $row->lab_ID);
			$this->db->where('status =', 'Alive');
			$this->db->or_where('status =', 'Sick');
			$this->db->group_by('lab_ID');
			$data['current_count'][$row->lab_ID] = $this->db->get();
		} 
		foreach ($data['all_labs']->result() as $row){			
			$this->db->select('sum(current_nursery) as fish_count,lab_name');
			$this->db->from('fish');
			$this->db->join('users', 'fish.user_ID = users.user_ID');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID like ', $row->lab_ID);
			$this->db->where('status like ', 'Alive');
			$this->db->or_where('status like ', 'Sick');
			$this->db->group_by('lab_name');
			$data['nurseryq_count'][$row->lab_ID] = $this->db->get();
		}  
		$data['datefilter'] = $this->db->query("SELECT DISTINCT CONCAT(FROM_UNIXTIME(date_taken, '%M'),' ',FROM_UNIXTIME(date_taken, '%Y'))  as groupby,
		 date_taken FROM stat_survival_track group by groupby ORDER BY date_taken desc");
		 
		$this->db->select('(\'empty\'),STAT.batch_ID,STAT.starting_nursery,STAT.current_adults,STAT.starting_adults,lab_name,STAT.status,survival_percent,STAT.birthday,death_date,date_taken');
		$currmonth = mktime(0,1,1,date('m',time()),1,date('Y',time()));
		if ($data['datefilter']->num_rows() > 0){
			$current_month = $data['datefilter']->row_array();
		} 
		$this->db->from('stat_survival_track STAT');
		$this->db->join('fish FS', 'FS.batch_ID = STAT.batch_ID');
		$this->db->join('users', 'FS.user_ID = users.user_ID');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('date_taken >=', $currmonth);
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " "; 
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options_track']['track_datatables_select'] =  $select;
		$data['search_options_track']['track_datatables_field_wtables'] =  '(\'empty\'),lab_name,STAT.batch_ID,STAT.current_adults,STAT.starting_adults,STAT.starting_nursery,STAT.status,STAT.survival_percent, STAT.birthday, STAT.date_taken';
		$data['search_options_track']['track_datatables_from'] =  $from;
		$data['search_options_track']['track_datatables_where'] = $this->db->ar_where[0];
		$data['search_options_track']['track_datatables_fields'] = '(\'empty\'),batch_ID,starting_nursery,current_adults,starting_adults,lab_name,status,survival_percent,birthday,death_date,date_taken';
		$data['search_options_track']['track_datatables_buttons']  = $url;
	 	$this->db->limit(1);
		$this->db->get();  
		
		$this->db->select('(\'empty\'),batch_ID, username, lab_name,current_adults, starting_adults,starting_nursery,current_nursery,birthday,concat(convert(CAST(IF(current_adults >= starting_nursery,round(current_adults / starting_nursery  * 100,2),\'\') as UNSIGNED) USING latin1),\'%\') as survival',false);
		$this->db->from('fish');
		$this->db->join('users', 'fish.user_ID = users.user_ID');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('status not like', 'Dead'); 
		$this->db->where(array('starting_adults !=' => ''));  
		$this->db->where(array('current_adults !=' => ''));  
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}   
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options_survival']['survival_datatables_select'] =  $select;
		$data['search_options_survival']['survival_datatables_field_wtables'] =   '(\'empty\'),batch_ID, username,  lab_name,current_adults, starting_adults,starting_nursery,current_nursery,birthday,survival';
		$data['search_options_survival']['survival_datatables_from'] =  $from;
		$data['search_options_survival']['survival_datatables_where'] = $this->db->ar_where[0] .  " "  . $this->db->ar_where[1] . " "  . $this->db->ar_where[2];
		$data['search_options_survival']['survival_datatables_fields'] ='(\'empty\'),batch_ID,username,lab_name,current_adults,starting_adults,starting_nursery,current_nursery,birthday,survival';
		$data['search_options_survival']['survival_datatables_buttons']  = $url;
 		$this->db->limit(1);
		$this->db->get();  
		
		$this->db->select('entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date');
		$this->db->from('water_quality'); 
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}   
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_water_quality']['datatables_select'] =  $select;
		$data['search_water_quality']['datatables_field_wtables'] =   'entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date';
		$data['search_water_quality']['datatables_from'] =  $from;
		$data['search_water_quality']['datatables_where'] = "";
		$data['search_water_quality']['datatables_fields'] ='entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date';
		$data['search_water_quality']['datatables_buttons']  = $url; 
		$this->db->limit(1);
		$this->db->get();  
		
		$start_date = mktime(1,1,1,date("m",time()) - 1,1,date("Y",time()));
		$end_date = mktime(1,1,1,date("m",time()) - 1,31,date("Y",time()));
		$this->db->where("record_date > ",$start_date);
		$this->db->where("record_date < ",$end_date);
		$data['water_quality'] = $this->db->get('water_quality');
		
		$this->load->view('fish_view',$data);  
	}
	function filter_track_survival(){
		require 'function.php';	
		$url = $this->config->item('base_url');
		libraries($url); 
	 	$this->db->select('(\'empty\'),STAT.batch_ID,STAT.starting_nursery,STAT.current_adults,STAT.starting_adults,lab_name,STAT.status,survival_percent,STAT.birthday,death_date,date_taken');
	 	$this->db->from('stat_survival_track STAT');
		$this->db->join('fish FS', 'FS.batch_ID = STAT.batch_ID');
		$this->db->join('users', 'FS.user_ID = users.user_ID');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('date_taken =', $this->uri->segment(3));
		$this->db->order_by("date_taken", "desc");  
		
		
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " "; 
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$search_options_track['track_datatables_select'] =  $select;
		$search_options_track['track_datatables_field_wtables'] =  '((\'empty\'),lab_name,STAT.batch_ID,STAT.current_adults,STAT.starting_adults,STAT.starting_nursery,STAT.status,STAT.survival_percent, STAT.birthday, STAT.date_taken';
		$search_options_track['track_datatables_from'] =  $from;
		$search_options_track['track_datatables_where'] = $this->db->ar_where[0];
		$search_options_track['track_datatables_fields'] = '(\'empty\'),batch_ID,starting_nursery,current_adults,starting_adults,lab_name,status,survival_percent,birthday,death_date,date_taken';
		$search_options_track['track_datatables_buttons']  = $url; 
		
		
		$this->db->limit(1);
		$query = $this->db->get(); 
		if ($query->num_rows() > 0){
			$data['first_record'] = $query->row_array();
		}
		$this->load->library('SimpleLoginSecure');   
		$this->CI =& get_instance();
		$data['admin_access'] = $this->CI->session->userdata('admin_access');
		
		track_percentage_filtered($data,$url,$search_options_track);	
	} 
	function batch_summary(){	  
	 	require 'function.php';
		$_SESSION['report_data'] = ""; 
		$url = $this->config->item('base_url');
		libraries($url);
		$report_array = explode('_',$this->uri->segment(3)); 
	  	$select = '(\'empty\'),fish.batch_ID,fish.name, fish.status,fish.birthday, fish.death_date, users.username, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery';
		if ($report_array[1] == "m"){
			$this->db->where('username', $this->session->userdata('username'));
			$query = $this->db->get('users');	
			if ($query->num_rows() > 0){
  				$logged_in = $query->row_array();
			}	
			$this->db->select($select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('fish.user_ID',$logged_in['user_ID']);
			$report_array[0] = $logged_in['first_name'] . ' ' . $logged_in['last_name'];
		}elseif ($report_array[1] == "ml"){
			$this->db->where('username', $this->session->userdata('username'));	
			$this->db->from('users'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');		
			$query = $this->db->get(); 
			if ($query->num_rows() > 0){
				$current_lab = $query->row_array();
			}		 
			$this->db->where('lab',$current_lab['lab']);
			$query = $this->db->get('users');			
			 
			$this->db->select($select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$current_lab['lab']); 
			$report_array[0] = $current_lab['lab_name'];
		}elseif ($report_array[1] == "u"){ 
			$this->db->where('user_ID', $report_array[0]);
			$query = $this->db->get('users');	
			if ($query->num_rows() > 0){
  				$user_array = $query->row_array();
			}
			$this->db->select($select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('users.user_ID',$report_array[0]);		 
			$report_array[0] = $user_array['first_name'] . ' ' . $user_array['last_name'];
		}elseif ($report_array[1] == "l"){	
			$this->db->where('lab_ID', $report_array[0]);
			$query = $this->db->get('labs');	
			if ($query->num_rows() > 0){
  				$lab_name = $query->row_array();
			} 
			$this->db->select($select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$report_array[0]); 
			$report_array[0] = $lab_name['lab_name'];
		} 
		
		$from = $this->db->ar_from[0] . ' '; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . ' ';	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$search_options['datatables_select'] =  $select;
		$search_options['datatables_field_wtables'] =   '(\'empty\'),fish.batch_ID,fish.name, fish.status,fish.birthday, fish.death_date, users.username, strain.strain,fish.generation, fish.current_adults, fish.starting_nursery';
		$search_options['datatables_from'] =  $from;
		$search_options['datatables_where'] = $this->db->ar_where[0];
		$search_options['datatables_fields'] = '(\'empty\'),batch_ID,name,status,birthday,death_date,username,strain,generation,current_adults,starting_nursery';
		$search_options['datatables_buttons']  = $url;
	 	
		$this->db->limit(1);	
		$data['fish_report'] = $this->db->get(); 
		
		if ($data['fish_report']->num_rows() > 0){  
			$this->load->library('SimpleLoginSecure'); 		
			$this->simpleloginsecure->login($_SESSION['username'], '');  
			$this->CI =& get_instance();
			$data['admin_access'] = $this->CI->session->userdata('admin_access');
			$_SESSION['search_options'] = $search_options;
			output_batch_summary($data,$url,$report_array,$search_options);	
		}else{
			echo 'No Results!';	
		}
	}
	function quantity_summary(){	  
	 	require 'function.php';
		$_SESSION['report_data'] = "";
		$url = $this->config->item('base_url');
		libraries($url);
		$report_array = explode('_',$this->uri->segment(3));
		$user_select = "count(fish.batch_ID) as total_batches, sum(starting_adults) as starting_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery, sum(starting_nursery) as starting_nursery";
	 	$mutant_select = "mutant_assoc.mutant_ID,mutant, count(fish.batch_ID) as total_batches, sum(starting_adults) as starting_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery, sum(starting_nursery) as starting_nursery";
		$strain_select = "fish.strain_ID,strain, count(fish.batch_ID) as total_batches, sum(starting_adults) as starting_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery, sum(starting_nursery) as starting_nursery";
		$transgene_select = "transgene_assoc.transgene_ID,promoter, count(fish.batch_ID) as total_batches, sum(starting_adults) as starting_adults,sum(current_adults) as current_adults,sum(starting_nursery) as starting_nursery, sum(starting_nursery) as starting_nursery";
	 	if ($report_array[1] == "m"){
			$this->db->where('username', $this->session->userdata('username'));
			$query = $this->db->get('users');	
			if ($query->num_rows() > 0){
  				$logged_in = $query->row_array();
			}	
			//user report
			$this->db->select($user_select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID',''); 
			$this->db->where('fish.user_ID',$logged_in['user_ID']);
			$this->db->group_by('fish.user_ID');
			$data['user_quant'] = $this->db->get();	
			//mutant report
			$this->db->select($mutant_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('mutant_assoc', 'fish.batch_ID = mutant_assoc.batch_ID','left');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left');			
			$this->db->where('fish.user_ID',$logged_in['user_ID']);			 
			$this->db->where(array('mutant_assoc.mutant_ID !=' => ''));
			$this->db->group_by('mutant');
			$data['mutant_quant'] = $this->db->get();	
			//strain report
			$this->db->select($strain_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left');
			$this->db->where('fish.user_ID',$logged_in['user_ID']);
			$this->db->where(array('strain !=' => ''));		 
			$this->db->group_by('fish.strain_ID');
			$data['strain_quant'] = $this->db->get();			
			//transgene report
			$this->db->select($transgene_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('transgene_assoc', 'fish.batch_ID = transgene_assoc.batch_ID','left');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left');
			$this->db->where('fish.user_ID',$logged_in['user_ID']);
			$this->db->where(array('promoter !=' => ''));	
			$this->db->group_by('transgene_assoc.transgene_ID');
			$data['transgene_quant'] = $this->db->get();
			$report_array[0] = $logged_in['first_name'] . ' ' . $logged_in['last_name'];
		}elseif ($report_array[1] == "ml"){
			$this->db->where('username', $this->session->userdata('username'));			
			$query = $this->db->get('users');			
			if ($query->num_rows() > 0){
				$current_lab = $query->row_array();
			} 
			//user report
			$this->db->select($user_select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID',''); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$current_lab['lab']);
			$this->db->group_by('lab_ID');
			$data['user_quant'] = $this->db->get();				
			//mutant report
			$this->db->select($mutant_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('mutant_assoc', 'fish.batch_ID = mutant_assoc.batch_ID','left');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left');	
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$current_lab['lab']);
			$this->db->where(array('mutant_assoc.mutant_ID !=' => ''));
			$this->db->group_by('mutant');
			$data['mutant_quant'] = $this->db->get();	
			//strain report
			$this->db->select($strain_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$current_lab['lab']);
			$this->db->where(array('strain !=' => ''));	
			$this->db->group_by('fish.strain_ID');
			$data['strain_quant'] = $this->db->get();			
			//transgene report
			$this->db->select($transgene_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('transgene_assoc', 'fish.batch_ID = transgene_assoc.batch_ID','left');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$current_lab['lab']);
			$this->db->where(array('promoter !=' => ''));
			$this->db->group_by('transgene_assoc.transgene_ID');
			$data['transgene_quant'] = $this->db->get();
			
			$this->db->where('lab_ID', $current_lab['lab']);			
			$query = $this->db->get('labs');			
			if ($query->num_rows() > 0){
				$current_lab = $query->row_array();
			} 
			$report_array[0] = $current_lab['lab_name'];
		}elseif ($report_array[1] == "u"){ 
			$this->db->where('user_ID', $report_array[0]);
			$query = $this->db->get('users');	
			if ($query->num_rows() > 0){
  				$user_array = $query->row_array();
			}	
			//user report
			$this->db->select($user_select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID',''); 
			$this->db->where('fish.user_ID',$report_array[0]);
			$this->db->group_by('fish.user_ID');
			$data['user_quant'] = $this->db->get();	
			//mutant report
			$this->db->select($mutant_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('mutant_assoc', 'fish.batch_ID = mutant_assoc.batch_ID','left');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left');	
			$this->db->where('fish.user_ID',$report_array[0]);
			$this->db->where(array('mutant_assoc.mutant_ID !=' => ''));
			$this->db->group_by('mutant');
			$data['mutant_quant'] = $this->db->get();	
			//strain report
			$this->db->select($strain_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left');
			$this->db->where('fish.user_ID',$report_array[0]);
			$this->db->where(array('strain !=' => ''));	
			$this->db->group_by('fish.strain_ID');
			$data['strain_quant'] = $this->db->get();			
			//transgene report
			$this->db->select($transgene_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('transgene_assoc', 'fish.batch_ID = transgene_assoc.batch_ID','left');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left');
			$this->db->where('fish.user_ID',$report_array[0]);
			$this->db->where(array('promoter !=' => ''));
			$this->db->group_by('transgene_assoc.transgene_ID');
			$data['transgene_quant'] = $this->db->get();			
			$report_array[0] = $user_array['first_name'] . ' ' . $user_array['last_name'];
		}elseif ($report_array[1] == "l"){			 
			//user report
			$this->db->select($user_select);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer'); 
			$this->db->where('lab_ID',$report_array[0]);
			$this->db->group_by('lab_name');
			$data['user_quant'] = $this->db->get();				
			//mutant report
			$this->db->select($mutant_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('mutant_assoc', 'fish.batch_ID = mutant_assoc.batch_ID','left');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left');	
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$report_array[0]);
			$this->db->where(array('mutant_assoc.mutant_ID !=' => ''));
			$this->db->group_by('mutant');
			$data['mutant_quant'] = $this->db->get();	
			//strain report
			$this->db->select($strain_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$report_array[0]);
			$this->db->where(array('strain !=' => ''));	
			$this->db->group_by('fish.strain_ID');
			$data['strain_quant'] = $this->db->get();			
			//transgene report
			$this->db->select($transgene_select);
			$this->db->distinct();
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left'); 
			$this->db->join('transgene_assoc', 'fish.batch_ID = transgene_assoc.batch_ID','left');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('lab_ID',$report_array[0]);
			$this->db->where(array('promoter !=' => ''));
			$this->db->group_by('transgene_assoc.transgene_ID');
			$data['transgene_quant']  = $this->db->get();
			
			$this->db->where('lab_ID', $report_array[0]);			
			$query = $this->db->get('labs');			
			if ($query->num_rows() > 0){
				$current_lab = $query->row_array();
			} 
			$report_array[0] = $current_lab['lab_name'];		 
		}
		$url = $this->config->item('base_url');
		output_quantity_summary($data,$url,$report_array);
		
	}
	function export_range_report(){	   
		require 'function.php';	
		$vars =  explode('_',$this->uri->segment(3));
		$start_date = $vars[0];
		$end_date = $vars[1];
		if ($start_date && $end_date){			 
			$date_array = explode('-',$start_date);
			$start = mktime(0,0,0,$date_array[0],$date_array[1],$date_array[2]);
			$date_array = "";
			$date_array = explode('-',$end_date);
			$end = mktime(0,0,0,$date_array[0],$date_array[1],$date_array[2]);
			$this->db->where('record_date >', $start);
			$this->db->where('record_date <', $end);
			$line_items = $this->db->get('line_item');
			$url = $this->config->item('base_url');
			excel_output($line_items,$url);
		}		
		$this->load->view('csavings_view',$data);		
	}
	function export(){	  
		require 'function.php';	
		$url = $this->config->item('base_url');	
		$output_type = $this->uri->segment(3);
		if($output_type == "all" || $output_type == "lab"){
			$this->CI =& get_instance();  
			$this->db->where(array('username like ' =>  $this->CI->session->userdata('username')));
			$query = $this->db->get('users');
			if ($query->num_rows() > 0){
  				$userdata = $query->row_array();
			}
			if($output_type == "lab"){
				$this->db->select('fish.batch_ID,fish.name, fish.birthday, users.username, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery');
			}else{
				$this->db->select('fish.batch_ID,fish.name, fish.birthday, users.username,lab_name, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery');
			}
			$this->db->from('fish'); 
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			if($output_type == "lab"){
				$this->db->where(array('lab_ID like ' =>  $userdata['lab']));	
			}
			$this->db->where(array('status like ' =>  'Alive'));	
			$query = $this->db->get();
			if($output_type == "lab"){	
				$title_array = array('Batch #','Name','Birthday','User','Strain','Generation','Cur Adults','Start Nursery');
			}else{
				$title_array = array('Batch #','Name','Birthday','User','Lab','Strain','Generation','Cur Adults','Start Nursery');
			}
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					if (isset($row['birthday'])){
						$row['birthday'] =  date('m/d/Y', $row['birthday']); 
					}
					fputcsv($fp, $row); 
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
		}elseif($output_type == "batch_summary"){	
			$search_options = $_SESSION['search_options'];	
 			$this->db->select($search_options['datatables_select']);
			$this->db->from('fish'); 
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$query = $this->db->get();	 
			$str = $this->db->last_query();
			$sql = $str . ' WHERE ' . $search_options['datatables_where'];
			$query = $this->db->query($sql); 
			$title_array = explode(',',$search_options['datatables_fields']);
			array_shift($title_array);			
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w'); 
			fputcsv($fp, $title_array);
			 if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					array_shift($row);
					if (isset($row['birthday'])){
						$row['birthday'] =  date('m/d/Y', $row['birthday']); 
					}
					if (isset($row['death_date'])){
						$row['death_date'] =  date('m/d/Y', $row['death_date']); 
					}
					fputcsv($fp, $row);
				}
			} 
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype); 
		}elseif($output_type == "quantity_summary"){ 
			excel_quantity_output($_SESSION['report_data'],$url);
		}elseif($output_type == "water_quality"){  
 			$this->db->select('system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date');
			$this->db->from('water_quality'); 
			$this->db->order_by('system_name'); 
			$query = $this->db->get();	 
			$title_array = array('system_name','location','nitrate','nitrite','ph','conductivity','do','temperature','record_date');
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w'); 
			fputcsv($fp, $title_array);
			 if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){ 
					if (isset($row['record_date'])){
						$row['record_date'] =  date('m/d/Y', $row['record_date']); 
					} 
					fputcsv($fp, $row);
				}
			} 
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype); 
		}elseif($output_type == "survival_stat"){ 
			$datefilter = $this->db->query("SELECT DISTINCT CONCAT(FROM_UNIXTIME(date_taken, '%M'),' ',FROM_UNIXTIME(date_taken, '%Y'))  as groupby,
		 date_taken FROM stat_survival_track group by groupby ORDER BY date_taken desc");
			$this->db->select('(\'empty\'),STAT.batch_ID,STAT.starting_nursery,STAT.current_adults,STAT.starting_adults,lab_name,STAT.status,survival_percent,STAT.birthday,death_date,date_taken');
			$currmonth = mktime(0,1,1,date('m',time()),1,date('Y',time()));
			if ($datefilter->num_rows() > 0){
				$current_month = $datefilter->row_array();
			} 
			$this->db->from('stat_survival_track STAT');
			$this->db->join('fish FS', 'FS.batch_ID = STAT.batch_ID');
			$this->db->join('users', 'FS.user_ID = users.user_ID');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('date_taken >=', $currmonth);
			$query = $this->db->get();	 
			$title_array = array('Batch #','Starting Nursery','Current Adults','Starting Adults','Lab','Status','Survival Rate','Birthday','Death Date','Report Date');
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					array_shift($row);
					if (isset($row['birthday'])){
						$row['birthday'] =  date('m/d/Y', $row['birthday']);
					}
					if (isset($row['death_date'])){
						$row['death_date'] =  date('m/d/Y', $row['death_date']); 
					}
					if (isset($row['date_taken'])){
						$row['date_taken'] =  date('m/d/Y', $row['date_taken']);
					}
					$row['survival_percent'] =   $row['survival_percent'] . '%'; 					
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);			  
		}elseif($output_type == "survival_month_stat"){ 
			$datefilter = $this->db->query("SELECT DISTINCT CONCAT(FROM_UNIXTIME(date_taken, '%M'),' ',FROM_UNIXTIME(date_taken, '%Y'))  as groupby,
		 date_taken FROM stat_survival_track group by groupby ORDER BY date_taken desc");
			$this->db->select('(\'empty\'),STAT.batch_ID,STAT.starting_nursery,STAT.current_adults,STAT.starting_adults,lab_name,STAT.status,survival_percent,STAT.birthday,death_date,date_taken');
			$currmonth = mktime(0,1,1,date('m',time()),1,date('Y',time()));
			if ($datefilter->num_rows() > 0){
				$current_month = $datefilter->row_array();
			} 
			$this->db->from('stat_survival_track STAT');
			$this->db->join('fish FS', 'FS.batch_ID = STAT.batch_ID');
			$this->db->join('users', 'FS.user_ID = users.user_ID');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('date_taken =', $this->uri->segment(3));
			$query = $this->db->get();	 
			$title_array = array('Batch #','Starting Nursery','Current Adults','Starting Adults','Lab','Status','Survival Rate','Birthday','Death Date','Report Date');
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					array_shift($row);
					if (isset($row['birthday'])){
						$row['birthday'] =  date('m/d/Y', $row['birthday']);
					}
					if (isset($row['death_date'])){
						$row['death_date'] =  date('m/d/Y', $row['death_date']); 
					}
					if (isset($row['date_taken'])){
						$row['date_taken'] =  date('m/d/Y', $row['date_taken']);
					}
					$row['survival_percent'] =   $row['survival_percent'] . '%'; 					
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
		}elseif($output_type == "survival_current"){
			$this->db->select('(\'empty\'),batch_ID, username, lab_name,current_adults, starting_adults,starting_nursery,current_nursery,birthday,concat(convert(
CAST(IF(starting_nursery >= current_adults,round(current_adults / starting_nursery,4)*100 ,
\'\') as UNSIGNED) USING latin1),\'%\') as survival',FALSE);
			$this->db->from('fish');
			$this->db->join('users', 'fish.user_ID = users.user_ID');
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$this->db->where('status not like', 'Dead'); 
			$this->db->where(array('starting_adults !=' => ''));  
			$this->db->where(array('current_adults !=' => ''));	
			$query = $this->db->get();	 
			$title_array = array('Batch #','Username','Lab','Cur Adults','Start Adults','Start Nursery','Cur Nursery','Birthday','Survival Rate');
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					array_shift($row);
					if (isset($row['birthday'])){
						$row['birthday'] =  date('m/d/Y', $row['birthday']);
					}
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);
		}elseif($output_type == "search_results"){
			$search = $_SESSION['search_prev'];
			$search['datatables_select'] = str_replace("('empty'),",'',$search['datatables_select']);
			$search['datatables_field_wtables'] = str_replace("('empty'),",'',$search['datatables_field_wtables']);
			$search['datatables_fields'] = str_replace("('empty'),",'',$search['datatables_fields']);  
		 	$this->db->select($search['datatables_select']);
			$this->db->distinct();
			$this->db->from('fish');
			$this->db->join('mutant_assoc', 'mutant_assoc.batch_ID = fish.batch_ID','left outer');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left outer');
			
			$this->db->join('transgene_assoc', 'transgene_assoc.batch_ID = fish.batch_ID','left outer');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left outer');
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');			 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			$query = $this->db->get();	 
			$str = $this->db->last_query();
			$sql = $str . ' WHERE ' . $search['datatables_where']; 
			$query = $this->db->query($sql); 
			$title_array = explode(',',$search['datatables_fields']);			
			srand ((double) microtime( )*1000000);
			$random = rand( );
			$filetype = "csv";
			$path = getcwd();
			$tempname = $path . "/tmp/" . $random . "." . $filetype;	
			$fp = fopen($tempname, 'w');
			fputcsv($fp, $title_array);
			if ($query->num_rows() > 0){ 
				foreach ($query->result_array() as $row){
					if (isset($row['birthday'])){ 
						$row['birthday'] =  date('m/d/Y', $row['birthday']);  
					}
					fputcsv($fp, $row);
				}
			}
			fclose($fp);
			header('Location: ' . $url . 'tmp/' . $random . "." . $filetype);		 
		} 
	}
	function main(){
		//ini_set('display_errors', 'On');
		require 'function.php'; 
	}  
	function print_prev_all(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);	
		$this->db->select('(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,lab_name, strain.strain,fish.generation, fish.current_adults, fish.starting_nursery');
		$this->db->from('fish');  
		$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
		$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer'); 
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('status like', 'Alive');
		 $from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options']['datatables_select'] =  $select;
		$data['search_options']['datatables_field_wtables'] =  '(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,lab_name, strain.strain,fish.generation, fish.current_adults, fish.starting_nursery';
		$data['search_options']['datatables_from'] =  str_replace("`","",$from);
		$data['search_options']['datatables_where'] = $this->db->ar_where[0];
		$data['search_options']['datatables_fields'] = "('empty'),batch_ID,name,birthday,username,lab_name,strain,generation,current_adults,starting_nursery";
		$data['search_options']['datatables_buttons']  = $url;
	 	 
        all_lines_prev($url,$data);
	}
	
	function print_prev_wq(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);	
		$this->db->select('entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date');
		$this->db->from('water_quality'); 
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}   
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['datatables_select'] =  $select;
		$data['datatables_field_wtables'] =   'entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date';
		$data['datatables_from'] =  $from;
		$data['datatables_where'] = "";
		$data['datatables_fields'] ='entry_ID,system_name,location,nitrate,nitrite,ph,conductivity,do,temperature,record_date';
		$data['datatables_buttons']  = $url; 
	 	 
        all_wq_prev($url,$data);
	}
	
	function print_prev_lab(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url); 
		$this->simpleloginsecure->login($_SESSION['username'], '');  
		$this->CI =& get_instance();
		$this->db->where('username', $this->session->userdata('username'));
		$query = $this->db->get('users');	
		if ($query->num_rows() > 0){
			$data['loggedin_user'] = $query->row_array();
		} 
		$this->db->select('(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,lab_name, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery');
		$this->db->from('fish');  
		$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
		$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('lab_ID like', $data['loggedin_user']['lab']);
		$this->db->where('status like', 'Alive');
		 $from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$data['search_options']['datatables_select'] =  $select;
		$data['search_options']['datatables_field_wtables'] =  '(\'empty\'),fish.batch_ID,fish.name, fish.birthday, users.username,lab_name, strain.strain, fish.generation, fish.current_adults, fish.starting_nursery';
		$data['search_options']['datatables_from'] =  str_replace("`","",$from);
		$data['search_options']['datatables_where'] = $this->db->ar_where[0] . ' ' . $this->db->ar_where[1];
		$data['search_options']['datatables_fields'] = "('empty'),batch_ID,name,birthday,username,lab_name,strain,generation,current_adults,starting_nursery";
		$data['search_options']['datatables_buttons']  = $url;
	 	 
        all_lab_prev($url,$data);
	}
	function print_prev_batchsum(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);		 
        batch_summary_prev($url);
	}
	function print_prev_search(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);
	 
		$search = $_SESSION['search_prev'];	
		$search['datatables_select'] = str_replace("('empty'),",'',$search['datatables_select']);
		$search['datatables_field_wtables'] = str_replace("('empty'),",'',$search['datatables_field_wtables']);
		$search['datatables_fields'] = str_replace("('empty'),",'',$search['datatables_fields']); 
        search_prev($url,$search);
	}
	function print_prev_quantsum(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);		 
        quantity_summary_prev($url,$_SESSION['report_data']);
	}	
	function print_prev_survivalstat(){  
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);  
		
		$data['datefilter'] = $this->db->query("SELECT DISTINCT CONCAT(FROM_UNIXTIME(date_taken, '%M'),' ',FROM_UNIXTIME(date_taken, '%Y'))  as groupby,
		 date_taken FROM stat_survival_track group by groupby ORDER BY date_taken desc");
		 
		$this->db->select('STAT.batch_ID,STAT.starting_nursery,STAT.current_adults,STAT.starting_adults,lab_name,STAT.status,survival_percent,STAT.birthday,death_date,date_taken');
		$currmonth = mktime(0,1,1,date('m',time()),1,date('Y',time()));
		if ($data['datefilter']->num_rows() > 0){
			$current_month = $data['datefilter']->row_array();
		} 
		$this->db->from('stat_survival_track STAT');
		$this->db->join('fish FS', 'FS.batch_ID = STAT.batch_ID');
		$this->db->join('users', 'FS.user_ID = users.user_ID');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('date_taken >=', $currmonth);
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " "; 
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}  
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$search_options_track['track_datatables_select'] =  $select;
		$search_options_track['track_datatables_field_wtables'] =  '(lab_name,STAT.batch_ID,STAT.current_adults,STAT.starting_adults,STAT.starting_nursery,STAT.status,STAT.survival_percent, STAT.birthday, STAT.date_taken';
		$search_options_track['track_datatables_from'] =  $from;
		$search_options_track['track_datatables_where'] = $this->db->ar_where[0];
		$search_options_track['track_datatables_fields'] = 'batch_ID,starting_nursery,current_adults,starting_adults,lab_name,status,survival_percent,birthday,death_date,date_taken';
		$search_options_track['track_datatables_buttons']  = $url;
        survivalstat_prev($url,$search_options_track);
	}
	function print_prev_currentstat(){ 
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url); 
		$this->db->select('batch_ID, username, lab_name,current_adults, starting_adults,starting_nursery,current_nursery,birthday,concat(convert(CAST(IF(starting_nursery >= current_adults,round(current_adults / starting_nursery,4)*100,\'\') as UNSIGNED) USING latin1),\'%\') as survival');
		$this->db->from('fish');
		$this->db->join('users', 'fish.user_ID = users.user_ID');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('status not like', 'Dead'); 
		$this->db->where(array('starting_adults !=' => ''));  
		$this->db->where(array('current_adults !=' => ''));  
		$from = $this->db->ar_from[0] . " "; 
		foreach ($this->db->ar_join as $fromvar){
			$from .= $fromvar . " ";	
		}  
		$select_temp = "";  
		foreach ($this->db->ar_select as $selectvar){
			$select_temp .= $selectvar . ',';	
		}   
		$select = substr($select_temp,0,strlen($select_temp) -1);
		$search_options_survival['survival_datatables_select'] =  $select;
		$search_options_survival['survival_datatables_field_wtables'] =   'batch_ID, username,  lab_name,current_adults, starting_adults,starting_nursery,current_nursery,birthday,survival';
		$search_options_survival['survival_datatables_from'] =  $from;
		$search_options_survival['survival_datatables_where'] = $this->db->ar_where[0] .  " "  . $this->db->ar_where[1] . " "  . $this->db->ar_where[2];
		$search_options_survival['survival_datatables_fields'] ='batch_ID,username,lab_name,current_adults,starting_adults,starting_nursery,current_nursery,birthday,survival';
		$search_options_survival['survival_datatables_buttons']  = $url;
        survivalcurrent_prev($url,$search_options_survival);
	} 
	function submit_search_data(){  
		$attributes = array('id' => 'search_ID','name' => 'search_f');
		echo form_open('fish/search_data', $attributes); 
		echo '<input type="submit"><input type="text" name="temp"></form>';
		echo '<script language="javascript">
		var form_var = self.parent.document.search_form;
		form_var.action = "fish/search_data/";  
		var shadowbox_form = document.search_f;
		for(i=0; i<form_var.elements.length; i++){ 
			var name_var = form_var.elements[i].name;
			var val_var = form_var.elements[i].value; 
			if (form_var.elements[i].type == "text"){ 
				  if(val_var){			 		
					var newinput = document.createElement("input");
					shadowbox_form.appendChild(newinput);	
					newinput.name = form_var.elements[i].name;
					newinput.value = form_var.elements[i].value;
				  }
			}else if (form_var.elements[i].type == "checkbox"){
				if (form_var.elements[i].checked == true){
					var newinput = document.createElement("input");
					shadowbox_form.appendChild(newinput);	
					newinput.name = form_var.elements[i].name;
					newinput.value = "1"; 
				 }else if(form_var.elements[i].checked == false){ 
				 }
			}else if(form_var.elements[i].type == "select-one"){ 			
			 	if(form_var.elements[i].selectedIndex){ 
					var newinput = document.createElement("input");
					shadowbox_form.appendChild(newinput);	
					newinput.name = form_var.elements[i].name;
					newinput.value = form_var.elements[i].options[form_var.elements[i].selectedIndex].value; 
				}
			}  			 
		}  
		document.search_f.submit();				 
		</script> ';
	}
	function load_search_data(){ 
		$attributes = array('id' => 'search_ID','name' => 'search_f');
		echo form_open('fish/search_data', $attributes); 
		$search_ID = $this->uri->segment(3);
		$this->db->where(array('search_ID like ' => $search_ID));
		$criteria = $this->db->get('saved_searches');
		$fields = $this->db->list_fields('saved_searches');  
		echo '<input type="submit"><input type="text" name="temp"></form>';
		echo '<script language="javascript">
	 	var shadowbox_form = document.search_f;';
		foreach ($criteria->result() as $row){ 
			foreach ($fields as $field) {
				if ($row->$field && $field != "search_ID" && $field != "search_name"){
					if ($field == "mutant_genotype_wildtype" || $field == "mylab" || $field == "mutant_genotype_heterzygous" || $field == "mutant_genotype_homozygous" || $field == "transgene_genotype_wildtype" || $field == "transgene_genotype_heterzygous" || $field == "transgene_genotype_homozygous"){
						echo 'var newinput = document.createElement("input");
								shadowbox_form.appendChild(newinput);	
								newinput.name = "' . $field . '";
								newinput.value = "1";   ';
					}else{
						echo 'var newinput = document.createElement("input");
								shadowbox_form.appendChild(newinput);	
								newinput.name = "' . $field . '";
								newinput.value = "' . $row->$field . '";   ';
					} 
				}
			} 
		}  
		echo 'shadowbox_form.submit();	'; 
		echo '</script> ';
	}
	function search_data(){
		require 'function.php'; 
		$url = $this->config->item('base_url');
		libraries($url);
		$nocriteria = 1;
		 
		$this->load->library('SimpleLoginSecure');
		$this->simpleloginsecure->login($_SESSION['username'], '');  
		$this->CI =& get_instance();
		$admin_access = $this->CI->session->userdata('admin_access');  
		
		$this->db->where('username', $this->session->userdata('username'));
		$query = $this->db->get('users');	
		if ($query->num_rows() > 0){
			$logged_in = $query->row_array();
		} 	  
		foreach ($_POST as $key => $value){
			if ($key == "strain_ID" || $key == "user_ID" || $key == "comments" || $key == "batch_ID"){
				$key = "fish." . $key; 
			}elseif ($key == "mutant_ID"){
				 $key = "mutant_assoc." . $key; 
			}elseif ($key == "transgene_ID"){ 
				$key = "transgene_assoc." . $key; 
			}elseif ($key == "tank_ID"){ 
				$key = "tank." . $key;  
			}elseif ($key == "batch_name"){ 
				$key = "fish.name";
			}elseif ($key == "birthday"){
				$birthday_array = explode("/",$value);
				$value = mktime(0,0,0,$birthday_array[0],$birthday_array[1],$birthday_array[2]); 
			}elseif ($key == "death_date"){
				$death_date = explode("/",$value);
				$value = mktime(0,0,0,$death_date[0],$death_date[1],$death_date[2]); 
			} 
			$select .= $key . ','; 
			if ($value){
				if ($key == "fish.comments"){
					$this->db->where($key . ' like ', $value);
					$nocriteria = 0;
				}elseif ($key == "mylab"){ 
					$this->db->where('lab_ID like ', $logged_in['lab']);
				}elseif ($key == "mutant_allele" || $key == "transgene_allele"){ 
					$this->db->where(str_replace('_','.',$key) . ' =', $value);
					$nocriteria = 0;
				}elseif ($key == "mutant_genotype_wildtype" || $key == "mutant_genotype_heterzygous" || $key == "mutant_genotype_homozygous" || $key == "mutant_ID"){ 
					$this->db->where('mutant_assoc.' . $key . ' like ', $value);
				}elseif ($key == "transgene_genotype_wildtype" || $key == "transgene_genotype_heterzygous" || $key == "transgene_genotype_homozygous" || $key == "transgene_ID"){ 
					$this->db->where('transgene_assoc.' . $key . ' like ', $value);
				}else{
					$this->db->where($key . ' like ', $value);
					$nocriteria = 0;
				}
			}
		}  
		if ($nocriteria == 0){ 		
			$select = substr($select,0,strlen($select) - 1); 
			$where = substr($select,0,strlen($select) - 4);
			$this->db->select('fish.batch_ID,fish.name,fish.status, fish.birthday, users.username,lab_name, strain.strain, fish.generation, fish.current_adults,fish.starting_nursery',false); 
			$this->db->from('fish');
			$this->db->join('mutant_assoc', 'mutant_assoc.batch_ID = fish.batch_ID','left outer');
			$this->db->join('mutant', 'mutant_assoc.mutant_ID = mutant.mutant_ID','left outer');
			
			$this->db->join('transgene_assoc', 'transgene_assoc.batch_ID = fish.batch_ID','left outer');
			$this->db->join('transgene', 'transgene_assoc.transgene_ID = transgene.transgene_ID','left outer');
			$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');
			$this->db->join('users', 'fish.user_ID = users.user_ID','left outer'); 
			$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
			if ($_POST['tank_ID']){
				$this->db->join('tank_assoc', 'tank_assoc.batch_ID = fish.batch_ID','left outer');
				$this->db->join('tank', 'tank.tank_ID = tank_assoc.tank_ID','left outer');
			} 
			$from = $this->db->ar_from[0] . ' '; 
			foreach ($this->db->ar_join as $fromvar){
				$from .= $fromvar . ' ';	
			}  
			$select_temp = "";  
			foreach ($this->db->ar_select as $selectvar){
				$select_temp .= $selectvar . ',';	
			}  
			$select = substr($select_temp,0,strlen($select_temp) -1); 
			$where = ""; 
			foreach ($this->db->ar_where as $wherevar){
				$where .= $wherevar . ' ';	
			}   
			$search['datatables_select'] =  $select;
			$search['datatables_field_wtables'] =  '(\'empty\'),fish.batch_ID,fish.name,fish.status, fish.birthday, users.username,lab_name, strain.strain,fish.generation, fish.current_adults, fish.starting_nursery';
			$search['datatables_from'] =  $from;
			$search['datatables_where'] = $where;
			$search['datatables_fields'] = "('empty'),batch_ID,name,status,birthday,username,lab_name,strain,generation,current_adults,starting_nursery";
			$search['datatables_buttons']  = $url;
			$_SESSION['search_prev'] = $search; 
			output_search_results($query,$url,$admin_access,$search);
		}else{
			echo 'No criteria was selected.  Please go back and select a field to search by.';
		}
	} 
	function print_prev_label(){
		require 'function.php';
		 
		$url = $this->config->item('base_url');
		$text1 = "";
		$text2 = "";
		$batch_ID = $this->uri->segment(3); 
		$this->db->select('fish.*,users.*,strain.*,labs.*');
		$this->db->from('fish'); 
		$this->db->join('users', 'fish.user_ID = users.user_ID','left outer');
		$this->db->join('strain', 'fish.strain_ID = strain.strain_ID','left outer');
		$this->db->join('labs', 'users.lab = labs.lab_ID','left outer');
		$this->db->where('fish.batch_ID', $batch_ID);
		$query = $this->db->get(); 				
		if ($query->num_rows() > 0){
			$temp_object = $query->row_array(); 
		} 
		$this->db->where('user_ID',$temp_object['user_ID']);
		$query = $this->db->get("users");
		if ($query->num_rows() > 0){
			$name_array = $query->row_array();
		} 
		$date = "";
		if ($temp_object['birthday']){	
			$date = date('m/d/Y', $temp_object['birthday']);
		}
		$text = "Batch Number: " . $batch_ID;
		$text .= "<br>Name: " . $temp_object['name'];
		$text .= "<br>Birthday: " . $date;  
		//$text1 = "<strong>PI</strong>: " . $temp_object['lab_name'];
		//$text1 .= "   <strong>User</strong>: "	 . $name_array['username'] . "<br>";	
		
		//$text .= "<br>Strain: " . $temp_object['strain'];	 
		$this->load->library('zend');
		$this->zend->load('Zend/Barcode');   
		$barcodeOptions = array('drawText' => false,'text' => $batch_ID, 'barHeight' => '10','barThickWidth' => '6', 'barThinWidth' => '3','font' => '2','fontSize' => '18'); 
		$rendererOptions = array();
		$bc = Zend_Barcode::factory(
			'code39',
			'image',
			$barcodeOptions,
			$rendererOptions
		);
	 	$res = $bc->draw();
		$curpath = getcwd(); 
	 	imagepng($res, $curpath . '/tmp/' . $batch_ID . ".png");  
		  echo '<style type="text/css">
			@media print {
			input#btnPrint {
			display: none;
			}
			}
		</style> ';
		echo  "<div style=\"height:400px; background:#ffffff; \"><div style=\" font-size:10px; background:#ffffff; \">
		<div style=\"margin-left:-20px\; font-family:Verdana, Geneva, sans-serif\">
		<img align=\"left\"  src=\"" . $url . "tmp/" . $batch_ID . ".png\"alt=\"barcode\" />
		</div><br>
		<br>
		<div>" . "\n";	 
		echo   $text . '</div>
		</div>
		<input type="button" id="btnPrint" value="Print Label" onClick="self.print();"></div>'; 	 
	} 
	function output_charts(){
		require 'function.php';
		$url = $this->config->item('base_url');
		libraries($url); 
		$chart = $this->uri->segment(3);
		if ($_POST['start_d'] && $_POST['end_d']){
			$start_d = explode("/",$_POST['start_d']);
			$end_d = explode("/",$_POST['end_d']);  
			$start_date = mktime(1,1,1,$start_d[0],$start_d[1],$start_d['2']);
			$end_date = mktime(1,1,1,$end_d[0],$end_d[1],$end_d['2']);
			$this->db->where("record_date > ",$start_date);
			$this->db->where("record_date < ",$end_date); 
			$water_quality = $this->db->get("water_quality"); 
			echo '<h1 style="padding-left:10px;">Range ' . $_POST['start_d'] . ' to ' . $_POST['end_d'] . '</h1>';	
			echo '<div style="padding-top:50px;">';	 
			switch ($chart){
				case "nitrate":
				output_nitrate_chart($water_quality);
				break;
				case "nitrite":
				output_nitrite_chart($water_quality);
				break;
				case "ph":
				output_ph_chart($water_quality);
				break;
				case "conductivity":
				output_conductivity_chart($water_quality);
				break;
				case "do":
				output_do_chart($water_quality);
				break;
				case "temperature":
				output_temperature_chart($water_quality);
				break;
			}
			echo '</div>';
		}else{
			echo 'Please go back and select a date range.';	
		}
	}
	function submit_charts_data(){
		$chart = $this->uri->segment(3);
		$url = $this->config->item('base_url');
		echo '<form name="quick_submit" method="post" action="' . $url . 'index.php/fish/output_charts/' . $chart . '">';	
		echo '<input name="start_d" type="text">';
		echo '<input name="end_d" type="text">';
		echo '</form>';
		echo '<script language="javascript">';
		echo 'document.quick_submit.start_d.value = self.parent.document.crange_form_' .$chart . '.start_d.value;';
		echo 'document.quick_submit.end_d.value = self.parent.document.crange_form_' .$chart . '.end_d.value;';
		echo 'document.quick_submit.submit();';
		echo '</script>';
	}
} 
?>