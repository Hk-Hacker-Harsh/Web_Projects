const inputField = document.getElementById('phone');

if (inputField.value === "") {
    inputField.value = "+91 ";
}

inputField.addEventListener('input', (e) => {
let value = e.target.value;

    // Ensure it always starts with +91
    if (!value.startsWith('+91 ')) {
        value = '+91 ' + value.replace(/^\+91\s?/, '');
    }

    // Extract only the digits AFTER the +91
    let digits = value.substring(4).replace(/\D/g, '');
    
    // Limit to 10 digits
    digits = digits.substring(0, 10);
    
    // 4. Formatting: +91 12345 67890
    let formatted = '+91 ';
    if (digits.length > 5) {
        formatted += digits.substring(0, 5) + ' ' + digits.substring(5);
    } else {
        formatted += digits;
    }

    e.target.value = formatted;
});

// Prevent user from moving cursor or deleting +91 with backspace
inputField.addEventListener('keydown', (e) => {
    if (e.target.selectionStart < 4 && (e.key === 'Backspace' || e.key === 'Delete')) {
        e.preventDefault();
    }
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