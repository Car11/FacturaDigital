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
var UltProTou = ""; //Ultimo Producto Tocado -- Sirve para identificar cual fue el ultimo producto que se agrego o se modifico en la lista

$(document).ready(function () {

});

//Valida si se presiona enter -> carga el producto en la lista
//si preciona "*" -> Quita el "*", pasa el punto de insercion a la cantidad y selecciona el valor para ser reemplazdo con el teclado
$("#p_searh").keyup(function(e) {
    if(e.which == 13) {
       // Acciones a realizar, por ej: enviar formulario.
       LoadProducto();
    }
    if (e.which == 106) {
        $("#p_searh")[0].value = "";
        // $("#cant_"+ document.getElementById("productos").lastChild.childNodes[3].innerText).focus().select();;
        // se usa UltProTou xq es el UltimoProductoTocado (si la cantidad del producto es alterada no hay forma de saber cual fue el ultimo modificado.)
        $("#cant_"+ UltProTou).focus().select(); 
    }
 });



// Carga los productos a la lista de la factura
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
        ValidateProductFac(e);
    })
    .fail(function (e) {
        showError(e);
    });
};
// valida que el producto nuevo a ingresar no este en la lista
// si esta en la lista lo suma a la cantidad
// si es nuevo lo agrega a la lista
function ValidateProductFac(e){
    //compara si el articulo ya existe
    // carga lista con datos.
    var data = JSON.parse(e)[0];
    UltProTou = data.codigorapido;
    var repetido = false;

    if(document.getElementById("productos").rows.length != 0 && data != null){
        $(document.getElementById("productos").rows).each(function(i,item){
            // alert(item.childNodes[3].innerText);
            if(item.childNodes[3].innerText==data.codigorapido){
                item.childNodes[9].firstElementChild.value = parseFloat(item.childNodes[9].firstElementChild.value) + 1;
                item.childNodes[11].firstChild.textContent = "¢" + parseFloat((item.childNodes[7].firstChild.textContent).replace("¢","")) * parseFloat(item.childNodes[9].firstElementChild.value);
                repetido=true;
                calcTotal();
            }     
        });
    }    
    if (repetido==false){
        showDataProducto(e);
    }
};

//Agrega el producto a la lista
function showDataProducto(e) {
    // carga lista con datos.
    var data = JSON.parse(e);
    // Recorre arreglo.
    $.each(data, function (i, item) {
        var opt =
        `<tr class="even pointer">
            <td class="a-center ">
                <div class="icheckbox_flat-green" style="position: relative;">
                    <input type="checkbox" class="flat" name="table_records" style="position: absolute; opacity: 0;">
                    <ins class="iCheck-helper" style="position: absolute; top: 0%; left: 0%; display: block; width: 100%; height: 100%; margin: 0px; padding: 0px; background: rgb(255, 255, 255); border: 0px; opacity: 0;"></ins>
                </div>
            </td>
            <td class="codigorapido" id="id_${item.codigorapido}"> ${item.codigorapido} </td>
            <td class="descripcion">${item.descripcion}</td>
            <td class=" " id="precio_${item.codigorapido}">¢${item.precio}</td>
            <td class="">
               <input class="cantidad form-control" type="number" min="1" max="999" step="1" value="1" id="cant_${item.codigorapido}" onchange="importe(${item.codigorapido})">
            </td>
                <td id="importe_${item.codigorapido}">¢${item.precio}</td>
        </tr>`;
        //<option value="'+ item.id + '">'+ item.nombre + '</option>';
        $('#productos').append(opt);
        // evento click del boton modificar-eliminar
        //$('#option' + item.id).click(event-handler);
    })
    calcTotal();
};

//Calcula los totales cada vez que un producto es modificado
function calcTotal(){
    var subT=0;
    $(document.getElementById("productos").rows).each(function(i,item){
        // alert(item.childNodes[3].innerText);
        
        subT= subT + parseFloat((item.childNodes[11].textContent).replace("¢","")); 
    });
    $("#subtotal")[0].textContent = "¢"+subT.toFixed(2); 
    $("#desc_val")[0].textContent = "¢"+ (subT * (parseFloat(($("#desc_100")[0].textContent).replace("%",""))) / 100).toFixed(2) ;
    $("#iv_val")[0].textContent = "¢"+ ((subT * (parseFloat(($("#iv_100")[0].textContent).replace("%","")) / 100)) - (parseFloat(($("#desc_val")[0].textContent).replace("¢","")))).toFixed(2) ;
    $("#total")[0].textContent = "¢" + ((($("#subtotal")[0].textContent).replace("¢","")) - parseFloat(($("#desc_val")[0].textContent).replace("¢","")) + parseFloat(($("#iv_val")[0].textContent).replace("¢",""))).toFixed(2);
};


//Calcula el nuevo importe al cambiar la cantidad del prodcuto seleccionado de forma manual y no por producto repetido.
function importe(codigorapido){
    UltProTou = codigorapido;
    pUnit = $(`#precio_${codigorapido}`)[0].textContent.replace("¢","");
    cant = $(`#cant_${codigorapido}`)[0].value;
    $(`#importe_${codigorapido}`)[0].textContent = "¢" + (parseFloat(pUnit) * parseFloat(cant)).toString();
    $(`#importe_${codigorapido}`)[0].textContent
    $(`#cant_${codigorapido}`).keyup(function(e) {
        if(e.which == 13) {
           if (cant==0){
            // alert("Borar Producto");
            // $(`id_${codigorapido}`).parentElement
            var rm = $(`id_${codigorapido}`).context.activeElement.parentElement.parentElement;
            rm.remove();
            calcTotal();
           }
            calcTotal();
            $("#p_searh").focus();
        }
     });
};

$( "#fac-ccard" ).click(function(event) {
    $("#formapago").empty();


    var newDiv =
    `<div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-offset-0 col-xs-6 col-xs-offset-0">
            <h3 class="text-left" >Ingrese Ref.:</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <input class="input-lg" type="text" placeholder="Ingrese Numero Referencia"  required="" minlength="5" autofocus="">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-offset-0 col-xs-6 col-xs-offset-0">
            <button type="button" class="btn btn-primary" style="margin-top:10px;">Procesar</button>
        </div>
    </div>`;
    $("#formapago").append(newDiv);



});


$( "#fac-cash" ).click(function(event) {
    $("#formapago").empty();


    var newDiv =
    `<div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-offset-0 col-xs-6 col-xs-offset-0">
            <h3 class="text-left" >Paga con:</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <input class="input-lg" type="text" placeholder="Ingrese Monto en Efectivo"  required="" minlength="5" autofocus="">
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-offset-0 col-xs-6 col-xs-offset-0">
            <button type="button" class="btn btn-primary" style="margin-top:10px;">Procesar</button>
        </div>
    </div>`;
    $("#formapago").append(newDiv);



});


function facturar(metodo){
    alert("EFECTIVO");
    if (metodo == "cash"){
        alert("EFECTIVO");
    }
    if (metodo=="creditcard") {
        alert ("TARJETA");
    }
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


