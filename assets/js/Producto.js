class Producto {
    // Constructor
    constructor(id, nombre, scancode, cantidad, precio, codigorapido, idcategoria, fechaExpiracion, descripcion) {
        this.id = id || null;
        this.nombre = nombre || '';
        this.descripcion = this.descripcion || '';
        this.scancode=scancode || '';
        this.cantidad=cantidad || 0;
        this.precio=precio || 0;
        this.codigorapido= codigorapido || '';
        this.idcategoria= idcategoria || null;
        this.fechaExpiracion= fechaExpiracion || null;
    }

    //Getter
    get Read() {
        var miAccion= this.id==null ? 'ReadAll' : 'Read';
        $.ajax({
            type: "POST",
            url: "class/Producto.php",
            data: { 
                action: miAccion,
                id: this.id
            }
        })
        .done(function( e ) {
            producto.Reload(e);
        })    
        .fail(function (e) {
            producto.showError(e);
        });
    }

    get Save(){
        $('#btnProducto').attr("disabled", "disabled");
        var miAccion= producto.id==null ? 'Create' : 'Update';
        this.nombre = $("#nombre").val();
        this.descripcion = $("#descripcion").val();
        this.cantidad = $("#cantidad").val();
        this.precio = $("#precio").val();
        this.codigorapido = $("#codigorapido").val();
        this.idcategoria= $("#categoria").val();
        this.fechaExpiracion= $("#fechaExpiracion").val();
        $.ajax({
            type: "POST",
            url: "class/Producto.php",
            data: {
                action: miAccion,  
                obj: JSON.stringify(this)
            }
        })
        .done(producto.showInfo)
        .fail(function (e) {
            producto.showError(e);
        })
        .always(function() {
            setTimeout('$("#btnProducto").removeAttr("disabled")', 1000);
            producto= new Producto();
            producto.ClearCtls();
            producto.Read;   
        });
    }

    get Delete() {
        $.ajax({
            type: "POST",
            url: "class/Producto.php",
            data: { 
                action: 'Delete',                
                id: this.id
            }            
        })
        .done(function() {
            swal({
                //position: 'top-end',
                type: 'success',
                title: 'Eliminado!',
                showConfirmButton: false,
                timer: 1000
            });
        })    
        .fail(function (e) {
            producto.showError(e);
        })
        .always(function(){
            producto= new Producto();
            producto.Reload();
        });
    }

    // Methods
    Reload(e){
        if(this.id==null)
            this.ShowAll(e);
        else this.ShowItemData(e);
    };

    // Muestra información en ventana
    showInfo() {
        //$(".modal").css({ display: "none" });  
        swal({
            position: 'top-end',
            type: 'success',
            title: 'Good!',
            showConfirmButton: false,
            timer: 1000
        });
        // new PNotify({
        //         title: 'Hecho',
        //         text: 'xxx xxx xxx!',
        //         type: 'success',
        //         styling: 'bootstrap3'
        //     });
    };

    // Muestra errores en ventana
    showError(e) {
        //$(".modal").css({ display: "none" });  
        var data = JSON.parse(e.responseText);
        swal({
            type: 'error',
            title: 'Oops...',
            text: 'Algo no está bien (' + data.code + '): ' + data.msg, 
            footer: '<a href>Contacte a Soporte Técnico</a>',
        })
    };

    ClearCtls() {
        $("#id").val('');
        $("#nombre").val('');    
        $("#descripcion").val('');
        $("#cantidad").val('');
        $("#precio").val('');
        $("#codigorapido").val('');
        $("#fechaExpiracion").val('');
        $("#categoria").val('optdef');
    };

    ShowAll(e) {
        // Limpia el div que contiene la tabla.
        $('#tableBody-Producto').html("");
        // Carga lista
        var data = JSON.parse(e);
        $.each(data, function (i, item) {
            $('#tableBody-Producto').append(`            
                <tr> 
                    <td class="a-center ">
                        <input type="checkbox" class="flat" name="table_records">
                    </td>
                    <td class="itemId" style="display: none" >${item.id}</td>
                    <td>${item.nombre}</td>
                    <td>${item.codigorapido}</td>
                    <td>${item.cantidad}</td>
                    <td>${item.precio}</td>
                    <td class=" last">                     
                        <a href="#" id="update${item.id}" data-toggle="modal" data-target=".bs-example-modal-lg" > <i class="glyphicon glyphicon-edit" > </i> Editar   </a>                         
                        <a href="#" id="delete${item.id}" data-toggle="modal" data-target=".bs-example-modal-lg" > <i class="glyphicon glyphicon-trash"> </i> Eliminar </a> 
                    </td>
                </tr>
            `);
            // event Handler
            $('#update' + item.id).click(producto.UpdateEventHandler);
            $('#delete' + item.id).click(producto.DeleteEventHandler);
        })
    };

    UpdateEventHandler() {
        producto.id = $(this).parents("tr").find(".itemId").text();  //Class itemId = ID del objeto.
        producto.Read;
    };
    
    ShowItemData(e) {
        // Limpia el controles
        this.ClearCtls();    
        // carga objeto.
        var data = JSON.parse(e)[0];
        producto = new Producto(data.id, data.nombre, data.scancode, data.cantidad, data.precio, data.codigorapido, data.idcategoria, data.fechaExpiracion);
        // Asigna objeto a controles
        $("#id").val(producto.id);
        $("#nombre").val(producto.nombre);
        $("#descripcion").val(producto.descripcion);
        $("#precio").val(producto.precio);
        $("#cantidad").val(producto.cantidad);
        $("#codigorapido").val(producto.codigorapido);
        $("#categoria").val(producto.idcategoria);
        $("#fechaExpiracion").val(producto.fechaExpiracion);
    };
    
    DeleteEventHandler() {
        producto.id = $(this).parents("tr").find(".itemId").text();  //Class itemId = ID del objeto.
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
        }).then((result) => {
            if (result.value) {
                producto.Delete;
            }
        })
    };

    Init() {    
        // $('#btnProducto').click(function(){
        //     producto.Save;
        // });
           
        //Form Validate
        // $('#frmProducto').Validate({
        //     submitHandler: function() {
        //         producto.Save;   
        //     }
        // });

        // $('#frmProducto').submit(function(e){
        //     e.preventDefault();
        //     var submit = true;
        //     // you can put your own custom validations below
    
        //     // check all the rerquired fields
        //     if( !validator.checkAll( $(this) ) )
        //         submit = false;
    
        //     if( submit ){
        //         //this.submit();
        //         producto.Save;
        //     }
                
    
        //     return false;
        // })
    };
}

//Class Instance
let producto = new Producto();







