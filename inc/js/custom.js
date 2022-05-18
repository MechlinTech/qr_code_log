jQuery(document).ready(function(){
 jQuery('.get_user_button').on('click',function(e){
	 e.preventDefault();
	 
	toptions(e.target.dataset.user);

 });



 jQuery('button.modal-close.modal-toggle').on('click',function(e){
	e.preventDefault();
	jQuery('.modal').toggleClass('is-visible');
  

});

	
function toptions(user_id){
	jQuery.ajax({
		type: 'post',
url: ajax_object.ajax_url,
data: {
action: 'get_data',
user_id:user_id
		},      
success: function (data) {
	console.log(data);
	jQuery('.modal').toggleClass('is-visible');
		 ;}
	}); 
}
});
