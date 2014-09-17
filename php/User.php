<?php


class User {
	
	private $id;
	private $email;
	private $password;
	private $name;
	private $surname;
	private $phone;
	private $address;
	private $cap;
	private $city;
	private $province;
	private $country;
	private $groups; //Array of Group objects
	
	public function construct($id, $email, $password, $groups) {
		$this->id = $id;
		$this->email = $email;
		$this->password = $password;
		$this->groups = $groups;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	/*
	 * Check if the string passed by argument mathes user's password.
	 * @param String
	 * @returns boolean
	 */
	public function checkPassword($password) {
		return ($this->password == $password);
	}
	
	public function getGroups() {
		return $this->groups;
	}
	
}


?>