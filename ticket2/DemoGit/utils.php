function inicio()
{
 echo '<!DOCTYPE html>
 <html lang="es">
 <head>
    <title></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script  src="https://code.jquery.com/jquery-3.7.1.min.js"  integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="  crossorigin="anonymous">
    </script>
    <script>
         function cargar(div,desde)
         {
         $(div).load(desde);
         } 
         </script>
         <script>
         function poner_nombre(div,nombre)
         {
         $(div).text(nombre);
         } 
         
    </script>
   
  </head>
  
  
  <body>' 
}