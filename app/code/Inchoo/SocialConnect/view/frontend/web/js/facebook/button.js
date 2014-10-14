window.fbAsyncInit = function() {
    FB.init({
        appId      : inchoo_socialconnect.appid,
        xfbml      : true,
        version    : 'v2.1'
    });
};

function inchoo_socialconnect_signin_callback(response) {
    if (response.authResponse) {
        // User accepted

        // Successful login, do ajax request
        jQuery.ajax({
            type: 'POST',
            url:  inchoo_socialconnect.ajaxurl,
            data: {
                // CSRF protection
                state:  inchoo_socialconnect.state,

                // Short lived token
                access_token: response.authResponse.accessToken,

                // When this token expires
                expires_in: response.authResponse.expiresIn
            },
            dataType: 'json',
            success: function(data) {
                window.location.replace(data.redirect);
            }
        });
    } else {
        // User canceled - This currently doesn't work due to Facebook API issue
        console.log('User cancelled login or did not fully authorize.');
    }
}