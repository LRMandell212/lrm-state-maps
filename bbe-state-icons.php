<?php
/*
Plugin Name: BBE State Icons
Description: Get SVG icons for a US State
Version: 0.0.2
Author: Lee Mandell
*/

/* TOC
		=FUNCTIONS
		- fetch_state_name_info( $thisState )
		- fetch_state_icon( $thisState )
			- call it in the template like this: 
				echo fetch_state_icon( $myState );
				where $myState = get_post_meta( $post->ID, 'state', true );
*/

/* $$$ TODO
	* Alternate error handling strategy - return a broken image (this will show the developer the error!!)
		- But if something happens to the live install will be broken.
	* Is there a way to display the icon inline?
	* Do we want need to create a basic css file that is loaded with the plugin?
	* Create a parameter that is an options array.
		- We could then pass in the:
			- File type (svg, png, jpg, gif, etc.).
			- The img class or ID.
			- The wrapper element (none, div, li, figure, etc )
			- The default image if the image is not found.
	* AE is ambiguous referring to 
			-	Armed Forces Africa
			-	Armed Forces Canada
			-	Armed Forces Europe
			-	Armed Forces Middle East
		Need to code the exception for this in the alt text.	
*/

function fetch_state_name_info ( $thisState ) {
	
	$myStateInfo = '0'; // Default value if we can't return a state name

  $arStates = array(
		'AL' => 'Alabama',
		'AK' => 'Alaska',
		'AZ' => 'Arizona',
		'AR' => 'Arkansas',
		'CA' => 'California',
		'CO' => 'Colorado',
		'CT' => 'Connecticut',
		'DE' => 'Delaware',
		'FL' => 'Florida',
		'GA' => 'Georgia',
		'HI' => 'Hawaii',
		'ID' => 'Idaho',
		'IL' => 'Illinois',
		'IN' => 'Indiana',
		'IA' => 'Iowa',
		'KS' => 'Kansas',
		'KY' => 'Kentucky',
		'LA' => 'Louisiana',
		'ME' => 'Maine',
		'MD' => 'Maryland',
		'MA' => 'Massachusetts',
		'MI' => 'Michigan',
		'MN' => 'Minnesota',
		'MS' => 'Mississippi',
		'MO' => 'Missouri',
		'MT' => 'Montana',
		'NE' => 'Nebraska',
		'NV' => 'Nevada',
		'NH' => 'New Hampshire',
		'NJ' => 'New Jersey',
		'NM' => 'New Mexico',
		'NY' => 'New York',
		'NC' => 'North Carolina',
		'ND' => 'North Dakota',
		'OH' => 'Ohio',
		'OK' => 'Oklahoma',
		'OR' => 'Oregon',
		'PA' => 'Pennsylvania',
		'RI' => 'Rhode Island',
		'SC' => 'South Carolina',
		'SD' => 'South Dakota',
		'TN' => 'Tennessee',
		'TX' => 'Texas',
		'UT' => 'Utah',
		'VT' => 'Vermont',
		'VA' => 'Virginia',
		'WA' => 'Washington',
		'WV' => 'West Virginia',
		'WI' => 'Wisconsin',
		'WY' => 'Wyoming',
		'DC' => 'District of Columbia',
		'MH' => 'Marshall Islands',
		'AA' => 'Armed Forces Americas',
		'AE' => 'Armed Forces Africa',
		'AE' => 'Armed Forces Canada',
		'AE' => 'Armed Forces Europe',
		'AE' => 'Armed Forces Middle East',
		'AP' => 'Armed Forces Pacific',
	);
	
	// 2 letter abbreviation,
	if( strlen( $thisState ) === 2 ) {
		$myStateInfo = $arStates[$thisState];
	} else {
		// Longer than 2 letters reverser the key with the value so we can
		// try to see if the full state name was entered/passed instead.
		$arrayFlipped = array_flip( $arStates );
		$myStateInfo = $arrayFlipped[$thisState];
	}

	return $myStateInfo;
}

/*
	Given a two letter US state abbreviation return the file name of an icon of the state.
	If the file doesn't exist, return an error message or default image (TBF).
	If for some reason the function is passed a string that is longer than two letters, it
	will check if the string is a valid state name and recover.
*/		

function fetch_state_icon( $thisState, $thisFileType = 'svg' ) {

	$myState = $thisState;
	
	if( strlen( $thisState ) < 2 ) {
		// write error to log file?
		return;	
	}

	if( strlen( $myState ) > 2 ) {
		/*	See if the full state name was entered/passed instead.
				Make sure the user info matches the data by upper casing the first letter of each word.
				Returns the proper 2 letter abbreviation so we can fall through and continue normally.
		*/
		$myState = fetch_state_name_info( ucwords( $myState ) );
	}

	$myPath =  plugin_dir_path(__FILE__) . 'assets\\';
	$myImgFile = strtolower($myState) . '.svg';

	if( file_exists( $myPath . $myImgFile ) ) {
		// URL as opposed to folder
		$myImage = 	plugin_dir_url( __FILE__ ) . 'assets/' . $myImgFile;
		$myAlt = fetch_state_name_info( $myState );
		$myHTML  = '<div class="bbe-state-icon">';
		$myHTML .= '<img src="' .	$myImage . '"' .
								' alt="Project location: ' . 	$myAlt . '."' . 
								'>'; 
		$myHTML .= '</div>';

	} else {
		// return default image or error.
		$myHTML = $myImgFile . '<br>file does not exist';
	}
	
	return $myHTML;	
}