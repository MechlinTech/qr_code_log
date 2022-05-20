jQuery(document).ready(function(){
 jQuery('.get_user_button').on('click',function(e){
	 e.preventDefault();
	 
	toptions(e.target.dataset.user);

 });

 jQuery('a.button.button-primary.get_data_qr_code').on('click',function(e){
	e.preventDefault();
	console.log(e.target.dataset);
	var data_user_id =((e.target.parentElement).parentElement).querySelector('.user_id_qr').value;
	var data_user_owner =((e.target.parentElement).parentElement).querySelector('.user_owner_qr').value;
	var data ={
		action: 'update_qr_code_generation',
		user_owner:data_user_owner,
		user_id:data_user_id,
		id:e.target.dataset.id
	};
	
		jQuery.ajax({
			type: 'post',
	url: ajax_object.ajax_url,
	data: data,      
	success: function (resp) {
		console.log(resp);
		
			 }
		}); 
	

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
