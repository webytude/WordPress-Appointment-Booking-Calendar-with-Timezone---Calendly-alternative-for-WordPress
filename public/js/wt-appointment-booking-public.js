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
	 */

	jQuery('.calendar').pignoseCalendar({
		select: function(date, context) {
	        // console.log(date[0]['_i']);
	        var $this = context.element;

	        $this.closest('.wt-appointment').find(".appointments-form-blk input[name='wt-date']").val( date[0]['_i'] );
	    }
	});

	jQuery('.wt-appointment').on('click', '.time a', function(event) {
		event.preventDefault();
		
		var $this = jQuery(this);
		$this.closest('ul').find('li').removeClass('active');
		$this.closest('li').addClass('active');
	    
	});

	jQuery('.wt-appointment').on('click', '.time-confirm a', function(event) {
		event.preventDefault();

		var $this = jQuery(this);
		$this.closest('.wt-appointment').find(".appointments-form-blk input[name='wt-time']").val( $this.data('time') );
		$this.closest('.wt-appointment').find('.wt-calendar').hide();
		$this.closest('.wt-appointment').find('.appointments-form-blk').show();
		$this.closest('ul').find('li').removeClass('active');
	});
	jQuery('.wt-appointment').on('click', '.appointments-form-blk .back', function(event) {
		event.preventDefault();

		var $this = jQuery(this);
		$this.closest('.wt-appointment').find('.appointments-form-blk').hide();
		$this.closest('.wt-appointment').find('.wt-calendar').show();
	});
	// ==============================================================================
    // Submit appointments form
    // ==============================================================================    
    jQuery( ".appointments-form" ).submit(function( event ) {
        event.preventDefault();
        var fieldName = '';
    	var popError = false;

        var $thisForm = jQuery(this);
        // var form = $thisForm.serialize();
        $thisForm.find('input.required, textarea.required').each(function(i, field) {
	        var $this = jQuery(this);
	        if($this.is(':disabled') == false){
	        if($this.val() == ''){

	            var msg = 'Please fill in the required field';
	            var attr = $this.attr('data-error');
	            if (typeof attr !== typeof undefined && attr !== false) {
	                msg = $this.attr('data-error');
	            }
	            $this.addClass('error').after('<span class="error-message">'+msg+'</span>');
	            popError = true;
	        }
	        }
	    });
	    if(popError == true){
	        return false;
	    }
        var formdata = (window.FormData) ? new FormData($thisForm[0]) : null;

        jQuery.ajax({
            type: 'POST',
            url: wt_object.ajaxurl,
            data: formdata,
            dataType: 'json',
            contentType: false,
            processData: false,
            success:function(data){
                
                $thisForm.find('.ajax-message').html(data.message);
                                                        
                setTimeout(function(){
                    $thisForm.find('.ajax-message').html('');
                    
                	location.reload();
                }, 3000);
            },
            error:function(){
                console.log("Error: There is some issue please try again.")
            },
            beforeSend:function(){
                jQuery('.siteloader').show();
            },
            complete: function () {
                jQuery('.siteloader').hide();
            },
        });
    }); 


})( jQuery );
