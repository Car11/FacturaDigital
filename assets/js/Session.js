class Session {

}

$(document).ready(function () {    
    var validator = new FormValidator({ "events": ['blur', 'input', 'change'] }, document.forms[0]);
    $('#frmLogin').submit(function(e){
        e.preventDefault();
        validatorResult = validator.checkAll(this);
        if (validatorResult.valid)
            Login();    
        return false;
    });

    // on form "reset" event
    document.forms[0].onreset = function (e) {
      validator.reset();
    }
});

function Login(){
    $.ajax({
        type: "POST",
        url: "class/Usuario.php",
        data: { 
            action: 'Login',               
            username:  $("#username").val(),
            password: $("#password").val(),
            beforeSend: function(){
                 $("#error").fadeOut();
            } 
        }        
    })
    .done(function( e ) {    
        var data= JSON.parse(e);
        if(data.status=='login')
            location.href= 'Dashboard.html';
        else alert("no login");
        //     $("#error").fadeIn(2000, function(){      
        //         $("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; Usuario/Contraseña inválido!</div>');
        //     });
        // else if(data.status=='error')
        //     $("#error").fadeIn(2000, function(){      
        //         $("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; Error!</div>');
        //     });
        // else if(data.status=='OK'){
        //     location.href= data.url;
        // }
    })    
    .fail(function( e ) {       
        showError(e);
    });
};

function showError(e) {
    //$(".modal").css({ display: "none" });  
    var data = JSON.parse(e.responseText);
    swal({
        type: 'error',
        title: 'Oops...',
        text: 'Algo no está bien (' + data.code + '): ' + data.msg, 
        footer: '<a href>Contacte a Soporte Técnico</a>',
    })    
    // var data= JSON.parse(e);
    // $("#error").fadeIn(2000, function(){
    //     $("#error").html('<div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp; '+data.status+' !</div>');
    // });
};



