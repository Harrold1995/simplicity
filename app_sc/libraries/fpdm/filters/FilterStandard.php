<?php

	//
	//  FPDM - Filter Standard
	//  NOTE: dummy filter for unfiltered streams!
	//

	if(isset($GLOBALS['FPDM_FILTERS'])) array_push($GLOBALS['FPDM_FILTERS'],"Standard");

	class FilterStandard {

		function decode($data) {
			return $data;
		}

		function encode($data) {
			return $data;
		}
	}
?>