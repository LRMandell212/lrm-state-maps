<?php
/*
Plugin Name: BBE State Icons
Description: Get SVG icons for a US State
Version: 0.1.0
Author: Lee Mandell
*/

/* TOC
		=FUNCTIONS
		- fetch_state_name_info( $thisState )
		- fetch_state_map( $thisState )
			- call it like this: 
				fetch_state_map( $myState, [$myOptions] );
				Where:
				- $myState is the two-letter State abbreviations
				- $myOptions is an optional associative array with any of the following
					'img-filetype' => 'svg',
					'img-class' => 'bbe-state-icon',
					'img-id' => 'bbe-' . $myState,
					'container' => 'div',
					'container-class' => 'bbe-state-frame',
					'container-id' => 'bbe-' . $myState . '-frame;,
					]);
*/

/* $$$ TODO
	* Alternate error handling strategy - return a broken image (this will show the developer the error!!)
		- But if something happens to the live install will be broken.
	-* Is there a way to display the icon inline? Yes - Done!
	* Do we want need to create a basic css file that is loaded with the plugin?
	-* Create a parameter that is an options array.
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
		// Longer than 2 letters. Reverse the key and value so we can
		// try to see if the full state name was entered/passed instead.
		$arrayFlipped = array_flip( $arStates );
		$myStateInfo = $arrayFlipped[$thisState];
	}

	return $myStateInfo;
}

/*
	Given a two letter US state abbreviation return the file name of an icon of the state.
	If the file doesn't exist, return an error message or default image (TBD).
	If for some reason the function is passed a string that is longer than two letters, it
	will check if the string is a valid state name and recover.
*/		
function fetch_state_map( $thisState, $theseOptions=[] ) {

	$myState = $thisState;
	$myImgFileType = 'svg'; //default value
	
	if( strlen( $thisState ) < 2 ) {
		// $thisState is empty or only 1 character. Write error to log file?
		return;	
	}

	if (! empty($theseOptions) ) {
		if( array_key_exists( 'img-filetype', $theseOptions ) ) { $myImgFileType = $theseOptions['img-filetype']; }
		if( array_key_exists( 'img-class', $theseOptions ) ) { $myImgClass = $theseOptions['img-class']; }
		if( array_key_exists( 'img-id', $theseOptions ) ) { $myImgID = $theseOptions['img-id']; }
		if( array_key_exists( 'img-alt-prefix', $theseOptions ) ) { $myImgAltPrefix = $theseOptions['img-alt-prefix']; }
		if( array_key_exists( 'container', $theseOptions ) ) { $myContainer = $theseOptions['container']; }
		if( array_key_exists( 'container-class', $theseOptions ) ) { $myContainerClass = $theseOptions['container-class']; }
		if( array_key_exists( 'container-id', $theseOptions ) ) { $myContainerID = $theseOptions['container-id']; }
	}

	if( strlen( $myState ) > 2 ) {
		/*	See if a full state name was entered/passed instead of the two letter abbreviation.
				Make sure the user info matches the data by upper casing the first letter of each word.
				If a valid state name was entered, return the proper 2 letter abbreviation so we can 
				fall through and continue normally.
		*/
		$myState = fetch_state_name_info( ucwords( $myState ) );
	}

	$myDirPath = plugin_dir_path(__FILE__) . 'assets\\';
	$myURLPath = plugin_dir_url( __FILE__ ) . 'assets/';
	$myImgFile = strtolower($myState) . '.' . $myImgFileType;

	if( file_exists( $myDirPath . $myImgFile ) ) {
		// URL as opposed to folder
		$myHTML = '';
		$myImg = 	$myURLPath . $myImgFile;

		$myAlt = ' alt="' . $myImgAltPrefix . fetch_state_name_info( $myState ) . '."';
		
		// Setup container and image classes and ids.
		if( ! empty( $myContainerID ) )	{ 
			$myContainerID = ' id="' . $myContainerID . '"';
		}

		if( ! empty( $myContainerClass ) )	{ 
			$myContainerClass = ' class="' . $myContainerClass . '"';
		}

		if( ! empty( $myImgID ) )	{ 
			$myImgID = ' id="' . $myImgID . '"';
		}

		if( ! empty( $myImgClass ) )	{ 
			$myImgClass = ' class="' . $myImgClass . '"';
		}

		// Generate the HTML
		if( ! empty( $myContainer ) ) {
			$myHTML .= '<' . $myContainer . $myContainerID . $myContainerClass . '>';
		}

		if( $myImgFileType === 'svg') {
			// Display svg files inline.
			$myHTML .= file_get_contents( $myDirPath . $myImgFile );
		} else {
			$myHTML .= '<img src="' . $myImg . '"' . $myImgID . $myImgClass . $myAlt . '>';
		}
		
		if( ! empty( $myContainer ) ) {
			$myHTML .= '</' . $myContainer . '>';
		}

	} else {
		// return default image or error.
		$myHTML = $myImgFile . '<br>file does not exist';
	}
	
	return $myHTML;	
}