$(document).ready(function(){

  $('#signup').click(function(){ 
  var email_id = $("#email_id").val();
  var mob_no = $("#mob_no").val();
 
  $.ajax({ 
    type: 'POST', 
    url: '../ajaxdata.php', 
    data: {email_id:email_id,mob_no:mob_no},  
    success: function (data) { 
    $('#login').submit();
          
    }
});
  

});
});
