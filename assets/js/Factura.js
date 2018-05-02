class Factura {
    // Constructor
    constructor(id, cajero, subtotal, descuento, producto, total, fechaCreacion, idfila, p_codigo, p_descripcion, p_precio, p_cantidad, importe) {
        this.id = id || null;
        this.cajero = cajero || '';
        this.subtotal=subtotal || '';
        this.descuento=descuento || 0;
        this.producto=producto || 0;
        this.total= total || '';
        this.fechaCreacion= fechaCreacion || null;
        this.idfila=idfila || 0;
        this.p_codigo= p_codigo || '';
        this.p_precio=p_precio || 0;
        this.p_cantidad=p_cantidad || 0;
        this.importe=importe || 0;
    }

    //Get
    get Importe() {
        return this.cantidad * this.precio;
    }
}

let factura = new Factura();

$(document).ready(function () {
    Session.Check();
//     if(!Session.state)
//         return;
    // Load list
    //LoadAll();
    //LoadCategories();
    //Form Validate
    /*$('#frmProducto').Validate({
        submitHandler: function() {
            //$('#btnProducto').attr("disabled", "disabled");
            Save();   
        }
    });*/
    // eventos
    $("tbody tr").click(function () {
        $('.selected').removeClass('selected');
        $(this).addClass("selected");
        // var product = $('.p',this).html();
        // var infRate =$('.i',this).html();
        // var note =$('.n',this).html();
        // alert(product +','+ infRate+','+ note);
        alert("si entra")
    });

    $(".cantidad").change(function() {
        // factura.cantidad= $('#cant_1').val();
        // factura.precio= $('#precio_1').text();
        // $('#importe1').text(factura.Importe);
        alert ("entra");
    });
});

$("#p_searh").keypress(function(e) {
    if(e.which == 13) {
       // Acciones a realizar, por ej: enviar formulario.
       LoadProducto();
    }
 });



// Carga lista
function LoadProducto() {
    factura.p_codigo = $("#p_searh").val();  //Columna 0 de la fila seleccionda= ID.
    $.ajax({
        type: "POST",
        url: "class/Factura.php",
        data: {
            action: "LoadProducto",
            producto: JSON.stringify(factura)
        }
    })
    .done(function (e) {
        CleanCtls();
        showDataProducto(e);
    })
    .fail(function (e) {
        showError(e);
    });
};

function showDataProducto(e) {
    // carga lista con datos.
    var data = JSON.parse(e);
    // Recorre arreglo.
    $.each(data, function (i, item) {
        var opt =
        '<tr class="even pointer">' +
            '<td class="a-center ">' +
                '<div class="icheckbox_flat-green" style="position: relative;">' +    
                    '<input type="checkbox" class="flat" name="table_records" style="position: absolute; opacity: 0;">' +
                    '<ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>' +
                '</div>' +
            '</td>' +
            '<td class=" ">'+ item.codigorapido + '</td>' +
            '<td class=" ">'+ item.descripcion + '</td>' +
            '<td class=" " id="precio_'+ item.codigorapido + '">¢'+ item.precio+ '</td>' +
            '<td class="">' +
            '   <input class="cantidad form-control" type="number" min="1" max="9999" step="1"value="1" id="cant_'+ item.codigorapido + '">' +
            '</td>' +
                '<td id="importe_'+ item.codigorapido + '">¢'+ item.precio + '</td>' +
        '</tr>';
        // '<option value="'+ item.id + '">'+ item.nombre + '</option>';
        $('#productos').append(opt);
        // evento click del boton modificar-eliminar
        //$('#option' + item.id).click(event-handler);
    })
};

// Carga lista
function LoadAll() {
    id=null;
    $.ajax({
        type: "POST",
        url: "../../class/Producto.php",
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
    
    // swal({
    //     position: 'top-end',
    //     type: 'success',
    //     title: 'Good!',
    //     showConfirmButton: false,
    //     timer: 1500
    // });
};

// Muestra errores en ventana
function showError(e) {    
    //$(".modal").css({ display: "none" });  
    var data = JSON.parse(e.responseText);
    alert("ERROR");
    // swal({
    //     type: 'error',
    //     title: 'Oops...',
    //     text: 'Algo no está bien (' + data.code + '): ' + data.msg, 
    //     footer: '<a href>Contacte a Soporte Técnico</a>',
    //   })
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
                '<td>' + item.nombre + '</td>' +
                '<td>' + item.scancode + '</td>' +
                '<td>' + item.cantidad + '</td>' +
                '<td>' + item.precio + '</td>' +
                '<td>' + item.codigorapido + '</td>' +
                '<td><img id=btnmodingreso'+ item.id + ' src=img/file_mod.png></td>'+
                '<td><img id=btnborraingreso'+ item.id + ' src=img/file_delete.png></td>'+
            '</tr>';
        $('#tableBody-Producto').append(row);
        // evento click del boton modificar-eliminar
        $('#btnmodingreso' + item.id).click(UpdateEventHandler);
        $('#btnborraingreso' + item.id).click(DeleteEventHandler);
    })
};

function showDataCategoria(e) {
    // carga lista con datos.
    var data = JSON.parse(e);
    // Recorre arreglo.
    $.each(data, function (i, item) {
        var opt =
            '<option value="'+ item.id + '">'+ item.nombre + '</option>';
        $('#categoria').append(opt);
        // evento click del boton modificar-eliminar
        //$('#option' + item.id).click(event-handler);
    })
};

function UpdateEventHandler() {
    producto.id = $(this).parents("tr").find("td").eq(0).text();  //Columna 0 de la fila seleccionda= ID.
    $.ajax({
        type: "POST",
        url: "class/Producto.php",
        data: {
            action: 'Load',
            producto: JSON.stringify(producto)
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
    producto.id = $(this).parents("tr").find("td").eq(0).text(); //Columna 0 de la fila seleccionda= ID.
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
            producto:  JSON.stringify(producto)
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
    })    
    .fail(function (e) {
        showError(e);
    })
    .always(LoadAll);
};

function CleanCtls() {
    $("#p_searh").val('');
    // $("#nombre").val('');    
    // $("#cantidad").val('');
    // $("#precio").val('');
    // $("#codigorapido").val('');
    // $("#fechaExpiracion").val('');
    // $("#categoria").val('optdef');
};

function ShowItemData(e) {
    // Limpia el controles
    CleanCtls();    
    // carga objeto.
    var data = JSON.parse(e)[0];
    producto = new Producto(data.id, data.nombre, data.scancode, data.cantidad, data.precio, data.codigorapido, data.idcategoria, data.fechaExpiracion);
    // Asigna objeto a controles
    $("#id").val(producto.id);
    $("#nombre").val(producto.nombre);
    $("#precio").val(producto.precio);
    $("#cantidad").val(producto.cantidad);
    $("#codigorapido").val(producto.codigorapido);
    $("#categoria").val(producto.idcategoria);
    $("#fechaExpiracion").val(producto.fechaExpiracion);
};

function Save(){   
    // Ajax: insert / Update.
    var miAccion= producto.id==null ? 'Insert' : 'Update';
    producto.nombre = $("#nombre").val();
    producto.cantidad = $("#cantidad").val();
    producto.precio = $("#precio").val();
    producto.codigorapido = $("#codigorapido").val();
    producto.idcategoria= $("#categoria").val();
    producto.fechaExpiracion= $("#fechaExpiracion").val();
    //
    $.ajax({
        type: "POST",
        url: "../../../class/Producto.php",
        data: { 
            action: miAccion,  
            producto: JSON.stringify(producto)
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


