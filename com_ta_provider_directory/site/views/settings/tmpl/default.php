<?php
/**
 * @version     2.0.0
 * @package     com_ta_provider_directory
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     
 * @author      Zachary Draper <zdraper@ncjfcj.org> - http://ncjfcj.org
 */
 
// no direct access
defined('_JEXEC') or die;

// require the helper file
require_once(JPATH_COMPONENT . '/helpers/ta_provider_directory.php');

/* Get the permission level
 * 0 = Public (view only)
 * 1 = TA Provider (restricted to adding and editing own)
 * 2 = Administrator (full access and ability to edit)
 */
$permission_level = Ta_provider_directoryHelper::getPermissionLevel();

// before continuing, we need to handle quotes in the PHP data
foreach($this->listing->projects as &$project){
    $project->title = htmlentities($project->title, ENT_QUOTES, 'UTF-8', false);
    $project->title = str_replace('&', '&amp;', $project->title); // I have no idea why this is necessary or why it works.
    $project->summary = htmlentities($project->summary, ENT_QUOTES, 'UTF-8', false);
    $project->summary = str_replace('&', '&amp;', $project->summary); // I have no idea why this is necessary or why it works.
    foreach($project->contacts as &$contact){
        $contact->first_name = htmlentities($contact->first_name, ENT_QUOTES, 'UTF-8', false);
        $contact->first_name = str_replace('&', '&amp;', $contact->first_name); // I have no idea why this is necessary or why it works.
        $contact->last_name = htmlentities($contact->last_name, ENT_QUOTES, 'UTF-8', false);
        $contact->last_name = str_replace('&', '&amp;', $contact->last_name); // I have no idea why this is necessary or why it works.
        $contact->title = htmlentities($contact->title, ENT_QUOTES, 'UTF-8', false);
        $contact->title = str_replace('&', '&amp;', $contact->title); // I have no idea why this is necessary or why it works.
    }
}

JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">/**
 * A Javascript object to encode and/or decode html characters using HTML or Numeric entities that handles double or partial encoding
 * Author: R Reid
 * source: http://www.strictly-software.com/htmlencode
 * Licences: GPL, The MIT License (MIT)
 * Copyright: (c) 2011 Robert Reid - Strictly-Software.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 * Revision:
 *  2011-07-14, Jacques-Yves Bleau: 
 *       - fixed conversion error with capitalized accentuated characters
 *       + converted arr1 and arr2 to object property to remove redundancy
 *
 * Revision:
 *  2011-11-10, Ce-Yi Hio: 
 *       - fixed conversion error with a number of capitalized entity characters
 *
 * Revision:
 *  2011-11-10, Rob Reid: 
 *       - changed array format
 *
 * Revision:
 *  2012-09-23, Alex Oss: 
 *       - replaced string concatonation in numEncode with string builder, push and join for peformance with ammendments by Rob Reid
 */
Encoder = {

    // When encoding do we convert characters into html or numerical entities
    EncodeType : "entity",  // entity OR numerical

    isEmpty : function(val){
        if(val){
            return ((val===null) || val.length==0 || /^\s+$/.test(val));
        }else{
            return true;
        }
    },
    
    // arrays for conversion from HTML Entities to Numerical values
    arr1: ['&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;','&quot;','&amp;','&lt;','&gt;','&OElig;','&oelig;','&Scaron;','&scaron;','&Yuml;','&circ;','&tilde;','&ensp;','&emsp;','&thinsp;','&zwnj;','&zwj;','&lrm;','&rlm;','&ndash;','&mdash;','&lsquo;','&rsquo;','&sbquo;','&ldquo;','&rdquo;','&bdquo;','&dagger;','&Dagger;','&permil;','&lsaquo;','&rsaquo;','&euro;','&fnof;','&Alpha;','&Beta;','&Gamma;','&Delta;','&Epsilon;','&Zeta;','&Eta;','&Theta;','&Iota;','&Kappa;','&Lambda;','&Mu;','&Nu;','&Xi;','&Omicron;','&Pi;','&Rho;','&Sigma;','&Tau;','&Upsilon;','&Phi;','&Chi;','&Psi;','&Omega;','&alpha;','&beta;','&gamma;','&delta;','&epsilon;','&zeta;','&eta;','&theta;','&iota;','&kappa;','&lambda;','&mu;','&nu;','&xi;','&omicron;','&pi;','&rho;','&sigmaf;','&sigma;','&tau;','&upsilon;','&phi;','&chi;','&psi;','&omega;','&thetasym;','&upsih;','&piv;','&bull;','&hellip;','&prime;','&Prime;','&oline;','&frasl;','&weierp;','&image;','&real;','&trade;','&alefsym;','&larr;','&uarr;','&rarr;','&darr;','&harr;','&crarr;','&lArr;','&uArr;','&rArr;','&dArr;','&hArr;','&forall;','&part;','&exist;','&empty;','&nabla;','&isin;','&notin;','&ni;','&prod;','&sum;','&minus;','&lowast;','&radic;','&prop;','&infin;','&ang;','&and;','&or;','&cap;','&cup;','&int;','&there4;','&sim;','&cong;','&asymp;','&ne;','&equiv;','&le;','&ge;','&sub;','&sup;','&nsub;','&sube;','&supe;','&oplus;','&otimes;','&perp;','&sdot;','&lceil;','&rceil;','&lfloor;','&rfloor;','&lang;','&rang;','&loz;','&spades;','&clubs;','&hearts;','&diams;'],
    arr2: ['&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;','&#34;','&#38;','&#60;','&#62;','&#338;','&#339;','&#352;','&#353;','&#376;','&#710;','&#732;','&#8194;','&#8195;','&#8201;','&#8204;','&#8205;','&#8206;','&#8207;','&#8211;','&#8212;','&#8216;','&#8217;','&#8218;','&#8220;','&#8221;','&#8222;','&#8224;','&#8225;','&#8240;','&#8249;','&#8250;','&#8364;','&#402;','&#913;','&#914;','&#915;','&#916;','&#917;','&#918;','&#919;','&#920;','&#921;','&#922;','&#923;','&#924;','&#925;','&#926;','&#927;','&#928;','&#929;','&#931;','&#932;','&#933;','&#934;','&#935;','&#936;','&#937;','&#945;','&#946;','&#947;','&#948;','&#949;','&#950;','&#951;','&#952;','&#953;','&#954;','&#955;','&#956;','&#957;','&#958;','&#959;','&#960;','&#961;','&#962;','&#963;','&#964;','&#965;','&#966;','&#967;','&#968;','&#969;','&#977;','&#978;','&#982;','&#8226;','&#8230;','&#8242;','&#8243;','&#8254;','&#8260;','&#8472;','&#8465;','&#8476;','&#8482;','&#8501;','&#8592;','&#8593;','&#8594;','&#8595;','&#8596;','&#8629;','&#8656;','&#8657;','&#8658;','&#8659;','&#8660;','&#8704;','&#8706;','&#8707;','&#8709;','&#8711;','&#8712;','&#8713;','&#8715;','&#8719;','&#8721;','&#8722;','&#8727;','&#8730;','&#8733;','&#8734;','&#8736;','&#8743;','&#8744;','&#8745;','&#8746;','&#8747;','&#8756;','&#8764;','&#8773;','&#8776;','&#8800;','&#8801;','&#8804;','&#8805;','&#8834;','&#8835;','&#8836;','&#8838;','&#8839;','&#8853;','&#8855;','&#8869;','&#8901;','&#8968;','&#8969;','&#8970;','&#8971;','&#9001;','&#9002;','&#9674;','&#9824;','&#9827;','&#9829;','&#9830;'],
        
    // Convert HTML entities into numerical entities
    HTML2Numerical : function(s){
        return this.swapArrayVals(s,this.arr1,this.arr2);
    },  

    // Convert Numerical entities into HTML entities
    NumericalToHTML : function(s){
        return this.swapArrayVals(s,this.arr2,this.arr1);
    },


    // Numerically encodes all unicode characters
    numEncode : function(s){ 
        if(this.isEmpty(s)) return ""; 

        var a = [],
            l = s.length; 
        
        for (var i=0;i<l;i++){ 
            var c = s.charAt(i); 
            if (c < " " || c > "~"){ 
                a.push("&#"); 
                a.push(c.charCodeAt()); //numeric value of code point 
                a.push(";"); 
            }else{ 
                a.push(c); 
            } 
        } 
        
        return a.join("");  
    }, 
    
    // HTML Decode numerical and HTML entities back to original values
    htmlDecode : function(s){

        var c,m,d = s;
        
        if(this.isEmpty(d)) return "";

        // convert HTML entites back to numerical entites first
        d = this.HTML2Numerical(d);
        
        // look for numerical entities &#34;
        arr=d.match(/&#[0-9]{1,5};/g);
        
        // if no matches found in string then skip
        if(arr!=null){
            for(var x=0;x<arr.length;x++){
                m = arr[x];
                c = m.substring(2,m.length-1); //get numeric part which is refernce to unicode character
                // if its a valid number we can decode
                if(c >= -32768 && c <= 65535){
                    // decode every single match within string
                    d = d.replace(m, String.fromCharCode(c));
                }else{
                    d = d.replace(m, ""); //invalid so replace with nada
                }
            }           
        }

        return d;
    },      

    // encode an input string into either numerical or HTML entities
    htmlEncode : function(s,dbl){
            
        if(this.isEmpty(s)) return "";

        // do we allow double encoding? E.g will &amp; be turned into &amp;amp;
        dbl = dbl || false; //default to prevent double encoding
        
        // if allowing double encoding we do ampersands first
        if(dbl){
            if(this.EncodeType=="numerical"){
                s = s.replace(/&/g, "&#38;");
            }else{
                s = s.replace(/&/g, "&amp;");
            }
        }

        // convert the xss chars to numerical entities ' " < >
        s = this.XSSEncode(s,false);
        
        if(this.EncodeType=="numerical" || !dbl){
            // Now call function that will convert any HTML entities to numerical codes
            s = this.HTML2Numerical(s);
        }

        // Now encode all chars above 127 e.g unicode
        s = this.numEncode(s);

        // now we know anything that needs to be encoded has been converted to numerical entities we
        // can encode any ampersands & that are not part of encoded entities
        // to handle the fact that I need to do a negative check and handle multiple ampersands &&&
        // I am going to use a placeholder

        // if we don't want double encoded entities we ignore the & in existing entities
        if(!dbl){
            s = s.replace(/&#/g,"##AMPHASH##");
        
            if(this.EncodeType=="numerical"){
                s = s.replace(/&/g, "&#38;");
            }else{
                s = s.replace(/&/g, "&amp;");
            }

            s = s.replace(/##AMPHASH##/g,"&#");
        }
        
        // replace any malformed entities
        s = s.replace(/&#\d*([^\d;]|$)/g, "$1");

        if(!dbl){
            // safety check to correct any double encoded &amp;
            s = this.correctEncoding(s);
        }

        // now do we need to convert our numerical encoded string into entities
        if(this.EncodeType=="entity"){
            s = this.NumericalToHTML(s);
        }

        return s;                   
    },

    // Encodes the basic 4 characters used to malform HTML in XSS hacks
    XSSEncode : function(s,en){
        if(!this.isEmpty(s)){
            en = en || true;
            // do we convert to numerical or html entity?
            if(en){
                s = s.replace(/\'/g,"&#39;"); //no HTML equivalent as &apos is not cross browser supported
                s = s.replace(/\"/g,"&quot;");
                s = s.replace(/</g,"&lt;");
                s = s.replace(/>/g,"&gt;");
            }else{
                s = s.replace(/\'/g,"&#39;"); //no HTML equivalent as &apos is not cross browser supported
                s = s.replace(/\"/g,"&#34;");
                s = s.replace(/</g,"&#60;");
                s = s.replace(/>/g,"&#62;");
            }
            return s;
        }else{
            return "";
        }
    },

    // returns true if a string contains html or numerical encoded entities
    hasEncoded : function(s){
        if(/&#[0-9]{1,5};/g.test(s)){
            return true;
        }else if(/&[A-Z]{2,6};/gi.test(s)){
            return true;
        }else{
            return false;
        }
    },

    // will remove any unicode characters
    stripUnicode : function(s){
        return s.replace(/[^\x20-\x7E]/g,"");
        
    },

    // corrects any double encoded &amp; entities e.g &amp;amp;
    correctEncoding : function(s){
        return s.replace(/(&amp;)(amp;)+/,"$1");
    },


    // Function to loop through an array swaping each item with the value from another array e.g swap HTML entities with Numericals
    swapArrayVals : function(s,arr1,arr2){
        if(this.isEmpty(s)) return "";
        var re;
        if(arr1 && arr2){
            //ShowDebug("in swapArrayVals arr1.length = " + arr1.length + " arr2.length = " + arr2.length)
            // array lengths must match
            if(arr1.length == arr2.length){
                for(var x=0,i=arr1.length;x<i;x++){
                    re = new RegExp(arr1[x], 'g');
                    s = s.replace(re,arr2[x]); //swap arr1 item with matching item from arr2    
                }
            }
        }
        return s;
    },

    inArray : function( item, arr ) {
        for ( var i = 0, x = arr.length; i < x; i++ ){
            if ( arr[i] === item ){
                return i;
            }
        }
        return -1;
    }

}
    jQuery(function($){
    	// draw the table 
    	reloadProjectTable();	
    	
    	$('#formSubmit').click(function(event){
    		event.preventDefault();
    		$('#form-settings')[0].submit();
    	});
    	
    	$('#formReset').click(function(event){
    		event.preventDefault();
    		location.reload();
    	});

        // check if the user is trying to add a new project or edit, and if so, open the dialog
        var editId = window.location.href.split('?edit=')[1];
        if(editId !== undefined){
            editId = parseInt(editId);
            openProjectModal(editId);
        }

        /* ----- Project Form Field Live Validation ----- */

        // title
        $('#jform_project_title').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),5)
            || !ta2ta.validate.maxLength($(this),255)
            || !ta2ta.validate.title($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // summary
        $('#jform_project_summary').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),20)
            || !ta2ta.validate.maxLength($(this),1500)){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // award_number
        var award_number_regex = /^20[0-9]{2}-[A-Z]{2}-[A-Z]{2}-[A-Z][0-9]{3}$/;
        $('#jform_project_award_number').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),15)
            || !ta2ta.validate.maxLength($(this),15)
            || !ta2ta.validate.regex($(this), award_number_regex)){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        /* ----- Contact Form Field Live Validation ----- */

        // first name
        $('#jform_contact_first_name').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),30)
            || !ta2ta.validate.name($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // last name  
        $('#jform_contact_last_name').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),30)
            || !ta2ta.validate.name($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });    

        // title
        $('#jform_contact_title').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),2)
            || !ta2ta.validate.maxLength($(this),100)
            || !ta2ta.validate.title($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // email
        $('#jform_contact_email').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),3)
            || !ta2ta.validate.maxLength($(this),150)
            || !ta2ta.validate.email($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });

        // phone
        $('#jform_contact_phone').change(function(){
            if(!ta2ta.validate.hasValue($(this))
            || !ta2ta.validate.minLength($(this),10)
            || !ta2ta.validate.maxLength($(this),25) 
            || !ta2ta.validate.phone($(this))){
                ta2ta.bootstrapHelper.showValidationState($(this), 'error', true);
            }else{
                ta2ta.bootstrapHelper.showValidationState($(this), 'success', true);
            }
        });
    });
    
    // project form functions
    var newID = 1;
    
    /**
	 * Function to close the contact form modal and clear the contents of the form
	 *
	 * @return	null
	 */
    function closeContactModal(){
    	// clear old data
    	jQuery('#contactForm input').val('');
    	jQuery('#jform_contact_state').val('1');
    	jQuery('#jform_contact_state').trigger('liszt:updated');
    	
    	// clear any old alerts
        ta2ta.bootstrapHelper.removeAlert(jQuery('#contactFormModal').find('.modal-body'));
    	
    	// close the modal
    	jQuery('#contactFormModal').modal('hide');
    }
    
    /**
	 * Function to close the project form modal and clear the contents of the form
	 *
	 * @return	null
	 */
    function closeProjectModal(){
    	// clear old data
    	jQuery('#jform_project_state').val('1');
    	jQuery('#jform_project_state').trigger('liszt:updated');
    	jQuery('#jform_project_title, #jform_project_summary, #jform_project_award_number, #jform_project_contacts').val('');
    	jQuery('#projectGrants input:checked').removeAttr('checked');
    	
    	// clear any old alerts
        ta2ta.bootstrapHelper.removeAlert(jQuery('#projectFormModal').find('.modal-body'));
        ta2ta.bootstrapHelper.removeAlert(jQuery('#contactGridAlertWrapper'));
    	
    	// close the modal
    	jQuery('#projectFormModal').modal('hide');
    }
    
    /**
     * Function to edit a single project
     * 
     * @return null
     */
    function editContact(){
    	var id = getSelectedIds('contactsList', true);
    	if(id != ''){
    		openContactModal(id);
    	}else{
    		// the user did not select anything
            ta2ta.bootstrapHelper.showAlert(jQuery('#contactGridAlertWrapper'), 'Please select a contact to edit by clicking the checkbox to the left of the contact name, or clicking directly on its name.');
    	}
    }
    
    /**
     * Function to edit a single project
     * 
     * @return null
     */
    function editProject(){
    	var id = getSelectedIds('projectsList', true);
    	if(id != ''){
    		openProjectModal(id);
    	}else{
    		// the user did not select anything
            ta2ta.bootstrapHelper.showAlert(jQuery('#gridAlertWrapper'), 'Please select a project to edit by clicking the checkbox to the left of the project name, or clicking directly on its name.', false, true);
    	}
    }
    
    /**
     * Function to format a phone number
     * 
     * @return string The phone number, digits only
     */
    function formatPhoneNumber(phone){
    	if(phone){
    		var formattedNumber = '';
    		if(phone.charAt(0) == '1'){
    			// this is a 1-800 or similar type number, format accordingly
    			formattedNumber = phone.substr(0,1) + '-' + phone.substr(1,3) + '-' + phone.substr(4,3) + '-' + phone.substr(7,4);
    			// if there is an extension, add it
    			if(phone.length > 11){
    				formattedNumber += ' ext. ' + phone.substring(11);
    			}
    		}else{
    			// this is a normal number
    			formattedNumber = '(' + phone.substr(0,3) + ') ' + phone.substr(3,3) + '-' + phone.substr(6,4);
    			// if there is an extension, add it
    			if(phone.length > 10){
    				formattedNumber += ' ext. ' + phone.substring(10);
    			}
    		}
    		return formattedNumber;
    	}
    	return '';
    }
        
    /**
	 * Function to obtain the ids of all records selected in the table, optionally limited to only a portion of those items
	 *
	 * @param 	string The name of the table
	 * @param	boolean Whether to return only the first value, or all
	 *
	 * @return	mixed string if single, array of strings otherwise, false on failure
	 */
    function getSelectedIds(table, single){
    	var ids = new Array();
    	jQuery('#' + table + ' input:checked').each(function(){
    		var id = jQuery(this).attr('id');
    		if(id){
	    		ids.push(id.replace('cb',''));
	    		if(single){
	    			return false;
	    		}
    		}
    	});
    	    	
    	// check that the array contains data
    	if(ids.length > 0){
    		// if single, return only the first one
    		if(single){
    			return ids[0];
    		}else{
    			return ids;
    		}
    	}else{
    		return false;
    	}
    }
    
    /**
	 * Function to open the contact form modal, changing the heading and loading information if necessary
	 *
	 * @param	string The ID of a single record
	 *
	 * @return	null
	 */
    function openContactModal(id){
    	// change the heading based on whether adding or editing a record
    	if(id != '0'){
    		jQuery('#contactFormModal .modal-header h3').text('Edit Contact');
    		jQuery('#contactSaveBtn').text('Save Changes');
    		
    		// load the data for this contact
    		contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
    		jQuery.each(contacts, function(index,contact){
    			if(contact.id == id){
    				jQuery('#jform_contact_state').val(contact.state);
    				jQuery('#jform_contact_state').trigger('liszt:updated');
    				jQuery('#jform_contact_first_name').val(Encoder.htmlDecode(contact.first_name));
					jQuery('#jform_contact_last_name').val(Encoder.htmlDecode(contact.last_name));
					jQuery('#jform_contact_title').val(Encoder.htmlDecode(contact.title));
    				jQuery('#jform_contact_phone').val(formatPhoneNumber(contact.phone));
					jQuery('#jform_contact_email').val(contact.email);
	    			
	    			// stop looping
    				return false;
    			}
    		});    	
    	}else{
    		jQuery('#contactFormModal .modal-header h3').text('Add Contact');
    		jQuery('#contactSaveBtn').text('Save');
    	}
    	
    	// set the height of this box to match that of the parent box
    	jQuery('#contactFormModal .modal-body').height(jQuery('#projectFormModal .modal-body').height());
    	
    	// set the ID
    	jQuery('#jform_contactID').val(id);
    	
    	// open the modal
    	jQuery('#contactFormModal').modal('show');
    }
    
    /**
	 * Function to open the project form modal, changing the heading and loading information if necessary
	 *
	 * @param	string The ID of a single record
	 *
	 * @return	null
	 */
    function openProjectModal(id){
    	// change the heading based on whether adding or editing a record
    	if(id != '0'){
    		jQuery('#projectFormModal .modal-header h3').text('Edit Project');
    		jQuery('#projectSaveBtn').text('Save Changes');
    		
    		// load the data for this project
    		projects = jQuery.parseJSON(jQuery('#jform_projects').val());
    		jQuery.each(projects, function(index,project){
    			if(project.id == id){
    				jQuery('#jform_project_state').val(project.state);
    				jQuery('#jform_project_state').trigger('liszt:updated');
    				jQuery('#jform_project_title').val(Encoder.htmlDecode(project.title));
    				jQuery('#jform_project_summary').val(Encoder.htmlDecode(project.summary));
                    <?php if($permission_level == 2): ?>
                    jQuery('#jform_project_award_number').val(project.award_number);
                    if(project.grantPrograms instanceof Array){
                        jQuery('#projectGrants input').each(function(){
                            // check the appropriate grant programs
                            if(jQuery.inArray(jQuery(this).val(), project.grantPrograms) >= 0){
                                jQuery(this).attr('checked', true);
                            }
                        });
                    }
    				<?php endif; ?>
                    jQuery('#jform_project_contacts').val(JSON.stringify(project.contacts));
    				
                    <?php if($permission_level < 2): ?>
                    jQuery("#jform_project_award_number").closest('.form-group').hide();
                    jQuery("#projectGrants").closest('.panel').hide();
                    <?php endif; ?>
	    			
                    // update the contact table
	    			if(project.contacts == ''){
	    				jQuery('#contactsList thead').hide();
	    			}else{
	    				jQuery('#contactsList thead').show();
	    				reloadContactTable(project.contacts);
	    			}
	    			
	    			// stop looping
    				return false;
    			}
    		});    		
    	}else{
    		jQuery('#projectFormModal .modal-header h3').text('Add Project');
    		jQuery('#projectSaveBtn').text('Save');
            jQuery("#jform_project_award_number").closest('.form-group').show();
            jQuery("#projectGrants").closest('.panel').show();
    		var contacts = new Array();
    		jQuery('#jform_project_contacts').val(JSON.stringify(contacts));    				
    		reloadContactTable(contacts);
    	}
    	
    	// set the ID
    	jQuery('#jform_projectID').val(id);
    	
    	// open the modal
    	jQuery('#projectFormModal').modal('show');
    }
    
    /**
     * Function to reload the contacts table
     * 
     * @param object List of contacts
     * 
     * @return null
     */
    
    function reloadContactTable(contacts){
    	// get the table
    	var classInt = 0;
    	var firstDrawn = false;
    	var table = jQuery('#contactsList');
    	var rows = '<tr><td colspan="4" class="center no-records">There are no contacts associated with this project. <a onclick="openContactModal(0);">Try adding one.</a></td></tr>';
    	// check if we have projects to display
    	jQuery.each(contacts, function(index,contact){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + contact.id + '" type="checkbox" value="1" name="cid[]"></input></td><td>' + (contact.state == 1 ? '' : '<span class="icomoon-remove"></span> ') + '<a onclick="openContactModal(\'' + contact.id + '\');" href="javascript:void(0);">' + contact.first_name + ' ' + contact.last_name + '</a></td><td>' + contact.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#contactsToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#contactsToolbarEdit').css('visibility', 'hidden');
		}
		
		// clear the check all checkbox
		jQuery('#contactsList input[name="checkall-toggle"]').attr('checked', false);
		
		if(contacts == ''){
			jQuery('#contactsList thead').hide();
		}else{
			jQuery('#contactsList thead').show();
		}
    	
    	// update the table with this content
    	table.find('tbody').html(rows);
    }
    
    /**
     * Function to reload the projects table
     * 
     * @param object List of projects
     * 
     * @return null
     */
    
    function reloadProjectTable(projects){
    	// get the table
    	var classInt = 0;
    	var firstDrawn = false;
    	var table = jQuery('#projectsList');
    	var rows = '<tr><td colspan="5" class="center no-records">There are no projects associated with this TA Provider. <a href="javascript:void(0);" onclick="openProjectModal(0);">Try adding one.</a></td></tr>';
    	
    	// check if projects has a value, and if not grab it from the form
    	if(!projects){
    		projects = jQuery.parseJSON(jQuery('#jform_projects').val());
    	}
    	
    	// check if we have projects to display
    	jQuery.each(projects, function(index,project){
			if(!firstDrawn){
    			rows = '';
    			firstDrawn = true;
    		}
    		rows += '<tr class="row' + classInt + '"><td><input id="cb' + project.id + '" type="checkbox" value="1" name="cid[]"></input></td><td>' + (project.state == 1 ? '' : '<span class="icomoon-remove"></span> ') + '<a href="javascript:void(0);" onclick="openProjectModal(\'' + project.id + '\');">' + project.title + '</a></td><td>' + project.created_by + '</td><td>' + project.id + '</td></tr>';
    		classInt = (classInt ? 0 : 1);
		});
		
		// show/hide applicable toolbar buttons
		if(firstDrawn){
			jQuery('#projectsToolbarEdit').css('visibility', 'visible');
		}else{
			jQuery('#projectsToolbarEdit').css('visibility', 'hidden');
		}
		
		if(projects == ''){
			jQuery('#projectsList thead').hide();
		}else{
			jQuery('#projectsList thead').show();
		}
		
		// clear the check all checkbox
		jQuery('#projectsList input[name="checkall-toggle"]').attr('checked', false);
    	
    	// update the table with this content
    	table.find('tbody').html(rows);
    }
    
    /**
     * Function to save the contact information in the modal form
     * 
     * @return null (shows messages to user)
     */
    function saveContact(){
        // variables
        var alertParent = jQuery('#contactFormModal').find('.modal-body');
        var data = new Object;
        var errors = [];
        
    	// close any residual alerts and clear validation states
    	ta2ta.bootstrapHelper.removeAlert(alertParent);
        ta2ta.bootstrapHelper.hideAllValidationStates();
        
 		/* validate the form */
    	
    	// id
        data.id = jQuery('#jform_contactID').val();
        if(!ta2ta.validate.unsigned(jQuery('#jform_contactID'))){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
        }
    	
    	// state
    	var state = jQuery('#jform_contact_state').val();
    	if(state == '-1' 
    	|| state == '0' 
    	|| state == '1'){
    		data.state = state;
    	}else{
    		data.state = 1;
    	}

    	// first name
    	data.first_name = jQuery('#jform_contact_first_name').val();
    	if(ta2ta.validate.hasValue(jQuery('#jform_contact_first_name'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_first_name'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_first_name'),30,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }

            // is name
            if(!ta2ta.validate.name(jQuery('#jform_contact_first_name'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME')); ?>');
        }

    	// last name
    	data.last_name = jQuery('#jform_contact_last_name').val();
        if(ta2ta.validate.hasValue(jQuery('#jform_contact_last_name'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_last_name'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_last_name'),30,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }

            // is name
            if(!ta2ta.validate.name(jQuery('#jform_contact_last_name'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME')); ?>');
        }

    	// title
    	data.title = jQuery('#jform_contact_title').val();
    	if(data.title){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_title'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_title'),255,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }

            // is title
            if(!ta2ta.validate.title(jQuery('#jform_contact_title'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL')); ?>');
            }
        }

    	// email
    	data.email = jQuery('#jform_contact_email').val();
    	if(data.email){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_email'),3,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_email'),150,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            }

            // is email
            if(!ta2ta.validate.email(jQuery('#jform_contact_email'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL')); ?>');
            } 		
    	}
    	
    	// phone
    	data.phone = jQuery('#jform_contact_phone').val();
        if(data.phone){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_contact_phone'),10,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_contact_phone'),15,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // is phone
            if(!ta2ta.validate.phone(jQuery('#jform_contact_phone'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL')); ?>');
            }

            // strip formatting
            data.phone = unformatPhoneNumber(data.phone);       
        }

        // check if any errors occured
        if(errors.length){
            ta2ta.bootstrapHelper.showAlert(alertParent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
        }else{
            // no errors, continue
            // get the current form contents and decode the JSON
            var contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
            
            // update the current record or add a new one to the hidden field
            if(data.id != '0'){
                // editing
                jQuery.each(contacts, function(index,contact){
                    // if the given element matches the current id, update that record              
                    if(contact.id == data.id){
                        contacts[index].state = data.state;
                        contacts[index].first_name = data.first_name;
                        contacts[index].last_name = data.last_name;
                        contacts[index].title = data.title;
                        contacts[index].email = data.email;
                        contacts[index].phone = data.phone;
                    }
                });
            }else{
                // adding
                data.created_by = '<?php echo $this->userName; ?>';
                data.id = 'n' + newID;
                contacts.push(data);
                            
                // incremeent the newID
                newID++;
            }
            
            //save the updated data
            jQuery('#jform_project_contacts').val(JSON.stringify(contacts));
            
            // cause the table to refresh
            reloadContactTable(contacts);
            
            // close the project window
            closeContactModal();
        }
    }
    
    /**
     * Function to save the project information in the modal form
     * 
     * @return null (shows messages to user)
     */
    function saveProject(){
        // variables
        var alertParent = $('#projectFormModal').find('.modal-body');
        var data = new Object;
        var errors = [];
        
        // close any residual alerts and clear validation states
        ta2ta.bootstrapHelper.removeAlert(alertParent);
        ta2ta.bootstrapHelper.hideAllValidationStates();
    	
    	// id
        data.id = jQuery('#jform_projectID').val();
        if(!ta2ta.validate.unsigned(jQuery('#jform_projectID'))){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_ERROR_OCCURED'); ?>');
        }
    	
    	// state
    	var state = jQuery('#jform_project_state').val();
    	if(state == '-1' 
    	|| state == '0' 
    	|| state == '1'){
    		data.state = state;
    	}else{
    		data.state = 1;
    	}

        // title
        data.title = jQuery('#jform_project_title').val();
        if(ta2ta.validate.hasValue(jQuery('#jform_project_title'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_project_title'),2,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_project_title'),255,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE_LBL')); ?>');
            }

            // is title
            if(!ta2ta.validate.title(jQuery('#jform_project_title'),1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_INVALID', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE_LBL')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE_LBL')); ?>');
        }

        // summary
        data.summary = jQuery('#jform_project_summary').val();
        if(ta2ta.validate.hasValue(jQuery('#jform_project_summary'), 1)){
            // min length
            if(!ta2ta.validate.minLength(jQuery('#jform_project_summary'),20,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY_LBL')); ?>');
            }

            // max length
            if(!ta2ta.validate.maxLength(jQuery('#jform_project_summary'),1500,1)){
                errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_MIN_LENGTH', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY_LBL')); ?>');
            }
        }else{
            errors.push('<?php echo JText::sprintf('COM_TA_PROVIDER_DIRECTORY_FORM_REQUIRED', JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY_LBL')); ?>');
        }
    	
    	// grant programs
    	data.grantPrograms = [];
        jQuery('#projectGrants input:checked').each(function(){
    		data.grantPrograms.push(jQuery(this).val());
    	});
    	
    	/*if(data.grantPrograms.length == 0){
            errors.push('<?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_GRANT_PROGRAM_REQUIRED'); ?>');
    	}*/
    	
    	// contacts
    	data.contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
    	
 		// check if any errors occured
        if(errors.length){
            ta2ta.bootstrapHelper.showAlert(alertParent, ta2ta.bootstrapHelper.constructErrorMessage(errors), 'warning', true, true);
        }else{
            // no errors, continue
     		// get the current form contents and decode the JSON
     		var projects = jQuery.parseJSON(jQuery('#jform_projects').val());
     		
     		// update the current record or add a new one to the hidden field
     		if(data.id != '0'){
     			// editing
     			jQuery.each(projects, function(index,project){
     				// if the given element matches the current id, update that record				
     				if(project.id == data.id){
     					projects[index].state = data.state;
     					projects[index].title = data.title;
     					projects[index].summary = data.summary;
                        <?php if($permission_level == 2): ?>
                        projects[index].award_number = data.award_number;
                        projects[index].grantPrograms = data.grantPrograms;
                        <?php endif; ?>
     					projects[index].contacts = data.contacts;
     				}
     			});
     		}else{
     			// adding
     			data.created_by = '<?php echo $this->userName; ?>';
     			data.id = 'n' + newID;
     			projects.push(data);
     			 			
     			// incremeent the newID
     			newID++;
     		}
     		
     		//save the updated data
     		jQuery('#jform_projects').val(JSON.stringify(projects));
        	
        	// cause the table to refresh
        	reloadProjectTable(projects);
        	
        	// close the project window
        	closeProjectModal();
        }
    }
    
    /**
     * Function to change the state of a contact
     * 
     * @param state The state to be set [-1(deleted), 0(unpublished), 1(published)]
     * @param array The IDs of one or more records to be affected
     * 
     * @return null (shows messages to user)
     */
    function setContactState(state, ids){
    	// get the current form contents and decode the JSON
 		var contacts = jQuery.parseJSON(jQuery('#jform_project_contacts').val());
 		
 		// check to see if each project matches those to be changed
 		if(ids instanceof Array){
			// array of different projects
			jQuery.each(contacts, function(index,contact){
				if(jQuery.inArray(contact.id, ids) >= 0){
					contact.state = state;
				}
			});
		}else{
			// single project
			jQuery.each(contacts, function(index,contact){
				if(contact.id == ids){
					contact.state = state;
				}
			});
		}
		
		//save the updated data
 		jQuery('#jform_project_contacts').val(JSON.stringify(contacts));
 		
 		// cause the table to refresh
    	reloadContactTable(contacts);
    }
    
    /**
     * Function to change the state of a project
     * 
     * @param state The state to be set [-1(deleted), 0(unpublished), 1(published)]
     * @param array The IDs of one or more records to be affected
     * 
     * @return null (shows messages to user)
     */
    function setProjectState(state, ids){
    	// get the current form contents and decode the JSON
 		var projects = jQuery.parseJSON(jQuery('#jform_projects').val());
 		
 		// check to see if each project matches those to be changed
 		if(ids instanceof Array){
			// array of different projects
			jQuery.each(projects, function(index,project){
				if(jQuery.inArray(project.id, ids) >= 0){
					project.state = state;
				}
			});
		}else{
			// single project
			jQuery.each(projects, function(index,project){
				if(project.id == ids){
					project.state = state;
				}
			});
		}
		
		//save the updated data
 		jQuery('#jform_projects').val(JSON.stringify(projects));
 		
 		// cause the table to refresh
    	reloadProjectTable(projects);
    }
    
    /**
     * Function to trash one or more contacts
     * 
     * @return null
     */
    function trashContacts(){
    	var ids = getSelectedIds('contactsList', false);
    	if(ids.length){
    		setContactState('-1', ids);
    	}else{
    		// the user did not select anything
            var alertParent = $('#contactGridAlertWrapper');
            ta2ta.bootstrapHelper.removeAlert(alertParent);
            ta2ta.bootstrapHelper.showAlert(alertParent, 'Please select at least one contact to trash by clicking the checkbox to the left of the contact name.');
    	}
    }
    
    /**
     * Function to trash one or more projects
     * 
     * @return null
     */
    function trashProjects(){
    	var ids = getSelectedIds('projectsList', false);
    	if(ids.length){
    		setProjectState('-1', ids);
    	}else{
    		// the user did not select anything
            var alertParent = $('#contactGridAlertWrapper');
            ta2ta.bootstrapHelper.removeAlert(alertParent);
            ta2ta.bootstrapHelper.showAlert(alertParent, 'Please select at least one project to trash by clicking the checkbox to the left of the project name.', false, true);
    	}
    }
    
    /**
     * Function to remove formatting from a phone number
     * 
     * @return string The phone number, digits only
     */
    function unformatPhoneNumber(phone){
    	// if the phone number starts with a one, but is not followed by an 8 or 9, remove the 1
    	if(phone){
    		// remove everything but digits, and return the result
    		phone = phone.replace(/[^\d]/g, '');
    		
    		// remove a leading 1 if it is not for an 800 or 900 series number
	    	if(phone.charAt(0) == '1'
	    	&& (phone.charAt(1) != '8' 
	    	&& phone.charAt(1) != '9')){
	    		phone = phone.substring(1);
	    	}
	 		
	 		// return the result    	
	    	return phone;
	    }
	    return '';
    }
</script>
<div class="ta-directory-settings-edit">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
	<?php endif; ?>
	<p>This page allows you to manage how your organization displays in the TA Provider Directory. Each website user associated with your organization has the ability to modify this information. The information you enter here will be instantly displayed on the website, exactly as you enter it.</p>
	<form autocomplete="off" id="form-settings" action="<?php echo JRoute::_('index.php?option=com_ta_provider_directory&task=settings.save'); ?>" method="post" class="form-validate big-inputs" enctype="multipart/form-data">
		<input type="hidden" id="jform_projects" name="jform[projects]" value='<?php echo json_encode($this->listing->projects); ?>' />
		<input type="hidden" name="option" value="com_ta_provider_directory" />
		<input type="hidden" name="task" value="settings.save" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
	<h3>Your Projects</h3>				
	<form action="/" method="post" enctype="multipart/form-data" name="projectTableForm" id="projectTableForm" class="form-validate">
		<div id="gridAlertWrapper"></div>
        <div class="row">
			<div class="col-xs-12">
				<div class="btn-toolbar">
					<div class="btn-group">
						<button type="button" class="btn btn-sm btn-success" onclick="openProjectModal(0);">
							<span class="icomoon-plus-circle"></span> <?php echo JText::_('TOOLBAR_NEW'); ?>
						</button>
					</div>
					<div id="projectsToolbarEdit" class="btn-group">
						<div class="btn-group">
							<button type="button" class="btn btn-default btn-sm" onclick="editProject();">
								<span class="icomoon-edit"></span> <?php echo JText::_('TOOLBAR_EDIT'); ?>
							</button>
						</div>
						<div class="btn-group" style="display: inline-block; margin-left: 5px;">
							<button type="button" class="btn btn-default btn-sm" onclick="trashProjects();">
								<span class="icomoon-remove"></span> <?php echo JText::_('TOOLBAR_TRASH'); ?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<table id="projectsList" class="table table-striped">
					<thead>
						<tr>
							<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
							<th class="left">Project Name</th>
							<th class="left" style="width: 20%;">Created by</th>
							<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="4"></td>
						</tr>
					</tfoot>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</form>
	<form>
		<div class="row">
			<div class="col-xs-12 form-actions">
				<button type="submit" class="btn btn-primary btn-lg validate" id="formSubmit"><span class="icomoon-disk"></span> Save Changes</button>
				<button type="reset" class="btn btn-default btn-lg" id="formReset"><span class="icomoon-undo"></span> Start Over</button>
			</div>
		</div>	
	</form>
</div>
<div class="modal fade" id="projectFormModal" role="modal" tabindex="-1" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        	<div class="modal-header">
        		<button onclick="closeProjectModal();" type="button" class="close" aria-hidden="true">&times;</button>
        		<h4 class="modal-title">Add Project</h4>
        	</div>
        	<div class="modal-body" style="height: 350px;">
        		<form action="/" method="post" name="projectForm" id="projectForm" class="form-horizontal form-validate" role="form">  
        			<div class="panel-group" id="projectAccordion">
        					<div class="panel panel-default">
        						<div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectInfo">
                                            Project Details
                                        </a>
                                    </h4>
        						</div>
        						<div id="projectInfo" class="panel-collapse collapse in">
        							<div class="panel-body">
        								<fieldset class="project">	
        									<div class="form-group">
        										<label id="jform_project_title-lbl" class="col-sm-3 control-label required" title="" for="jform_project_title"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_TITLE_LBL'); ?></label>
        										<div class="col-sm-9">
                                                    <?php echo $this->form->getInput('project_title'); ?>
                                                </div>
        									</div>
        									<div class="form-group">
        										<label id="jform_project_summary-lbl" class="col-sm-3 control-label required" title="" for="jform_project_summary"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_SUMMARY_LBL'); ?></label>
        										<div class="col-sm-9">
                                                    <?php echo $this->form->getInput('project_summary'); ?>
                                                </div>
        									</div>
                                            <div class="form-group">
                                                <label id="jform_project_award_number-lbl" class="col-sm-3 control-label required" title="" for="jform_project_award_number"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_PROJECT_AWARD_NUMBER_LBL'); ?></label>
                                                <div class="col-sm-9">
                                                    <?php echo $this->form->getInput('project_award_number'); ?>
                                                </div>
                                            </div>
        									<input type="hidden" id="jform_projectID" name="jform[projectID]" value="" />
        								</fieldset>
        							</div>
        						</div>
        					</div>
        					<div class="panel panel-default">
        						<div class="panel-heading">
                                    <h4 class="panel-title">
            							 <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectGrants">
                                            Grant Programs
                                        </a>
                                    </h4>
        						</div>
        						<div id="projectGrants" class="panel-collapse collapse">
        							<div class="panel-body">
        								<fieldset class="programs">
                                            <p><small>Select:  <a class="checkAll">All</a> / <a class="uncheckAll">None</a></small></p>
        									<?php 
        										// print each checkbox in two columns
        										$columns = 2;
        										$gpColumns = array_chunk($this->grantPrograms, ceil(count($this->grantPrograms) / $columns));	
        										foreach($gpColumns as $gpColumn){
        											echo '<div style="float: left; width: ' . (100/$columns) . '%">';
        											foreach($gpColumn as $grantProgram){
        												echo '<div style="margin-bottom: 10px;"><input type="checkbox" name="grantPrograms[]" style="margin-top: -2px" value="' . $grantProgram->id . '"' . ($grantProgram->checked ? ' checked' : '') . '> ' . $grantProgram->name . ' (' . $grantProgram->fund . ')</div>';
        											}
        											echo '</div>';
        										}
        									?>
        									<div class="clr"></div>
        						       </fieldset>
        							</div>
        						</div>
        					</div>
        				<div class="panel panel-default">
        					<div class="panel-heading">
                                <h4 class="panel-title">
        				            <a data-toggle="collapse" data-parent="#projectAccordion" href="#projectContacts">
                                        Contacts
                                    </a>
                                </h4>
        					</div>
        					<div id="projectContacts" class="panel-collapse collapse">
        						<div class="panel-body">
                                    <div id="contactGridAlertWrapper"></div>
        							<div class="row">
        								<div class="col-xs-12">
        									<div id="contacts-toolbar" class="btn-toolbar">
        										<div id="contacts-toolbar-new" class="btn-group">
        											<a class="btn btn-sm btn-success" onclick="openContactModal(0);">
        												<span class="icomoon-plus-circle"></span> <?php echo JText::_('TOOLBAR_NEW_CONTACT'); ?>
        											</a>
        										</div>
        										<div id="contactsToolbarEdit" style="display: inline; margin-left: 5px;">
        											<div id="contacts-toolbar-edit" class="btn-group">
        												<a class="btn btn-default btn-sm" onclick="editContact();">
        													<i class="icomoon-edit "></i> <?php echo JText::_('TOOLBAR_EDIT'); ?>
        												</a>
        											</div>
        											<div class="btn-group divider"></div>
        											<div id="contacts-toolbar-trash" class="btn-group">
        												<a class="btn btn-default btn-sm" onclick="trashContacts();">
        													<i class="icomoon-trash "></i> <?php echo JText::_('TOOLBAR_TRASH'); ?>
        												</a>
        											</div>
        										</div>
        									</div>
        								</div>
        							</div>
        							<div class="row">
        								<div class="col-xs-12">
        									<table id="contactsList" class="table table-striped">
        										<thead>
        											<tr>
        												<th class="hidden-phone" style="width: 1%;"><input type="checkbox" onclick="Joomla.checkAll(this)" title="Check All" value="" name="checkall-toggle" /></th>
        												<th class="left">Name</th>
        												<th class="nowrap center hidden-phone" style="width: 1%;">ID</th>
        											</tr>
        										</thead>
        										<tfoot>
        											<tr>
        												<td colspan="3"></td>
        											</tr>
        										</tfoot>
        										<tbody></tbody>
        									</table>
        								</div>
        							</div>
        						</div>
        					</div>
        				</div>
        			</div>
        			<input type="hidden" id="jform_project_contacts" name="jform[project_contacts]" value="">
        		</form>
        	</div>
        	<div class="modal-footer">
        		<button type="button" onclick="closeProjectModal();" class="btn btn-default"><span class="icomoon-close"></span> Close</button>
        		<button type="button" onclick="saveProject();" class="btn btn-primary" id="projectSaveBtn"><span class="icomoon-checkmark"></span> Save</button>
        	</div>
        </div>
    </div>
</div>
<div class="modal fade" id="contactFormModal" data-backdrop="static">
	<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
		      <button onclick="closeContactModal();" type="button" class="close" aria-hidden="true">&times;</button>
		      <h4 class="modal-title">Add Contact</h4>
            </div>
        	<div class="modal-body" style="height: 350px;">
        		<div class="alert-wrapper"></div>
        		<form action="/" method="post" name="contactForm" id="contactForm" class="form-horizontal form-validate" role="form">
                    <fieldset class="project">	
        				<div class="form-group">
        					<label for="jform_contact_first_name" class="col-sm-3 control-label required"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_FIRST_NAME_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_first_name'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_last_name" class="col-sm-3 control-label required"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_LAST_NAME_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_last_name'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_title" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_TITLE_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_title'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_email" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_EMAIL_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_email'); ?>
                            </div>
        				</div>
        				<div class="form-group">
        					<label for="jform_contact_phone" class="col-sm-3 control-label"><?php echo JText::_('COM_TA_PROVIDER_DIRECTORY_FORM_CONTACT_PHONE_LBL'); ?></label>
        					<div class="col-sm-9">
                                <?php echo $this->form->getInput('contact_phone'); ?>
                            </div>
        				</div>
        				<input type="hidden" id="jform_contactID" name="jform[contactID]" value="" />
        			</fieldset>
        		</form>
        	</div>
        	<div class="modal-footer">
        		<button type="button" onclick="closeContactModal();" class="btn btn-default"><span class="icomoon-close"></span> Close</button>
        		<button type="button" onclick="saveContact();" class="btn btn-primary" id="contactSaveBtn"><span class="icomoon-checkmark"></span> Save</button>
        	</div>
        </div>
    </div>
</div>