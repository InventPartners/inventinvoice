var total_invoice_rows = 0;
var invoice_line_total = 0;
var invoice_tax_total = 0;
var invoice_inctax_total = 0;


$(document).ready(function(){
	checkInvoiceRows();
	//$('.invoice').children('tbody').children('tr').children('textarea').blur(checkInvoiceRows());
	$('textarea').blur(function(){
		$(this).removeClass('ieinputfocus');
		checkInvoiceRows();
	});
	$('textarea').focus(function(){
		$(this).addClass('ieinputfocus');
	});
	$('input').blur(function(){
		$(this).removeClass('ieinputfocus');
		checkInvoiceRows();
	});
	$('input').focus(function(){
		$(this).addClass('ieinputfocus');
	});
});

function checkInvoiceRows(){

	var invoicerowcount = 0;
	invoice_line_total = 0;
	invoice_tax_total = 0;
	invoice_inctax_total = 0;
	total_invoice_rows = $('.invoice').children('tbody').children('tr').length;
	$('.invoice').children('tbody').children('tr').each(function(index){
		invoicerowcount ++;
		var row = $(this);
		
		// Add this row to the total
		addLineToTotal(row)
		
		// set the fieldnames correctly
		$(this).children('td').children('textarea.desc').attr('name' , 'itemkey_' + (invoicerowcount));
		$(this).children('td').children('input.total').attr('name' , 'item_' + (invoicerowcount) + '_total');
		// Do we need to add another row?
		if(checkRowHasData(row)){
			var line_amount_string = $(row).children('td').children('input').val();
			var line_amount = parseFloat(line_amount_string.replace(/[^\-0-9\.]/gi , ''));
			if(!isNaN(line_amount)){
				$(row).children('td').children('input').val(fixedRound(line_amount_string , 2));
			} else {
				$(row).children('td').children('input').val('');
			}
			if(total_invoice_rows == invoicerowcount){
				addInvoiceRow(row);
			}
		} else {
			if(invoicerowcount < total_invoice_rows){
				row.detach();
			}
		}
	});
	
	// Add the new invoice total
	$('#total').html(fixedRound(invoice_line_total , 2));
	$('#taxtotal').html(fixedRound(invoice_tax_total , 2));
	$('#invoicetotalplustax').html(fixedRound(invoice_inctax_total , 2));

}

function checkRowHasData(row){

	if($(row).children('td').children('textarea').val()){
		return true;
	} else if ($(row).children('td').children('input').val()){
		return true;
	} else {
		return false;
	}

}

function addLineToTotal(row){

	var line_amount_string = $(row).children('td').children('input').val();
	var line_amount = parseFloat(line_amount_string.replace(/[^\-0-9\.]/gi , ''));
	var tax_amount = roundNumber((line_amount * vat_rate) , 2);
	if(!isNaN(tax_amount)){
		$(row).children('td.tax').children('span').html(fixedRound(tax_amount , 2));
		invoice_tax_total = roundNumber(invoice_tax_total + tax_amount , 2);
		$(row).children('td.totalplustax').children('span').html(fixedRound(line_amount + tax_amount , 2));
		invoice_inctax_total = roundNumber(invoice_inctax_total + line_amount + tax_amount , 2);
	} else {
		$(row).children('td.tax').children('span').html('');
		$(row).children('td.totalplustax').children('span').html('');
	}
	if(!isNaN(line_amount)){
		$(row).children('td').children('input').val(line_amount);
		invoice_line_total = invoice_line_total + line_amount;
	}

}

function addInvoiceRow(rowafter){

	total_invoice_rows = $('.invoice').children('tbody').children('tr').length

	new_row = $(rowafter).clone();
	$(new_row).children('td').children('textarea').attr('name' , 'itemkey_' + (total_invoice_rows + 1));
	$(new_row).children('td').children('textarea').html('');
	$(new_row).children('td').children('textarea').removeClass('ieinputfocus');
	$(new_row).children('td').children('input').attr('name' , 'item_' + (total_invoice_rows + 1) + '_total');
	$(new_row).children('td').children('input').attr('value' , '');
	$(new_row).children('td').children('input').removeClass('ieinputfocus');
	$(new_row).children('td').children('span').html('');
	$(new_row).insertAfter(rowafter);
	
	$('textarea.desc').blur(checkInvoiceRows);
	$('input.total').blur(checkInvoiceRows);

}

function roundNumber(num, dec) {
	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
	return result;
}

function fixedRound(num2round,digits) {
	numRounded = roundNumber(num2round , digits);
	var num=numRounded.toString();
	var sep=num.indexOf(".");
	var sep=num.indexOf(".");
	if (sep==-1) {num+=".";sep=num.indexOf(".");}
	var dec=num.substring(sep,num.length);
	for (var x=0;x<eval((digits-dec.length)+1);x++)/*>*/{num=num+"0";}
	return num;
}
