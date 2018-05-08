<HTML>
<BODY>
<form action="#" method="post"
    enctype="multipart/form-data">
    <div><label id="upload">Seleccione archivo txt, doc o pdf a cargar:
        <input type="file" id="upload" name="upload"/></label></div>
    <div>
    <input type="hidden" name="action" value="upload"/>
    <input type="submit" value="Cargar"/>
  </div>
</form>
<?php
//** El objetivo es realizar todas las comunicaciones con POST, permitir solo subir archivos doc, pdf y txt, verificar el tamanio
//** del archivo a subir, y no permitir mostrar archivos que contengan .. o /.
$load_err="";
// Upload de archivo
if(isset($_POST['action'])) {
   $target_path = "uploads/";
   $target_path = $target_path . basename( $_FILES['upload']["name"]);
   //---------- Validacion del Archivo -------------
   //Solo se permiten txt, doc y pdf. Se verifica tanto extension, MIME del archivo, y su tamanio.
   $allowedExts = array("pdf", "doc", "txt");
   $temp = explode(".", $_FILES["upload"]["name"]);
   $extension = end($temp);
   if (($_FILES["upload"]["type"] == "application/pdf")
   || ($_FILES["upload"]["type"] == "application/doc")
   || ($_FILES["upload"]["type"] == "text/plain")
   && ($_FILES["upload"]["size"] < 200000) //verificar tambien que el tamanio no sea muy grande, pues puede ser alguna otra cosa.
   && in_array($extension, $allowedExts)) {
      if(move_uploaded_file($_FILES['upload']['tmp_name'], $target_path)) {
         echo "<b>El archivo ".  basename( $_FILES['upload']['name'])." ha sido cargado exitosamente!</b>";
      }
   }else{
   $load_err="Error: extension o tamanio invalido!";
   }
}
?>
<span class="help-block"><?php echo $load_err; ?></span>
<form action="#" method="post">
<input type="submit" name="submit" value="Mostrar archivo" />
<select name="level[]">
<?php
// Mostrar archivos en dropdown
echo "<option value= ></option>";
$dirname = "uploads/";
$dirhandle = opendir($dirname);
while($file = readdir($dirhandle)) {
   if ($file != "." && $file != "..") {
      echo "<option value='" . $file ."'>" . $file . "</option>";
   }
}
?>
</select>
</form>
<?php
// Mostrar contenido del archivo seleccionado
if(isset($_POST['submit'])) {
   foreach ($_POST['level'] as $select){
      //-------- Validacion para LFI ------------
      if (!strpos($select, "..") and !strpos($select, "/")) {
         echo "Mostrando archivo <b>$select</b><br><br>";
         $handle = fopen($select, "r");
         if ($handle) {
            while (($line = fgets($handle)) !== false) {
               echo "<a>{$line}</a>";
            }
            fclose($handle);
         }
      }else{
         echo "<b>Hmm que estas tratando de hacer >:| </b>";
      }
   }
}
?>
</BODY>
</HTML>

