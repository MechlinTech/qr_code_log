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
	
	
	document.querySelector(".owned_qr_code ul").innerHTML ='';
	document.querySelector(".downline_qr_code ul").innerHTML='';
	document.querySelector(".upline_qr_code ul").innerHTML='';

	var count =0;
	(data.data.owner).forEach(element => {
		const para = document.createElement("LI");
		para.innerHTML=`<div data-user_id="${element.user_id}"  data-id="${element.id}" data-user_owner="${element.user_owner}">
		<input type="checkbox" data-user_id="${element.user_id}"  data-id="${element.id}" data-user_owner="${element.user_owner}"/>
		<label>${element.qr_code}</label>
		</div>`;
			const elements = document.querySelector(".owned_qr_code ul");
				elements.appendChild(para);
		});

		count =0;
		(data.data.downline).forEach(element => {
			const para = document.createElement("LI");
		
			para.innerHTML=`<div data-user_id="${element.user_id}" data-id="${element.id}" data-user_owner="${element.user_owner}">
			 <input type="checkbox" data-user_id="${element.user_id}"  data-id="${element.id}" data-user_owner="${element.user_owner}"/>
			<label>${element.qr_code}</label></div>`;
			const elements = document.querySelector(".downline_qr_code ul");
				elements.appendChild(para);});

			
			const elements = document.querySelector(".upline_qr_code ul");
			const para = document.createElement("LI");
			para.innerHTML=`<div data-user_id="${data.data.upline.user_id}" data-mode="upline" data-id="${data.data.upline.id}"  data-user_owner="${data.data.upline.user_owner}"></div>`;
			elements.appendChild(para);
			 create_user_list(document.querySelector(".upline_qr_code ul div"),data.data.upline.user_owner);

			add_event();
			add_event_down();
			add_event_owner();
			jQuery('.code_display').attr('data-user_id', user_id);

		 }
	}); 
}

function add_event_down(){
	jQuery('.downline_qr_code ul li input').on('click',function(e){
	
	e.stopPropagation();
	console.log(e.target.checked);
	var list = document.querySelectorAll('.downline_qr_code ul li input');
	var count =0;
	list.forEach(element => {
		if(element.checked){
			count++;
		}
	});
	if(count===0){
		document.querySelector('button.button.button-danger.add_down_data_qr_code').disabled = true;
	}else{
		document.querySelector('button.button.button-danger.add_down_data_qr_code').disabled =false;
	}
	
	
	});
	jQuery('.owned_qr_code ul li input').on('click',function(e){
	
		e.stopPropagation();
		console.log(e.target.checked);
		var list = document.querySelectorAll('.owned_qr_code ul li input');
		var count =0;
		list.forEach(element => {
			if(element.checked){
				count++;
			}
		});
		if(count===0){
			document.querySelector('button.button.button-danger.add_owner_data_qr_code').disabled = true;
		}else{
			document.querySelector('button.button.button-danger.add_owner_data_qr_code').disabled =false;
		}
		
		
		});

	}
jQuery("button.button.button-danger.add_down_data_qr_code").on('click',function(e){
	
	e.preventDefault();
	var list = document.querySelectorAll('.downline_qr_code ul li input');
		var count =0;
		var data_send =[];
		list.forEach(element => {
			if(element.checked){
				data_send[count++] = element.dataset;
				
			}
		});
		var data_action={
			data_user:data_send,
			action:'update_data_line',
			flow_line:'down'
		};
		delete_owned_line(data_action);
});

jQuery("button.button.button-danger.add_owner_data_qr_code").on('click',function(e){
	e.preventDefault();
	console.log(e.target);
	var list = document.querySelectorAll('.owned_qr_code ul li input');
	var count =0;
	var data_send =[];
	list.forEach(element => {
		if(element.checked){
			data_send[count++] = element.dataset;
			
			
		}
	});
	var data_action={
		data_user:data_send,
		action:'update_data_line',
		flow_line:'owner'
	};
	console.log(data_action);

	delete_owned_line(data_action);


});

function delete_owned_line(qr_data){
	jQuery.ajax({
		type: 'post',
url: ajax_object.ajax_url,
data: qr_data,      
success: function (data) {
console.log(data);
}
})

}


function delete_down_line(qr_data){
	jQuery.ajax({
		type: 'post',
url: ajax_object.ajax_url,
data: qr_data,      
success: function (data) {
console.log(data);
}
})

}




function add_event_owner(){
	jQuery('button.add_button').on('click',function(e){
	e.preventDefault();
	
	});
}

const create_user_option = function (append){

	var parent = append;
	var select_option = ajax_object.user_list;
	var option = document.createElement("option");
	option.value = 'Null';
	option.text = 'Select User';
	parent.appendChild(option);
//Create and append the options
for (var i = 0; i < select_option.length; i++) {
    var option = document.createElement("option");
    option.value = select_option[i].ID;
	option.text = select_option[i].user_login;
    parent.appendChild(option);
}

}

const add_qr_option = function (append){

	var parent = append;
	var select_option = ajax_object.qr_list;
	var option = document.createElement("option");
	option.value = 'Null';
	option.text = 'Select Qr Code';
	parent.appendChild(option);
//Create and append the options
for (var i = 0; i < select_option.length; i++) {
    var option = document.createElement("option");
    option.value = select_option[i].id;
	option.text = select_option[i].qr_code;
    parent.appendChild(option);
}

}




const create_user_list = function (append,selectedUser){

	var Parent = append;
	var select_option = ajax_object.user_list;

	var selectList = document.createElement("select");
selectList.className = "select_user";
Parent.appendChild(selectList);

//Create and append the options

var option = document.createElement("option");
option.value = 'Null';
option.text = 'Delete';
selectList.appendChild(option);


for (var i = 0; i < select_option.length; i++) {
    var option = document.createElement("option");
    option.value = select_option[i].ID;
	if(selectedUser==select_option[i].ID){
		option.selected = true;
	}
	
    option.text = select_option[i].user_login;
    selectList.appendChild(option);
}
var buttonList = document.createElement("button");
buttonList.className = "add_button";
buttonList.innerHTML='Update';
Parent.appendChild(buttonList);
}



function add_event(){
jQuery('button.add_button').on('click',function(e){
e.preventDefault();
var selectedData= e.target.parentElement.querySelector('select.select_user');
var data_qr = e.target.parentElement.dataset;
data_qr.action="update_user_line";
data_qr.selectesUser=selectedData.value;
console.log(data_qr);
get_update_line(data_qr);
});
}


function get_update_line(qr_data){
	jQuery.ajax({
		type: 'post',
url: ajax_object.ajax_url,
data: qr_data,      
success: function (data) {
console.log(data);
}
})

}


    //var e = evt || window.event;
	
	jQuery(".input_downline_qr_code input,.input_owned_qr_code input").on('keypress',function(evt){
		var charCode = ((evt.which) ? evt.which : evt.keyCode);
		if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57)){
			return false;
		}else{
			return true;

		}
			
			
	})

	jQuery("button.button.button-primary.add_owner_data_qr_code").on('click',function(e){
		e.preventDefault();
		var get_data_qr_code =document.querySelector('.input_owned_qr_code input').value;
		var get_current_user =document.querySelector('.code_display').dataset;
		console.log(get_data_qr_code,get_current_user);
		var data ={
			action:'add_qr_code',
			user_id:get_current_user,
			qr_code:get_data_qr_code
		};
		add_owned_line(data);
	
	
	});
	
	jQuery("button.button.button-primary.add_down_data_qr_code").on('click',function(e){
		e.preventDefault();
		var get_data_qr_code =document.querySelector('.input_down_qr_code input').value;
		var get_current_user =document.querySelector('.code_display').dataset;
		console.log(get_data_qr_code,get_current_user);
		var data ={
			action:'add_qr_code',
			user_id:get_current_user,
			qr_code:get_data_qr_code
		};
		add_owned_line(data);
	
	});
	
	
	function add_owned_line(qr_data){
		jQuery.ajax({
			type: 'post',
	url: ajax_object.ajax_url,
	data: qr_data,      
	success: function (data) {
	console.log(data);
	}
	});
}


});
