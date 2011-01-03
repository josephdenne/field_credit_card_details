<?php

	Class extension_field_credit_card_details extends Extension {

		public function about() {
			return array('name' => 'Field: Credit Card Details',
						 'version' => '1.2',
						 'release-date' => '2011-01-03',
						 'author' => array('name' => 'Joseph Denne',
										   'website' => 'http://josephdenne.com/',
										   'email' => 'me@josephdenne.com')
				 		);
		}

		public function uninstall() {
			$this->_Parent->Database->query("DROP TABLE `tbl_fields_credit_card_details`");
		}

		public function install() {

			return $this->_Parent->Database->query("CREATE TABLE `tbl_fields_credit_card_details` (
			  `id` int(11) unsigned NOT NULL auto_increment,
			  `field_id` int(11) unsigned NOT NULL,
			  PRIMARY KEY  (`id`),
			  UNIQUE KEY `field_id` (`field_id`)
			) TYPE=MyISAM");
		}
	}

?>