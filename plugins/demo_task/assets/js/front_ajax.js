jQuery(document).ready(function($) {
    
    var ajaxurl = php_vars.admin_url;
	var nonce = php_vars.nonce;
    // Make the Ajax request
    $.ajax({
        url: ajaxurl, //Maps to ajax endpoint.
        method: 'POST',
        data: {
            nonce: nonce,
            action: 'get_projects' // action to trigger ajax callback
        },
        success: function(response) {
            if (response.success) {
                console.log(response);
            } else {
                
                console.log('No projects found.');
            }
        },
        error: function() {
            // Handle error if Ajax request fails
            console.log('Ajax request failed.');
        }
        });
    });
