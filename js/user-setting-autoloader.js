const USER_SETTING_AUTOLOADER = {
  /**
   * Export all users roles.
   *
   * @returns {Promise<*>}
   */
  exportAllRolesCapabilities: async() => {
    const formData = new FormData();
    formData.append( 'action', USA_CONFIG.action );
    formData.append( 'nonce', USA_CONFIG.nonce );

    return await axios.post( USA_CONFIG.api, formData )
      .catch( ( err ) => err.response );
  },

  /**
   * Download json file.
   *
   * @param response
   */
  download: ( response ) => {
    const disposition = response.headers['content-disposition'] || '';
    const fileName = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1] || ( new Date() ).getTime() + 'user-setting.json';
    const link = document.createElement( 'a' );
    const blob = new Blob( [ response.data.data ], { type: 'application/json' } );

    link.href = window.URL.createObjectURL( blob );
    link.download = decodeURIComponent( fileName ).replace( /"/g, "" );

    document.body.appendChild( link );
    link.click();
    document.body.removeChild( link );
  }
};


document.addEventListener( 'DOMContentLoaded', () => {
  const $exportBtn = jQuery( '#usa-export-btn' );

  $exportBtn.on( 'click', async () => {
    $exportBtn.prop('disabled', true);
    const response = await USER_SETTING_AUTOLOADER.exportAllRolesCapabilities();

    if ( response.status === 200 ) {
      USER_SETTING_AUTOLOADER.download( response );
    } else {
      alert(response.data.data);
    }
    $exportBtn.prop('disabled', false);
  });
});
