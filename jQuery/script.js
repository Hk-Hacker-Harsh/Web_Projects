const inputField = document.getElementById('phone');

inputField.addEventListener('input', (e) => {
    let digits = e.target.value.replace(/\D/g, '');
    
    digits = digits.substring(0, 12);
    
    let formatted = '';

    if (digits.length > 0) {
        formatted = '+';

        if (digits.length <= 2) {
            formatted += digits;
        } else if (digits.length <= 7) {
            formatted += digits.substring(0, 2) + ' ' + digits.substring(2);
        } else {
            formatted += digits.substring(0, 2) + ' ' + digits.substring(2, 7) + ' ' + digits.substring(7);
        }
    }
    e.target.value = formatted;
});



// Functions
function IsPhone(num){
    if (num.slice(0,3)!="+91"){
        return false;
    }

    if(num.length!=15){
        return false;
    }
    return true;

}

function IsEmail(email) {
    const regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if (!regex.test(email)) {
        return false;
    }
    else {
        return true;
    }}

function showError(msg) {
        $("#output").html(msg).addClass("alert-error").fadeIn();
    }

function passchk(password){
    if (password.length < 8){
        showError("Password must be 8+ characters.")
        return false;
    }
    if ((password.match(/[a-z]/)) == null){
        showError("Need 1 Lower Case letter.")
        return false;
    }
    if ((password.match(/[A-Z]/)) == null){
        showError("Need 1 Upper Case letter.")
        return false;
    }
    if ((password.match(/[0-9]/)) == null){
        showError("Need 1 Numeric character.")
        return false;
    }
    if ((password.match(/[@#.!$%&*()\-_=+|,;:'"]/)) == null){
        showError("Need 1 Special character.")
        return false;
    }
    return true;
}


$("#butt").click(function(){
    //variables
    const email = $("#email").val();
    const phone = $("#phone").val();
    const pass = $("#pass").val();
    const confirmpass = $("#conpass").val();
    const output = $("#output");

    output.removeClass("alert-error alert-success").hide();

    //Empty Check
    if (email == "" || phone == "" || pass == "" || confirmpass == ""){
        showError("Input Fields cannot be Empty!!");
       return;
    }

    // Email Check
    if (!IsEmail(email)){
        showError("Please Enter Valid Email Address!!");
        return;
    }

    // Phone Check
    if (!IsPhone(phone)){
        showError("Please Enter Valid 10-digit Indian Number!!");
        return;
    }

    // Pass Check
    if (!passchk(pass)){
        return;
    }

    // Pass Match
    if(pass!=confirmpass){
        showError("Passwords do not match!");
        return;
    }
    // Check if everything is OK.
    
    output.html("User Registered Successfully!!").addClass("alert-success").fadeIn();
    output.css("color","green")
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