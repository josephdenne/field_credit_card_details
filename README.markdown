# Field: Credit Card Details

* Version: 1.2
* Author: Joseph Denne (me@josephdenne.com)
* Build Date: 03rd January 2011
* Requirements: Symphony 2.0.3 or later

## Summary

Provides a validated field for credit card type and number, designed to be used ahead of payment submission.

Verifying card details before submission reduces the chance of fraudulent activity. It is also a prerequisite for many card processing authorities.

### Validation

Credit numbers are validated on a card-type basis. Validation checks for:

* Number length
* Number prefix(es)
* Modulus 10 check digit(s)

### Supported credit card types

* American Express
* Diners Club Carte Blanche
* Diners Club
* Discover
* Diners Club Enroute
* JCB
* Maestro
* MasterCard
* Solo
* Switch
* Visa
* Visa Electron
* LaserCard

### Bypassing card validation

You can bypass card validation by using the credit card number: 9999 9999 9999 9999

## Installation

** Note: The latest version can alway be grabbed with
"git clone git@github.com:josephdenne/field_credit_card_details.git"

1. Rename the extension folder to 'field_credit_card_details' and upload it to your Symphony 'extensions' folder
2. Enable it by selecting "Field: Credit Card Details", choosing "Enable" from the with-selected menu, then clicking "Apply"

## Usage

1. Add the "Credit Card Details" field to your section(s)

## Example front end visualisation

![Data Source attached to a page and working](http://josephdenne.com/workspace/images/screenshots/field-credit-card-details/front-end-output.png)

[CHANGES]

1.2
- Fixed bug with MasterCard verification routine

1.1
- Added support for multiple credit card types

You can contact me directly at me@josephdenne.com