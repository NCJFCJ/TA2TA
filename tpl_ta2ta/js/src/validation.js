/* -------------------------------------- *
 * This file contains functions for the   *
 * validation of forms.                   *
 *                                        *
 * IMPORTANT: Include only after jQuery   *
 *                                        *
 * Dependencies:                          *
 * - jQuery                               *
 * - Bootstrap 3                          *
 * - bootstrapHelper.js                   *
 * -------------------------------------- */


ta2ta.validate = ta2ta.validate || {


	/* --------- Regular Expressions -------- */

	regEx_date: /^((0?[1-9]|1[012])[-](0?[1-9]|[12][0-9]|3[01])[-](19|20)?[0-9]{2})*$/, // only allows dates formatted (m)m/(d)d/(yy)yy in 1900s or 2000s
	regEx_email: /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
	regEx_name: /^[a-zA-Z-\' ]*$/, // upper and lowercase letters with apostrophes, hyphens, and/or spaces
	regEx_password: /\w{5,}/, // 5 characters or more
	regEx_phone: /[\d() -ext\.]/, // digits with parenthesis, periods, and extension (no formatting check)
	regEx_time: /^(([1-9]|1[012])[:](0[0-9]|[12345][0-9])[ap][m])*$/, // a time string formatted hh:mm[am|pm]
	regEx_title: /^[a-zA-Z0-9-@&():,\[\]\'\"\-\.\/\\ ]*$/, // alpha numeric with select special characters
	regEx_unsigned: /^n?\d+$/, // unsigned integer
	regEx_url: /^(https?:\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(\d*)?)(\/((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=])|\/|\?)*)?$/,


	/* ----------- Field Validation --------- */

	/**
	 * Checks whether a checkbox or set of checkboxes has a selection.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int The minimum number of checkboxes that must be checked
	 *
	 * @return boolean True if valid, false otherwise.
	 */
	checkboxes: function(obj, minRequired){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		if(obj.length < minRequired){
			return false;
		}
		return true;
	},

	/**
	 * Checks whether a date is valid. Valid format is (m)m/(d)d/(yy)yy in 1900s or 2000s. Optionally shows the validation state on the field.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	date: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_date.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether an email is valid. Optionally shows the validation state on the field.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	email: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_email.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the supplied field has a value. Optionally shows the validation state on the field.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	hasValue: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(obj.val().trim() && obj.val() != '0', obj, showState);
	},

	/**
	 * Checks whether the values of two fields match. Optionally shows validation state on the second provided field.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	match: function(obj1, obj2, showState){
		// make sure both inputs exist, and if not return false
		if(!obj1.length || !obj2.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(obj1.val() && obj1.val() == obj2.val(), obj, showState);
	},

	/**
	 * Checks whether the value provided passes the maximum length requirements.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int The minimum length
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	maxLength: function(obj, length, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize length
		length = parseInt(length);
		if(length <= 0){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult((obj.val()).length <= length, obj, showState);
	},

	/**
	 * Checks whether the value provided passes the minimum length requirements.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int The minimum length
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	minLength: function(obj, length, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize length
		length = parseInt(length);
		if(length <= 0){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult((obj.val()).length >= length, obj, showState);
	},

	/**
	 * Checks whether the value provided passes the name regex.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	name: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_name.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the value provided passes the minimum password requirements.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	password: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_password.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the value provided passes the phone regex.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	phone: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_phone.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the value provided passes the time regex. Formatting allowed is hh:mm[am|pm]
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	time: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_time.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the value provided passes the title regex.
	 * 
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	title: function(obj, showState){
		// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_title.test(obj.val()), obj, showState);
	},

	/**
	 * Checks whether the value provided is an unsigned integer.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	 unsigned: function(obj, showState){
	 	// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_unsigned.test(obj.val()), obj, showState);
	 },

	/**
	 * Checks whether the value provided is a valid URL.
	 *
	 * @param object A jQuery object for a single form element.
	 * @param int Determines when to show the validation state:
	 *	   3 = success and failure
     *     2 = success only
     *     1 = failure only
     *     0 = don't show (default)
	 * @return boolean True if valid, false otherwise.
	 */
	 url: function(obj, showState){
	 	// make sure the input exists, and if not return false
		if(!obj.length){
			return false;
		}

		// sanitize showState
		showState = this.sanitizeShowState(showState);

		// check validity
		return this.validationResult(this.regEx_url.test(obj.val()), obj, showState);
	 },


	 /*************************************************************************************
	  * The following are internal functions not meant to be called from the public scope *
	  *************************************************************************************/


	 /**
	  * Establishes the default for the showState variable and ensures it is an integer
	  * in the appropriate range.
	  *
	  * @param int A value to sanitize, hopefully an integer
	  * @param int A clean, verified integer
	  */
	 sanitizeShowState: function(showState){
	 	// establish showState default
		showState = typeof showState !== 'undefined' ? showState : 0;

		// ensure the value is an integer
	 	showState = parseInt(showState);

	 	// ensure the integer is in range
		if(showState < 0 || showState > 3){
			showState = 0;
		}

		// return the resulting integer
		return showState;
	 },

	 /**
	  * Takes the result of a validation check and comples the appropriate actions
	  *
	  * @param boolean Whether or not the value provided to be valided is valid
	  * @param obj The jQuery object being tested
	  * @param int Determines when to show the validation state:
	  *	   3 = success and failure
      *    2 = success only
      *    1 = failure only
      *    0 = don't show (default)
      * @return
	  */ 
	 validationResult: function(result, obj, showState){
		if(result){
			// valid
			if(showState > 1){
				ta2ta.bootstrapHelper.showValidationState(obj, 'success', true);
			}
			return true;
		}else{
			// not valid
			if(showState && showState != 2){
				ta2ta.bootstrapHelper.showValidationState(obj, 'error', true);
			}
			return false;
		}
	 }
}