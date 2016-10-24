var qsTimer;

function quickContactSearch(){
	value = $('#invoice_company').attr('value');
	clearTimeout(qsTimer);
	if(value.length > 2){
		var qsTimer = setTimeout(getQuickContactSearchResults , 300);
	}
}

function getQuickContactSearchResults(){
	value = $('#invoice_company').attr('value');
	var currentresults = $('#contactsearchresults').html();
	$('#contactsearchresults').stop(true, true).load(
		'/ajax/admin/contactsearch/' , 
		'q=' + value ,
		function(responseText, textStatus){
			if(responseText == ''){
				$('#contactsearchresults').css('display' , 'none');
			} else {
				$('#contactsearchresults').css('display' , 'block');
			}
		}
	);
}

function getContactDetails(id){
	$.ajax({
		url:'/ajax/admin/getcontactdetails/' + id ,
		type:'json' ,
		success:function(data, textStatus, jqXHR){
			$('#contact_id').attr('value' , id);
			if(data['contact_company']){
				$('#invoice_company').attr('value' , data['contact_company']);
			} else {
				$('#invoice_company').attr('value' , data['contact_firstname'] + ' ' + data['contact_lastname']);
			}
			$('#invoice_address1').attr('value' , data['contact_address1']);
			$('#invoice_address2').attr('value' , data['contact_address2']);
			$('#invoice_address3').attr('value' , data['contact_address3']);
			$('#invoice_address4').attr('value' , data['contact_address4']);
			$('#invoice_address5').attr('value' , data['contact_address5']);
			$('#invoice_postcode').attr('value' , data['contact_postcode']);
			$('#invoice_country').attr('value' , data['country_code']);
			$('#invoice_vatnumber').attr('value' , data['contact_vatnumber']);
			$('#contactsearchresults').css('display' , 'none');
		} ,
		error: function(){
			$('#contact_id').attr('value' , id);
			$('#contactsearchresults').css('display' , 'none');
		}
	});
	//$('#contactsearchresults').css('display' , 'none');
}

function newContact(id){
	$('#contact_id').attr('value' , '');
	$('#invoice_address1').attr('value' , '');
	$('#invoice_address2').attr('value' , '');
	$('#invoice_address3').attr('value' , '');
	$('#invoice_address4').attr('value' , '');
	$('#invoice_address5').attr('value' , '');
	$('#invoice_postcode').attr('value' , '');
	$('#invoice_country').attr('value' , 'GB');
	$('#invoice_vatnumber').attr('value' , '');
	$('#contactsearchresults').css('display' , 'none');
}