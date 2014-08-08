function validate(email) {
   var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   return reg.test(email);
}
$("document").ready(function(){
    var str = $("#msg").val();
    var left = 500 - str.length;
    $("#charLeft").html(left);

    $("#msg").keyup(function(){
        var str = $("#msg").val();
        var left = 500 - str.length;
        $("#charLeft").html(left);
    })

    $("#add_mail").click(function(){
        if($("#email").val() == ""||!validate($("#email").val()))
            apprise('{-$friends_invite_invalid_address}');
        else{
            var mail = '<div class="receiver_mail"><span style="float: left;">'+$("#email").val()+'</span><img src="style/{-$STYLE}/img/del_mail.png" style="float: right;" id="del_mail"/><div class="clear"></div><input type="hidden" name="to[]" value="'+$("#email").val()+'" id="input_'+$("#email").val()+'"/></div>';
            $("#to").append(mail);
            $("#email").val("").focus();
        }
    })

    $("#del_mail").live('click', function(){
        $(this).parent().remove();
    })
})