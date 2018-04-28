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
        else{
            $('#call_username').html(
                '<img src="images/user.png" alt="" > ' + data.username + ' ' + 
                '<span class=" fa fa-angle-down" ></span> '        
            );
            $('#call_name').text(data.nombre);
        }        
    })    
    .fail(function( e ) {        
        showError(e);
        location.href= 'login.html';
    });
};


function EndSession(){
    $.ajax({
        type: "POST",
        url: "class/Usuario.php",
        data: {
            action: 'EndSession'
        }
    })
    .done(function( e ) {
        location.href= 'login.html';
    })    
    .fail(function( e ) {        
        showError(e);
        //location.href= 'login.html';
    });
};


