<?php

	Class fieldCredit_Card_Details extends Field {

		static private $_driver;

		function __construct(&$parent) {
			parent::__construct($parent);
			$this->_name = 'Credit Card Details';
			$this->_required = true;
			$this->set('required', 'yes');
		}

		function isSortable() {
			return true;
		}

		function canFilter() {
			return true;
		}

		function allowDatasourceOutputGrouping() {
			return true;
		}

		function allowDatasourceParamOutput() {
			return true;
		}

		function canPrePopulate() {
			return true;
		}

		public function mustBeUnique() {
			return true;
		}

		function groupRecords($records) {

			if(!is_array($records) || empty($records)) return;

			$groups = array($this->get('element_name') => array());

			foreach($records as $r) {
				$data = $r->getData($this->get('id'));

				$value = $data['type'];

				if(!isset($groups[$this->get('element_name')][$value])) {
					$groups[$this->get('element_name')][$value] = array('attr' => array('value' => $value),
						'records' => array(), 'groups' => array());
				}

				$groups[$this->get('element_name')][$value]['records'][] = $r;

			}

			return $groups;
		}

		function prepareTableValue($data, XMLElement $link=NULL) {
			return parent::prepareTableValue(array('value' => $data['type']), $link);
		}

		function displaySettingsPanel(&$wrapper, $errors=NULL) {
			parent::displaySettingsPanel($wrapper, $errors);
			$this->appendRequiredCheckbox($wrapper);
			$this->appendShowColumnCheckbox($wrapper);
		}

		function displayPublishPanel(&$wrapper, $data=NULL, $flagWithError=NULL, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL) {

			$div = new XMLElement('div', NULL, array('class' => 'group'));

			$type = $data['type'];
			$label = Widget::Label('Credit Card Type');
			if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));
			$label->appendChild(Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').'][type]'.$fieldnamePostfix, (strlen($type) != 0 ? $type : NULL)));
			$div->appendChild($label);

			$number = $data['number'];
			$label = Widget::Label('Credit Card Number');
			if($this->get('required') != 'yes') $label->appendChild(new XMLElement('i', 'Optional'));
			$label->appendChild(Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').'][number]'.$fieldnamePostfix, (strlen($number) != 0 ? $number : NULL)));
			$div->appendChild($label);

			if($flagWithError != NULL) $wrapper->appendChild(Widget::wrapFormElementWithError($div, $flagWithError));
			else $wrapper->appendChild($div);
		}

		public function checkPostFieldData($data, &$message, $entry_id=NULL) {

			$message = NULL;

			if($this->get('required') == 'yes' && (strlen($data['type']) == 0 || strlen($data['number']) == 0)) {
				$message = 'Credit card type and credit card number are required fields.';
				return self::__MISSING_FIELDS__;
			}

			if(strlen($data['number']) > 0 && !is_numeric(str_replace(" ", "", $data['number']))) {
				$message = 'Credit card number can only consist of numbers and spaces.';
				return self::__INVALID_FIELDS__;
			}

			// Validate the credit card number
			// Check for test number
			if ($data['number'] != "9999 9999 9999 9999") {

				$cardname = $data['type'];
				$cardnumber = $data['number'];

				$cards = array (array ('name' => 'American Express',
									  'length' => '15',
									  'prefixes' => '34,37',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Diners Club Carte Blanche',
									  'length' => '14',
									  'prefixes' => '300,301,302,303,304,305',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Diners Club',
									  'length' => '14,16',
									  'prefixes' => '305,36,38,54,55',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Discover',
									  'length' => '16',
									  'prefixes' => '6011,622,64,65',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Diners Club Enroute',
									  'length' => '15',
									  'prefixes' => '2014,2149',
									  'checkdigit' => true
									 ),
							   array ('name' => 'JCB',
									  'length' => '16',
									  'prefixes' => '35',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Maestro',
									  'length' => '12,13,14,15,16,18,19',
									  'prefixes' => '5018,5020,5038,6304,6759,6761',
									  'checkdigit' => true
									 ),
							   array ('name' => 'MasterCard',
									  'length' => '16',
									  'prefixes' => '51,52,53,54,55',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Solo',
									  'length' => '16,18,19',
									  'prefixes' => '6334,6767',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Switch',
									  'length' => '16,18,19',
									  'prefixes' => '4903,4905,4911,4936,564182,633110,6333,6759',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Visa',
									  'length' => '13,16',
									  'prefixes' => '4',
									  'checkdigit' => true
									 ),
							   array ('name' => 'Visa Electron',
									  'length' => '16',
									  'prefixes' => '417500,4917,4913,4508,4844',
									  'checkdigit' => true
									 ),
							   array ('name' => 'LaserCard',
									  'length' => '16,17,18,19',
									  'prefixes' => '6304,6706,6771,6709',
									  'checkdigit' => true
									 )
							);

				$ccErrorNo = 0;

				$ccErrors [0] = "Unknown card type.";
				$ccErrors [1] = "No card number provided.";
				$ccErrors [2] = "The credit card number provided has an invalid format.";
				$ccErrors [3] = "The credit card number provided is invalid.";
				$ccErrors [4] = "The credit card number provided is the wrong length.";

				// Establish card type
				$cardType = -1;
				for ($i=0; $i<sizeof($cards); $i++) {

					// See if it is this card (ignoring the case of the string)
					if (strtolower($cardname) == strtolower($cards[$i]['name'])) {
						$cardType = $i;
						break;
					}
				}

				// If card type not found, report an error
				if ($cardType == -1) {
					$errornumber = 0;
					$message = $ccErrors [$errornumber];
					return self::__INVALID_FIELDS__;
				}

				// Ensure that the user has provided a credit card number
				if (strlen($cardnumber) == 0)  {
					$errornumber = 1;
					$message = $ccErrors [$errornumber];
					return self::__INVALID_FIELDS__;
				}

				// Remove any spaces from the credit card number
				$cardNo = str_replace (' ', '', $cardnumber);

				// Check that the number is numeric and of the right sort of length.
				if (!eregi('^[0-9]{13,19}$',$cardNo))  {
					$errornumber = 2;
					$message = $ccErrors [$errornumber];
					return self::__INVALID_FIELDS__;
				}

				// Now check the modulus 10 check digit - if required
				if ($cards[$cardType]['checkdigit']) {
					$checksum = 0; // running checksum total
					$mychar = ""; // next char to process
					$j = 1; // takes value of 1 or 2

					// Process each digit one by one starting at the right
					for ($i = strlen($cardNo) - 1; $i >= 0; $i--) {

						// Extract the next digit and multiply by 1 or 2 on alternative digits.
						$calc = $cardNo{$i} * $j;

						// If the result is in two digits add 1 to the checksum total
						if ($calc > 9) {
							$checksum = $checksum + 1;
							$calc = $calc - 10;
						}

						// Add the units element to the checksum total
						$checksum = $checksum + $calc;

						// Switch the value of j
						if ($j ==1) {$j = 2;} else {$j = 1;};
					}

					// All done - if checksum is divisible by 10, it is a valid modulus 10.
					// If not, report an error.
					if ($checksum % 10 != 0) {
						$errornumber = 3;
						$message = $ccErrors [$errornumber];
						return self::__INVALID_FIELDS__;
					}
				}

				// The following are the card-specific checks we undertake.

				// Load an array with the valid prefixes for this card
				$prefix = split(',',$cards[$cardType]['prefixes']);

				// Now see if any of them match what we have in the card number
				$PrefixValid = false;
				for ($i=0; $i<sizeof($prefix); $i++) {
				$exp = '^' . $prefix[$i];
					if (ereg($exp,$cardNo)) {
						$PrefixValid = true;
						break;
					}
				}

				// If it isn't a valid prefix there's no point at looking at the length
				if (!$PrefixValid) {
					$errornumber = 3;
					$message = $ccErrors [$errornumber];
					return self::__INVALID_FIELDS__;
				}

				// See if the length is valid for this card
				$LengthValid = false;
				$lengths = split(',',$cards[$cardType]['length']);
				for ($j=0; $j<sizeof($lengths); $j++) {
					if (strlen($cardNo) == $lengths[$j]) {
						$LengthValid = true;
						break;
					}
				}

				// See if all is OK by seeing if the length was valid. 
				if (!$LengthValid) {
					$errornumber = 4;
					$message = $ccErrors [$errornumber];
					return self::__INVALID_FIELDS__;
				};
			}

			// The credit card is in the required format.
			return self::__OK__;
		}

		function appendFormattedElement(&$wrapper, $data, $encode=false) {
			if(!isset($data['type']) || !isset($data['number'])) return;
			$wrapper->appendChild(
				new XMLElement(
					$this->get('element_name'),
					NULL,
					array('type' => $data['type'], 'number' => $data['number'])
			));
		}

		private static function __hashit($data) {

			if(strlen($data) == 0) return;
			elseif(strlen($data) != 32 || !preg_match('@^[a-f0-9]{32}$@i', $data)) return md5($data);

			return $data;
		}


		public function processRawFieldData($data, &$status, $simulate=false, $entry_id=NULL) {

			$status = self::__OK__;

			return array(
				'type' => $data['type'],
				'number' => self::__hashit($data['number']),
			);
		}

		function commit() {

			if(!parent::commit()) return false;

			$id = $this->get('id');

			if($id === false) return false;

			$fields = array();

			$fields['field_id'] = $id;

			$this->_engine->Database->query("DELETE FROM `tbl_fields_".$this->handle()."` WHERE `field_id` = '$id' LIMIT 1");
			return $this->_engine->Database->insert($fields, 'tbl_fields_' . $this->handle());
		}

		public function createTable() {

			return $this->Database->query(

				"CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				  `id` int(11) unsigned NOT NULL auto_increment,
				  `entry_id` int(11) unsigned NOT NULL,
				  `type` varchar(25) default NULL,
				  `number` varchar(32) default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `entry_id` (`entry_id`),
				  KEY `number` (`number`)
				) TYPE=MyISAM;"

			);
		}

		public function getExampleFormMarkup() {

			$div = new XMLElement('div', NULL, array('class' => 'group'));
			$label = Widget::Label('Credit Card Type');
			$label->appendChild(Widget::Input('fields['.$this->get('element_name').'][type]'));
			$div->appendChild($label);

			$label = Widget::Label('Credit Card Number');
			$label->appendChild(Widget::Input('fields['.$this->get('element_name').'][number]', NULL, 'number'));
			$div->appendChild($label);

			return $div;
		}
	}

?>