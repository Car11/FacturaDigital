class Categoria {
    // Constructor
    constructor(id, nombre, descripcion) {
        this.id = id || null;
        this.nombre = nombre || '';
        this.descripcion = this.descripcion || '';
    }

    //Getter
    get Read() {
        var miAccion= this.id==null ? 'ReadAll' : 'Read';
        $.ajax({
            type: "POST",
            url: "class/Categoria.php",
            data: { 
                action: miAccion,
                id: this.id
            }
        })
        .done(function( e ) {
            Reload(e);
        })    
        .fail(function (e) {
            showError(e);
        });
    }

    get Save(){
        $('#btnCategoria').attr("disabled", "disabled");
        var miAccion= Categoria.id==null ? 'Create' : 'Update';
        this.nombre = $("#nombre").val();
        this.descripcion = $("#descripcion").val();
        $.ajax({
            type: "POST",
            url: "class/Categoria.php",
            data: { 
                action: miAccion,  
                obj: JSON.stringify(this)
            }
        })
        .done(showInfo)
        .fail(function (e) {
            showError(e);
        })
        .always(function() {
            setTimeout('$("#btnCategoria").removeAttr("disabled")', 1000);
            Categoria= new Categoria();
            ClearCtls();
            Reload();   
        });
    }

    get Delete() {
        $.ajax({
            type: "POST",
            url: "class/Categoria.php",
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
            showError(e);
        })
        .always(function(){
            comment= new Categoria();
            Reload();
        });
    }
}


//Class Instance
let categoria = new Categoria();

function Init() {
    // Read list
    categoria.Read;
    $('#btnCategoria').click(function(){
        categoria.Save;
    });
    //Form Validate
    // $('#frmcategoria').Validate({
    //     submitHandler: function() {
    //         categoria.Save;   
    //     }
    // });
};

// Methods
function Reload(e){
    if(categoria.id==null)
        ShowAll(e);
    else ShowItemData(e);
};

// Muestra información en ventana
function showInfo() {
    //$(".modal").css({ display: "none" });  
    swal({
        position: 'top-end',
        type: 'success',
        title: 'Good!',
        showConfirmButton: false,
        timer: 1000
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

function ClearCtls() {
    $("#id").val('');
    $("#nombre").val('');    
    $("#descripcion").val('');
};

function ShowAll(e) {
    // Limpia el div que contiene la tabla.
    $('#tableBody-categoria').html("");
    // Carga lista
    var data = JSON.parse(e);
    $.each(data, function (i, item) {
        $('#tableBody-categoria').append(`
            <tr> 
                <td class="a-center ">
                    <input type="checkbox" class="flat" name="table_records">
                </td>
                <td class="itemId" style="display: none" >${item.id}</td>
                <td>${item.nombre}</td>
                <td>${item.descripcion}</td>
                <td class=" last"> 
                    <a id="update${item.id}" > <i class="glyphicon glyphicon-edit"> </i> Editar </a> 
                    <a id="delete${item.id}" > <i class="glyphicon glyphicon-trash"> </i> Eliminar </a> 
                </td>
            </tr>
        `);
        // event Handler
        $('#update' + item.id).click(UpdateEventHandler);
        $('#delete' + item.id).click(DeleteEventHandler);
    })
};

function ShowList(e) {
    // carga lista con datos.
    var data = JSON.parse(e);
    // Recorre arreglo.
    $.each(data, function (i, item) {
        $('#categoria').append(`
            <option value="${item.id}">${item.nombre}</option>
        `);
    })
};

function UpdateEventHandler() {
    categoria.id = $(this).parents("tr").find(".itemId").text();  //Class itemId = ID del objeto.
    categoria.Read;
};

function ShowItemData(e) {
    // Limpia el controles
    ClearCtls();    
    // carga objeto.
    var data = JSON.parse(e)[0];
    categoria = new categoria(data.id, data.nombre, data.descripcion);
    // Asigna objeto a controles
    $("#id").val(categoria.id);
    $("#nombre").val(categoria.nombre);
    $("#descripcion").val(categoria.descripcion);
};

function DeleteEventHandler() {
    categoria.id = $(this).parents("tr").find(".itemId").text();  //Class itemId = ID del objeto.
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
            categoria.Delete;
        }
    })
};