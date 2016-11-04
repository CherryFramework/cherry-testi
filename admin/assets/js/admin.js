( function( $, CherryJsCore ) {
	'use strict';

	CherryJsCore.utilites.namespace( 'cherryTestiAdminScripts' );

	CherryJsCore.cherryTestiAdminScripts = {
		saveHandlerId:       'cherry_testi_save_setting',
		resetHandlerId:      'cherry_testi_reset_setting',
		saveButtonId:        '#cherry-testi-option-form__save',
		resetButtonId:       '#cherry-testi-option-form__reset',
		formId:              '#cherry-testi-option-form',
		saveOptionsInstance:  null,
		resetOptionsInstance: null,

		init: function() {
			this.saveOptionsInstance = new CherryJsCore.CherryAjaxHandler(
				{
					handlerId: this.saveHandlerId,
					successCallback: this.saveSuccessCallback.bind( this )
				}
			);

			this.resetOptionsInstance = new CherryJsCore.CherryAjaxHandler(
				{
					handlerId: this.resetHandlerId,
					successCallback: this.resetSuccessCallback.bind( this )
				}
			);

			this.addEvents();
		},

		addEvents: function() {
			$( 'body' )
				.on( 'click', this.saveButtonId, this.saveOptionsHandler.bind( this ) )
				.on( 'click', this.resetButtonId, this.resetOptionsHandler.bind( this ) );
		},

		saveOptionsHandler: function( event ) {
			this.disableButton( event.target );
			this.saveOptionsInstance.sendFormData( this.formId );
		},

		resetOptionsHandler: function( event ) {
			this.disableButton( event.target );
			this.resetOptionsInstance.send();
		},

		resetSuccessCallback: function() {
			this.enableButton( this.resetButtonId );
		},

		saveSuccessCallback: function() {
			this.enableButton( this.saveButtonId );
		},

		disableButton: function( button ) {
			$( button )
				.attr( 'disabled', 'disabled' );
		},

		enableButton: function( button ) {
			var timer = null;

			$( button )
				.removeAttr( 'disabled' )
				.addClass( 'success' );

			timer = setTimeout(
				function() {
					$( button ).removeClass( 'success' );
					clearTimeout( timer );
				},
				1000
			);
		}
	};

	CherryJsCore.cherryTestiAdminScripts.init();

}( jQuery, window.CherryJsCore ) );
