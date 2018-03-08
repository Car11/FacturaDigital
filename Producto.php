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
        <thead> <tr> <th>ID</th> <th>NOMBRE</th> <th>SCANCODE</th> <th>CANTIDAD</th> </tr> </thead> <tbody id='tableBody-Producto'></tbody>
    </table>
    
    <form id="frmProducto" name="frmProducto" >
        <input type="text" id="id" name="id" placeholder="UUID" required>
        <input type="text" id="nombre" name="nombre" placeholder="Nombre" required>
        <button type="submit" value="submit" id="btnProducto" >SUBMIT</button>
    </form>
</body>
</html>