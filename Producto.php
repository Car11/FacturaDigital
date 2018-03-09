<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Productos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- css -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
    <!-- js -->
    <script src="assets/js/jquery.min.js"></script></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
    <script src="assets/js/sweetalert2.min.js"></script>
    <script src="assets/js/Producto.js"></script>
</head>
<body>
    <table id='tbl'>
        <thead> <tr> <th>ID</th> <th>NOMBRE</th> <th>SCANCODE</th> <th>CANTIDAD</th> <th>PRECIO</th> <th>CODIGO</th> <th>ACcIONES</th> </tr> </thead> <tbody id='tableBody-Producto'></tbody>
    </table>
    
    <form id="frmProducto" name="frmProducto" >
        <input type="text" id="id" name="id" placeholder="UUID" required>
        <input type="text" id="nombre" name="nombre" placeholder="nombre" required>
        <input type="text" id="cantidad" name="cantidad" placeholder="Cantidad">
        <input type="text" id="precio" name="precio" placeholder="Precio" >
        <input type="text" id="codigorapido" name="codigorapido" placeholder="Codigo Rapido" >
        <select id="categoria" >  
            <option disabled="disabled" selected="selected" value="optdef">Categorías</option>
        </select>
        <input id="fecha" name="fecha" type="date" />
        <input type="text" id="calc" name="calc" placeholder="***CALC***" >
        <button type="submit" value="submit" id="btnProducto" >SUBMIT</button>
    </form>

</body>

</html>