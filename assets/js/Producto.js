// function Producto(id){
//     this.id=id;
// }

// function Producto(id, nombre, scancode, cantidad){
//     this.id=id;
//     this.nombre=nombre;
//     this.scancode=scancode;
//     this.cantidad=cantidad;
// }

// function Producto(id, nombre){
//     this.id=id;
//     this.nombre=nombre;
// }

class Producto {
    constructor(id, nombre, scancode='', cantidad=0) {
        this.id = id;
        this.nombre = nombre;
        this.scancode=scancode;
        this.cantidad=cantidad;
    }
}

$(document).ready(function () {
    $('#btnProducto').click(FormValidate);
    LoadAll();
    //Form Validate
});

// Carga lista
function LoadAll() {
    id=null;
    $.ajax({
        type: "POST",
        url: "class/Producto.php",
        data: {
            action: "LoadAll"
        }
    })
    .done(function (e) {
        CleanCtls();
        showData(e);
    })
    .fail(function (e) {
        showError(e);
    });
};

// Muestra información en ventana
function showInfo() {
    //$(".modal").css({ display: "none" });  
    swal({
        position: 'top-end',
        type: 'success',
        title: 'Good!',
        showConfirmButton: false,
        timer: 1500
    });
};

// Muestra errores en ventana
function showError(e) {    
    //$(".modal").css({ display: "none" });  
    var data = JSON.parse(e.responseText);
    swal({
        type: 'error',
        title: 'Oops...',
        text: 'Algo no está bien (' + data.code + '): ' + data.msg, 
        footer: '<a href>Contacte a Soporte Técnico</a>',
      })
};

function showData(e) {
    // Limpia el div que contiene la tabla.
    $('#tableBody-Producto').html("");
    // carga lista con datos.
    var data = JSON.parse(e);
    // Recorre arreglo.
    $.each(data, function (i, item) {
        var row =
            '<tr>' +
            '<td>' + item.id + '</td>' +
            '<td>' + item.Nombre + '</td>' +
            '<td>' + item.scancode + '</td>' +
            '<td>' + item.Cantidad + '</td>' +
            // '<td><img id=btnmodingreso'+ item.id + ' class=borrar src=img/file_mod.png></td>'+
            // '<td><img id=btnborraingreso'+ item.id + ' class=borrar src=img/file_delete.png></td>'+
            '</tr>';
        $('#tableBody-Producto').append(row);
        // evento click del boton modificar-eliminar
        //$('#btnmodingreso' + item.id).click(UpdateEventHandler);
        //$('#btnborraingreso' + item.id).click(DeleteEventHandler);
    })
};

function UpdateEventHandler() {
    id = $(this).parents("tr").find("td").eq(0).text();  //Columna 0 de la fila seleccionda= ID.
    $.ajax({
        type: "POST",
        url: "class/Producto.php",
        data: {
            action: 'Load',
            id: id
        }
    })
    .done(function (e) {
        ShowItemData(e);
    })
    .fail(function (e) {
        showError(e);
    });
};

function DeleteEventHandler() {
    id = $(this).parents("tr").find("td").eq(0).text(); //Columna 0 de la fila seleccionda= ID.
    // Mensaje de borrado:
    swal({
        title: 'Eliminar?',
        text: "Esta acción es irreversible!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, eliminar!',
        cancelButtonText: 'No, cancelar!',
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger'
    }).then(function () {
        // eliminar registro.
        Delete();
    })
};

function Delete() {
    $.ajax({
        type: "POST",
        url: "class/Producto.php",
        data: { 
            action: 'Delete',                
            id:  id
        }            
    })
    .done(function( e ) {        
        var data = JSON.parse(e);   
        if(data.status==1)
        {
            swal(
                'Mensaje!',
                'El registro se encuentra  en uso, no es posible eliminar.',
                'error'
            );
        }
        else swal(
            'Eliminado!',
            'El registro se ha eliminado.',
            'success'
        );
        LoadAll();
    })    
    .fail(function (e) {
        showError(e);
    });
};

function CleanCtls() {
    // $("#inp-Nombre-Producto").val('');
    // $("#inp-PrecioFinal-Producto").val('');    
    // $("#inp-Cantidad-Producto").val('');
    // $("#inp-Codigo-Producto").val('');
    // $("#inp-var5-Producto").val('');
};

function ShowItemData(e) {
    // Limpia el controles
    CleanCtls();
    // carga objeto.
    var data = JSON.parse(e);
    $("#id").val(data[0].id);
    $("#nombre").val(data[0].Nombre);
};

function FormValidate(){
    $("#frmProducto").validate({
        lang: 'es', 
        rules: {
            'id': "required",
            'nombre': "required"
        },
        submitHandler: function() {
            $('#btnProducto').attr("disabled", "disabled");
            Save();   
        }
    });  
};

// Save
function Save(){   
    // Ajax: insert / Update.
    var miAccion= id==null ? 'Insert' : 'Update';
    $.ajax({
        type: "POST",
        url: "class/Producto.php",
        data: { 
            action: miAccion,  
            id: id,              
            Nombre: $("#nombre").val()
        }
    })
    .done(showInfo)
    .fail(function (e) {
        showError(e);
    })
    .always(function() {
        setTimeout('$("#btnProducto").removeAttr("disabled")', 1500);
        LoadAll();   
    });
}; 


