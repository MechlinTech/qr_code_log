
	// Initialize Variables
	var closePopup = document.getElementById("popupclose");
	var overlay = document.getElementById("overlay");
	var popup = document.getElementById("popup");
	var button = document.getElementById("button");
	// Close Popup Event
	closePopup.onclick = function() {
	  overlay.style.display = 'none';
	  popup.style.display = 'none';
	};
	// Show Overlay and Popup
	button.onclick = function() {
	  overlay.style.display = 'block';
	  popup.style.display = 'block';
	}


function toptions(){
            jQuery.ajax({
                type: 'post',
        url: my_ajax.ajax_url,
        data: {
        action: 'get_data'
                },      
        success: function (data) {
        jQuery("#myModal").show()
                 ;}
            }); 
        }