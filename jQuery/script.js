// Functions
function IsEmail(email) {
    const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    }
    else {
        return true;
    }}

function passchk(password){
    if (password.length < 8){
        $("#output").html("Minimum Password Length should be 8 Characters.")
        $("#output").css("color","red")
        return false;
    }
    if ((password.match(/[a-z]/)) == null){
        $("#output").html("Password must contain atleast 1 Lower Case.")
        $("#output").css("color","red")
        return false;
    }
    if ((password.match(/[A-Z]/)) == null){
        $("#output").html("Password must contain atleast 1 Upper Case.")
        $("#output").css("color","red")
        return false;
    }
    if ((password.match(/[0-9]/)) == null){
        $("#output").html("Password must contain atleast 1 Numeric.")
        $("#output").css("color","red")
        return false;
    }
    if ((password.match(/[@#.!$%&*()\-_=+|,;:'"]/)) == null){
        $("#output").html("Password must contain 1 Special Character.")
        $("#output").css("color","red")
        return false;
    }
    return true;
}


$("#butt").click(function(){
    var flag = true
    //Empty Check
    if ($("#email").val()=="" || $("#phone").val()=="" || $("#pass").val()=="" || $("#conpass").val()==""){
        $("#output").html("Input Fields can not be Empty!!")
        $("#output").css("color","red")
        flag=false
    }

    // Email Check
    if (!IsEmail($("#email").val())){
        $("#output").html("Please Enter Valid Email Address!!")
        $("#output").css("color","red")
        flag=false
    }

    // Phone Check
    if (($("#phone").val()).length != 10){
        $("#output").html("Please Enter Valid Indian Mobile Number!!")
        $("#output").css("color","red")
        flag=false
    }

    // Pass Check
    var value = $('#pass').val();
    if (!passchk(value)){
        flag=false
    }

    // Pass Match
    if($("#pass").val()!=$("#conpass").val()){
        $("#output").html("Password and Confirm Password not match")
        $("#output").css("color","red")
        flag=false
    }
    // Check if everything is OK.
    if (flag){
        $("#output").html("User Registered!!")
        $("#output").css("color","green")
    }
})

// Password show/hide functionality
$("#passshow").click(function(){
    if ($("#pass").attr("type")=="password"){
        $("#pass").attr("type", "text")
        $("#conpass").attr("type", "text")
    }
    else {
        $("#pass").attr("type", "password")
        $("#conpass").attr("type", "password")
    }
})