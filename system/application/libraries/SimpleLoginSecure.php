<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.1/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using 
 * this setup you may need to do some tweaking.
 *   
 * 
 *   CREATE TABLE `users` (
 *     `user_id` int(10) unsigned NOT NULL auto_increment,
 *     `username` varchar(255) NOT NULL default '',
 *     `user_pass` varchar(60) NOT NULL default '',
 *     `user_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation date',
 *     `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
 *     `user_last_login` datetime NULL default NULL,
 *     PRIMARY KEY  (`user_id`),
 *     UNIQUE KEY `username` (`username`),
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 * @package   SimpleLoginSecure
 * @version   1.0.1
 * @author    Alex Dunae, Dialect <alex[at]dialect.ca>
 * @copyright Copyright (c) 2008, Alex Dunae
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      http://dialect.ca/code/ci-simple-login-secure/
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	 function get_pass_hash(){  
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$passhash= $hasher->HashPassword($_POST['user_pass']);
		return $passhash; 
	 }
	function create($username = '', $user_pass = '', $auto_login = true) 
	{
		$this->CI =& get_instance();
		


		//Make sure account info was sent
		if($username == '' OR $user_pass == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('username', $username); 
		$query = $this->CI->db->getwhere($this->user_table);
		
		if ($query->num_rows() > 0) //username already exists
			return false;

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);
		
		//Insert account into the database
		$data = array(
					'username' => $username,
					'user_pass' => $user_pass_hashed,
					'user_date' => date('c'),
					'user_modified' => date('c'), 
					'lab' => $_POST['lab'],
					'office_location' => $_POST['office_location'],
					'lab_location' => $_POST['lab_location'],
					'lab_phone' => $_POST['lab_phone'],
					'emergency_phone' => $_POST['emergency_phone'],
					'email' => $_POST['email'],
					'first_name' => $_POST['first_name'],
					'middle_name' => $_POST['middle_name'],
					'last_name' => $_POST['last_name'], 
					'admin_access' => $_POST['admin_access'] 
				);

		$this->CI->db->set($data); 
		if(!$this->CI->db->insert($this->user_table)) //There was a problem! 
			return false;  
		 
		/*$this->CI->session->set_userdata($user_data);
		
		if($auto_login)
			$this->login($username, $user_pass); */
		return true;
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($username = '', $user_pass = '') 
	{
		$this->CI =& get_instance();

		/*if($username == '' OR $user_pass == '')
			return false;*/


		//Check if already logged in
		 
		if($this->CI->session->userdata('username') == $username)
			return true;
		
		
		//Check against user table
		$this->CI->db->where('username', $username); 
		$query = $this->CI->db->getwhere($this->user_table);

		
		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass']))
			return false;

			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();

			//$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = NOW() WHERE user_ID = ' . $user_data['user_ID']);

			//Set session data
			unset($user_data['user_pass']);
			$user_data['user'] = $user_data['username'];
			$user_data['user_ID'] = $user_data['user_ID']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
			
			return true;
		} 
		else 
		{
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		

		$this->CI->session->sess_destroy();
	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_ID) 
	{
		$this->CI =& get_instance();
		
		if(!is_numeric($user_ID))
			return false;			

		return $this->CI->db->delete($this->user_table, array('user_ID' => $user_ID));
	}
	
}
?>
