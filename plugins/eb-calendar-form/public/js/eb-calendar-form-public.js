(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 * 
	 *  ;
	 */
	 var disabledDays;
	 var dmy;	
	 
	/* utility functions */	
	function unavailable(date) {
		dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
		if ($.inArray(dmy, disabledDays) < 0) {
		  return [true,"","Book Now"];
		} else {
		  return [false,"","Booked Out"];
		}
	  }
	 jQuery(document).ready(function($) {
		$("#datepicker").datepicker({
		beforeShowDay: unavailable
		});
	 });	

	  



})( jQuery);

