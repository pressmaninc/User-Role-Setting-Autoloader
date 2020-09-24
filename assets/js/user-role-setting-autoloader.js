const USER_SETTING_AUTOLOADER = {

	/**
	 * Download json file.
	 *
	 */
	download: () => {
		jQuery( '<form/>', { action: USA_CONFIG.api, method: 'post' } )
			.append( jQuery( '<input/>', { type: 'hidden', name: "action", value: USA_CONFIG.action } ) )
			.append( jQuery( '<input/>', { type: 'hidden', name: "nonce", value: USA_CONFIG.nonce } ) )
			.appendTo( document.body )
			.submit()
			.remove();
	}
};


document.addEventListener( 'DOMContentLoaded', () => {
	const $exportBtn = jQuery( '#usa-export-btn' );

	$exportBtn.on( 'click', async() => {
		$exportBtn.prop( 'disabled', true );
		USER_SETTING_AUTOLOADER.download();
		$exportBtn.prop( 'disabled', false );
	});
});
