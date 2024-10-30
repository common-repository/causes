////

//
jQuery(document).ready(function() {	
    jQuery( "#causes_start_date" ).datepicker({
     dateFormat : 'MM dd, yy',
	 defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        jQuery( "#causes_end_date" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    jQuery( "#causes_end_date" ).datepicker({
      dateFormat : 'MM dd, yy',
	  defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 1,
      onClose: function( selectedDate ) {
        jQuery( "#causes_start_date" ).datepicker( "option", "maxDate", selectedDate );
      }
    }); 	
	});