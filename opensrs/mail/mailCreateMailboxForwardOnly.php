<?php
/*
 *  Required object values:
 *  data - 
 */
 
class mailCreateMailboxForwardOnly extends openSRS_mail {
	private $_dataObject;
	private $_formatHolder = "";
	private $_osrsm;
	
	public $resultRaw;
	public $resultFormatted;
	public $resultSuccess;

	public function __construct ($formatString, $dataObject) {
		parent::__construct();

		$this->_dataObject = $dataObject;
		$this->_formatHolder = $formatString;
		$this->_validateObject ();
	}

	public function __destruct () {
		parent::__destruct();
	}

	// Validate the object
	private function _validateObject (){
		$allPassed = true;
		$compile = "";
		
		// Command required values - authentication
		if (!isSet($this->_dataObject->data->admin_username) || $this->_dataObject->data->admin_username == "") {
			if (APP_MAIL_USERNAME == ""){
				trigger_error ("oSRS-eMail Error - username is not defined.", E_USER_WARNING);
				$allPassed = false;
			} else {
				$this->_dataObject->data->admin_username = APP_MAIL_USERNAME;
			}
		}
		if (!isSet($this->_dataObject->data->admin_password) || $this->_dataObject->data->admin_password == "") {
			if (APP_MAIL_PASSWORD == ""){
				trigger_error ("oSRS-eMail Error - password is not defined.", E_USER_WARNING);
				$allPassed = false;
			} else {
				$this->_dataObject->data->admin_password = APP_MAIL_PASSWORD;
			}
		}
		if (!isSet($this->_dataObject->data->admin_domain) || $this->_dataObject->data->admin_domain == "") {
			if (APP_MAIL_DOMAIN == ""){
				trigger_error ("oSRS-eMail Error - authentication domain is not defined.", E_USER_WARNING);
				$allPassed = false;
			} else {
				$this->_dataObject->data->admin_domain = APP_MAIL_DOMAIN;
			}
		}
						
		// Command required values
		if (!isSet($this->_dataObject->data->domain) || $this->_dataObject->data->domain == "") {
			trigger_error ("oSRS-eMail Error - domain is not defined.", E_USER_WARNING);
			$allPassed = false;
		} else {
			$compile .= " domain=\"". $this->_dataObject->data->domain ."\"";
		}
		
		if (!isSet($this->_dataObject->data->workgroup) || $this->_dataObject->data->workgroup == "") {
			trigger_error ("oSRS-eMail Error - workgroup is not defined.", E_USER_WARNING);
			$allPassed = false;
		} else {
			$compile .= " workgroup=\"". $this->_dataObject->data->workgroup ."\"";
		}
		
		if (!isSet($this->_dataObject->data->mailbox) || $this->_dataObject->data->mailbox == "") {
			trigger_error ("oSRS-eMail Error - mailbox is not defined.", E_USER_WARNING);
			$allPassed = false;
		} else {
			$compile .= " mailbox=\"". $this->_dataObject->data->mailbox ."\"";
		}
		
		if (!isSet($this->_dataObject->data->forward_email) || $this->_dataObject->data->forward_email == "") {
			trigger_error ("oSRS-eMail Error - forwarding email is not defined.", E_USER_WARNING);
			$allPassed = false;
		} else {
			$compile .= " forward_email=\"". $this->_dataObject->data->forward_email ."\"";
		}
		
		// Command optional values
		if (isSet($this->_dataObject->data->spam_level) && $this->_dataObject->data->spam_level != "")
                        $compile .= " spam_level=\"". $this->_dataObject->data->spam_level ."\"";
		
		
		// Run the command
		if ($allPassed) {
			// Execute the command
			$this->_processRequest ($compile);
		} else {
			trigger_error ("oSRS-eMail Error - Missing data.", E_USER_WARNING);
		}
	}
	
	// Post validation functions
	private function _processRequest ($command = ""){
		$sequence = array (
			0 => "ver ver=\"3.4\"",
			1 => "login user=\"". $this->_dataObject->data->admin_username ."\" domain=\"". $this->_dataObject->data->admin_domain ."\" password=\"". $this->_dataObject->data->admin_password ."\"",
			2 => "create_mailbox_forward_only". $command,
			3 => "quit"
		);		
		$tucRes = $this->makeCall($sequence);
		$arrayResult = $this->parseResults($tucRes);
		
		// Results
		$this->resultFullRaw = $arrayResult;
		$this->resultRaw = $arrayResult;
		$this->resultFullFormatted = convertArray2Formatted ($this->_formatHolder, $this->resultFullRaw);
		$this->resultFormatted = convertArray2Formatted ($this->_formatHolder, $this->resultRaw);
	}
}
