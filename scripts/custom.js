$(document).ready(function(){           
	$("#invoice_list tbody tr td").click(function(){
	  window.location=$(this).find("a").attr("href"); return false;
	});
        $("#invoice_list tbody tr.overdue").hover(
        function(){
          $(this).css("cursor", "pointer")
          $(this).css("background", "#f9f9c0")
        },
        function(){
          $(this).css("background", "#fcd5d5")
        });
        
         $("#invoice_list tbody tr.outstanding").hover(
        function(){
          $(this).css("cursor", "pointer")
          $(this).css("background", "#f9f9c0")
        },
        function(){
          $(this).css("background", "#cbeff9")
        });
        
        $("#invoice_list tbody tr.paid").hover(
        function(){
          $(this).css("cursor", "pointer")
          $(this).css("background", "#f9f9c0")
        },
        function(){
          $(this).css("background", "#edecec")
        });
        
        $("#invoice_list tbody tr").hover(
        function(){
        $(this).find("a").css("color", "#666")
        },
        function(){
         $(this).find("a").css("color", "#666")
        });
        
        $('.dropdown-toggle').dropdown()
       
    });


function confirmDelete(element){

	if(confirm("Confirm Delete?")){
		element.href = element.href + '?confirm=yes';
		return true;
	} else {
		return false;
	}

}