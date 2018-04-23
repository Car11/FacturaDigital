function CheckSession(){
    $.ajax({
        type: "POST",
        url: "class/Usuario.php",
        data: {
            action: 'CheckSession',
            url: window.location.href
        }
    })
    .done(function( e ) {
        var data= JSON.parse(e);
        if(data.status=='invalido')
            location.href= 'login.html';
    })    
    .fail(function( e ) {        
        showError(e);
        location.href= 'login.html';
    });
};



