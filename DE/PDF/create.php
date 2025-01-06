<?php
  //$html = file_get_contents("../PDF/protokollscelet.txt");

?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>PDF Entwurf</title>
<style>
.container-fluid{
    padding: 10px;
}
h2{
  padding: 0px;
}
.container{
    padding: 10px;
}
.center{
  text-align: center;
}
.top-3{
  padding-top: 20px;
}
.right{
  text-align: right;
}
th, td {
  padding: 4px;
}
td{
  font-size: 12px;
}
.margin-auto{
  margin: auto;
}
.margin-top{
  margin-top: 30px;
}
tr{
  border-bottom: 1px solid #ccc;
}
.text-capitalize{
  text-transform: capitalize;
}
.border-bottom{
  border-bottom: 1px solid #ccc;
}
.border{
  border: 1px solid #ccc;
}
.img-fluid{
  width: 500px;
}
</style>
</head>
<body>
  <?php include "../PDF/protokollscelet.php";?>
</body>
</html>
