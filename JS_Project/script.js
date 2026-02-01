document.getElementById("greetbut").onclick = function(){
    document.getElementById("heading").innerHTML = "Hello, " + document.getElementById("name").value + "!";
}

document.getElementById("red").onclick=function(){
    document.getElementById("red").style.background="red";
    document.getElementById("red").style.color="white";
}

document.getElementById("yellow").onclick=function(){
    document.getElementById("yellow").style.background="yellow";
    document.getElementById("yellow").style.color="white";
}

document.getElementById("green").onclick=function(){
    document.getElementById("green").style.background="green";
    document.getElementById("green").style.color="white";
}

document.getElementById("blue").onclick=function(){
    document.getElementById("blue").style.background="blue";
    document.getElementById("blue").style.color="white";
}

document.getElementById("bg").onclick=function(){
    document.body.style.background="orange";
}