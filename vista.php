<?php
function aniadirPlantilla($seccion, $urlPlantilla,$nombreEtiqueta):string
{
    $plantilla = file_get_contents($urlPlantilla);
    return (str_replace($nombreEtiqueta,$plantilla,$seccion));
}
function obtenerCabecera($rolUsuario): string
{
    $cabecera = file_get_contents("./templates/cabecera/cabecera.html");
    if ($rolUsuario === "user") {
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/seccionCabeceraLogueado.html", "##seccionLogueado##");
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraLogueado.html", "##botonCabecera##");
    }
    else if ($rolUsuario === "admin"){
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/seccionCabeceraAdmin.html", "##seccionLogueado##");
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraLogueado.html", "##botonCabecera##");
    }
    else{
        $cabecera = str_replace("##seccionLogueado##","",$cabecera);
        $cabecera = aniadirPlantilla($cabecera, "./templates/cabecera/botonCabeceraNoLogueado.html", "##botonCabecera##");
    }
    return $cabecera;
}
/***********************Funciones auxiliares**************************/
function vMostrarHome($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/home.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Home",$page);
    echo($page);
}
function vMostrarOfertas($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/ofertas.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Ofertas",$page);
    echo($page);
}
function vMostrarCatalogo($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if($resultado === -1){
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","Ha habido un fallo mostrando el catalogo , por favor intentelo mas tarde", $userAlert);
        $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
        $page = str_replace("##TITLE##","Error Modelos",$page);
        echo($page);
    }
    else{
        $seccion = file_get_contents("./templates/secciones/modelos.html");
        $trozos = explode("##coche##",$seccion);
        $catalogo = "";
        while($datos = $resultado->fetch_assoc()){
            $coche = $trozos[1];
            $coche = str_replace("##marca##",$datos["marca"],$coche);
            $coche = str_replace("##modelo##",$datos["modelo"],$coche);
            $coche = str_replace("##precio##",$datos["precio"],$coche);
            $coche = str_replace("##imagen##",$datos["foto"],$coche);
            $coche = str_replace("##matricula##",$datos["matricula"],$coche);
            $catalogo = $catalogo.$coche;
        }
        $seccion = $trozos[0].$catalogo.$trozos[2];
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        $page = str_replace("##TITLE##","Modelos",$page);
        echo($page);
    }
}
function vMostrarInfoVehiculo($resultado, $resultadoObtenerVehiculo, $obtenerFotos, $comentario, $rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if($resultadoObtenerVehiculo === -1){
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","Ha habido un fallo mostrando el catalogo , por favor intentelo mas tarde", $userAlert);
        $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
        $page = str_replace("##TITLE##","Error Modelos",$page);
        echo($page);
    }
    else{
        $infoVehiculo = file_get_contents("./templates/secciones/infoVehiculo.html");
        $datos = $resultadoObtenerVehiculo->fetch_assoc();
        $infoVehiculo = str_replace("##marca##",$datos["marca"],$infoVehiculo);
        $infoVehiculo = str_replace("##modelo##",$datos["modelo"],$infoVehiculo);
        $infoVehiculo = str_replace("##precio##",$datos["precio"],$infoVehiculo);
        $infoVehiculo = str_replace("##descripcion##",$datos["descripcion"],$infoVehiculo);
        $infoVehiculo = str_replace("##matricula##",$datos["matricula"],$infoVehiculo);
        $infoVehiculo = str_replace("##NombrePropietario##",$datos["nombre"],$infoVehiculo);
        $infoVehiculo = str_replace("##ApellidosPropietario##",$datos["apellidos"],$infoVehiculo);
        $infoVehiculo= str_replace("##fotoCentral##",$datos["foto"],$infoVehiculo);
        $trozos = explode("##fotos##",$infoVehiculo);
        $fotosCarrusel = "";
        while($fotos = $obtenerFotos->fetch_assoc()){
            $imagen= $trozos[1];
            $imagen = str_replace("##imagen##",$fotos["imagen"],$imagen);
            $fotosCarrusel= $fotosCarrusel.$imagen;
        }
        $infoVehiculo = $trozos[0].$fotosCarrusel.$trozos[2];
        if ($rolUsuario === "user" || $rolUsuario === "admin"){
            $introducirComentario = file_get_contents("./templates/formularios/introducirComentario.html");
            $introducirComentario = str_replace("##matricula##",$datos["matricula"],$introducirComentario);
            $introducirComentario = str_replace("##oidUsuario##",$_SESSION["id"],$introducirComentario);
        }
        else{
            $introducirComentario = file_get_contents("./templates/buttons/loginComentar.html");
        }
        $cajaComentario = file_get_contents("./templates/secciones/cajaComentarios.html");
        $trozos = explode("##comentario##",$cajaComentario);
        $cjtoComentarios = "";
        while($datos = $comentario->fetch_assoc()){
            $coment= $trozos[1];
            $coment = str_replace("##idUsuario##",$datos["idUsuario"],$coment);
            $coment = str_replace("##comentarioUsuario##",$datos["comentario"],$coment);
            if ($rolUsuario === "admin") {
                $delete = file_get_contents("./templates/buttons/deleteButon.html");
                $delete = str_replace("##idComentario##",$datos["id"],$delete);
                $delete = str_replace("##matricula##",$datos["matricula"],$delete);
                $coment= $coment.$delete;
            }
            $cjtoComentarios = $cjtoComentarios.$coment;
        }

        if ($cjtoComentarios === ""){
            $cjtoComentarios = "<p>No hay comentarios.</p>";
        }
        $cajaComentario = $trozos[0].$introducirComentario.$cjtoComentarios.$trozos[2];
        if ($resultado === 1){
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##","Enhorabuena, tu comentario se ha publicado correctamente", $userAlert);
            $page = $slices[0] .$cabecera.$userAlert .$infoVehiculo.$cajaComentario.$slices[1];
            $page = str_replace("##TITLE##","Informacion",$page);
            echo($page);
        }
        else if ($resultado === 2){
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##","Enhorabuena, has borrado el comentario correctamente", $userAlert);
            $page = $slices[0] .$cabecera.$userAlert .$infoVehiculo.$cajaComentario .$slices[1];
            $page = str_replace("##TITLE##","Informacion",$page);
            echo($page);
        }
        else if($resultado === 0){
            $page = $slices[0] .$cabecera .$infoVehiculo.$cajaComentario .$slices[1];
            $page = str_replace("##TITLE##","Informacion",$page);
            echo($page);
        }
        else{

            $userAlert = file_get_contents("./templates/userAlert/error.html");
            if($resultado === -2){
                $userAlert = str_replace("##mensaje##","Por favor introduce texto antes de enviar el comentario", $userAlert);
            }
            else{
                $userAlert = str_replace("##mensaje##","Ha habido un fallo al publicar el comentario, por favor intentelo mas tarde", $userAlert);

            }

            $page = $slices[0] .$cabecera.$userAlert .$infoVehiculo.$cajaComentario .$slices[1];
            $page = str_replace("##TITLE##","Informacion",$page);
            echo($page);
        }
    }
}
function vmostrarPantallaCompleta($resultadoObtenerVehiculo,$obtenerFotos){
    $page = file_get_contents("./templates/default_template.html");
    $slices = explode("##CONTENT##", $page);
    $pantallaCompleta = file_get_contents("./templates/secciones/pantallaCompleta.html");
    $datos = $resultadoObtenerVehiculo->fetch_assoc();
    $pantallaCompleta = str_replace("##matricula##",$datos["matricula"],$pantallaCompleta);
    $pantallaCompleta= str_replace("##fotoCentral##",$datos["foto"],$pantallaCompleta);
    $trozos = explode("##fotos##",$pantallaCompleta);
    $fotosCarrusel = "";
    while($fotos = $obtenerFotos->fetch_assoc()){
        $imagen= $trozos[1];
        $imagen = str_replace("##imagen##",$fotos["imagen"],$imagen);
        $fotosCarrusel= $fotosCarrusel.$imagen;
    }
    $pantallaCompleta = $trozos[0].$fotosCarrusel.$trozos[2];
    $page = $slices[0].$pantallaCompleta.$slices[1];
    echo($page);

}
function vMostrarServicios($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/secciones/servicios.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Servicios",$page);
    echo($page);
}
function vMostrarInicioSesion($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/formularios/iniciarSesion.html");
    $slices = explode("##CONTENT##", $page);
    $page = $slices[0] .$cabecera .$seccion.$slices[1];
    $page = str_replace("##TITLE##","Iniciar Sesion",$page);
    echo($page);
}
function vMostrarRegistro($rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##", "Registrarse", $page);
    $slices = explode("##CONTENT##", $page);
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/formularios/registrarse.html");
    if ($rolUsuario === "admin") {
        $seccion = str_replace("##registrar##", "./index.php?seccion=6&accion=altaUsuario&id=2", $seccion);
    } else {
        $seccion = str_replace("##registrar##", "./index.php?seccion=5&accion=registrarse&id=2", $seccion);
    }
    $page = $slices[0] . $cabecera . $seccion . $slices[1];
    echo($page);
}
function vMostrarPerfil($resultado,$tipo,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Perfil",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if(!is_object($resultado)){
        echo("Visualizacion de persona". "Se ha producido un error, vuelve a intentarlo mas tarde.");
    }
    else{
        $datos = $resultado -> fetch_assoc();
        if($tipo ==="visualizar"){
            $seccion = file_get_contents("./templates/secciones/verPerfil.html");
        }
        elseif ($tipo === "modificar"){
            $seccion = file_get_contents("./templates/formularios/modificarPerfil.html");
        }
        elseif ($tipo === "modificarPassword"){
            $seccion = file_get_contents("./templates/formularios/modificarPassword.html");
        }
        else{
            $seccion = file_get_contents("./templates/formularios/eliminarPerfil.html");
        }
        $seccion = str_replace("##oidUsuarios##",$datos["id"],$seccion);
        $seccion = str_replace("##idUsuario##", $datos["idUsuario"], $seccion);
        $seccion = str_replace("##nombre##", $datos["nombre"], $seccion);
        $seccion = str_replace("##apellidos##", $datos["apellidos"], $seccion);
        $seccion = str_replace("##correo##", $datos["correo"], $seccion);
        $seccion = str_replace("##fechaNacimiento##", $datos["fechaNacimiento"], $seccion);
        $seccion = str_replace("##telefono##", $datos["telefono"], $seccion);
        $seccion = str_replace("##password##", $datos["contrasena"], $seccion);
        $seccion = str_replace("##rol##",$datos["rol"],$seccion);

        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
}
function vMostrarAdmin($sesionIniciada, $rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Administrador",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if ($sesionIniciada === 1){
        $seccion = file_get_contents("./templates/secciones/admin.html");
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
    else{
        vMostrarHome($rolUsuario);
    }

}
function vMostrarVenderCoche($sesionIniciada,$resultado, $rolUsuario)
{
    if($sesionIniciada === 1){
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Ventas",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
        $seccion = file_get_contents("./templates/formularios/venderCoche.html");
        $datalist = "";
        while( $datos = $resultado -> fetch_assoc()){
            $option =  "<option value=".$datos["marca"]." label=".$datos["marca"]."></option><br>";
            $datalist = $datalist.$option;
        }
        $seccion = str_replace("##datalist##",$datalist,$seccion);
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarReparacion($sesionIniciada, $rolUsuario)
{
    if($sesionIniciada === 1){
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Reparacion",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
        $seccion = file_get_contents("./templates/secciones/repararVehiculo.html");
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);
    }
    else{
        vMostrarHome($rolUsuario);
    }
}
/***********************MostrarSecciones***************************/
function vMostrarResultadoInicioSesion($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $slices = explode("##CONTENT##", $page);
    $cabecera = obtenerCabecera($rolUsuario);
    if($resultado === 1){  //login correcto
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","Ha iniciado sesion correctamente", $userAlert);
    }
    else{
        $seccion = file_get_contents("./templates/formularios/iniciarSesion.html");
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        if($resultado == -1) {  //login incorrecto
            $userAlert = str_replace("##mensaje##","Parametros incorrectos", $userAlert);
        }
        else if($resultado == -2){//fallo en la base de datos
            $userAlert = str_replace("##mensaje##","Fallo en la base de datos", $userAlert);
        }
    }
    $page = $slices[0] .$cabecera .$userAlert.$seccion.$slices[1];
    echo($page);
}
function vMostrarResultadoRegistro($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $slices = explode("##CONTENT##", $page);
    $cabecera = obtenerCabecera($rolUsuario);
    if ($resultado == 1) {  //registro correcto;
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents( "./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Se ha registrado correctamente, inicie sesion para continuar",$userAlert);
    } else {
        $seccion = file_get_contents("./templates/formularios/registrarse.html");
        $userAlert = file_get_contents( "./templates/userAlert/error.html");
        if ($resultado == -1) {  //usuario Repetido
            $userAlert = str_replace("##mensaje##", "El usuario con el que se intenta registrar ya existe, por favor utilice otro nombre.",$userAlert);
        } else if ($resultado == -2) {//fallo en la base de datos
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo con la base de datos, por favor intentelo mas tarde.", $userAlert);
        } else {//parametros incorrectos
            $userAlert = str_replace("##mensaje##", "Parametros introducidos incorrectos", $userAlert);
        }

    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
/***********************Acciones Login**************************/
function vMostrarResultadoModificarPerfil($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    if($resultado === 1){
        $seccion = file_get_contents("./templates/secciones/home.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","Se han modificado los datos correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","ha habido un error modificando los datos, intentelo mas tarde",$userAlert);
        $seccion = file_get_contents("./templates/secciones/home.html");
    }
    $page = $slices[0] .$cabecera.$userAlert.$seccion.$slices[1];
    echo $page;
}
function vMostrarResultadoEliminarPerfil($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/home.html");
    if($resultado === 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##","se ha eliminado correctamente el perfil",$userAlert);


    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##","No se ha podido eliminar el perfil , por favor intentelo mas tarde",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
/***********************Acciones Editar perfil**************************/
function vMostrarAltaPersona($sesionIniciada, $rolUsuario)
{
    if ($sesionIniciada === 1 ){
        vMostrarRegistro($rolUsuario);

    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoAltaPersona($sesionIniciada, $resultado, $rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $cabecera = obtenerCabecera($rolUsuario);
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $slices = explode("##CONTENT##", $page);
    if($sesionIniciada === 1){
        if ($resultado == 1){  //registro correcto;
            $seccion = file_get_contents("./templates/secciones/admin.html");
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##", "Ha dado de alta al usuario correctamente", $userAlert);
        }
        else{
            $seccion = file_get_contents("./templates/formularios/registrarse.html");
            $userAlert = file_get_contents("./templates/userAlert/error.html");
            if ($resultado == -1) {  //usuario Repetido
                $userAlert = str_replace("##mensaje##", "El usuario que intenta dar de alta ya existe, por favor utilice otro nombre.", $userAlert);
            } else if ($resultado == -2) {//fallo en la base de datos
                $userAlert = str_replace("##mensaje##", "Ha habido un fallo con la base de datos, por favor intentelo mas tarde.", $userAlert);
            } else {//parametros incorrectos
                $userAlert = str_replace("##mensaje##", "Parametros introducidos incorrectos", $userAlert);
            }

        }
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
function vMostrarSeleccionUsuario($sesionIniciada,$rolUsuario)
{
    if ($sesionIniciada  === 1){
        if ($rolUsuario === "admin") {
            $page = file_get_contents("./templates/default_template.html");
            $cabecera = obtenerCabecera($rolUsuario);
            $page = str_replace("##TITLE##","Administrador",$page);
            $slices = explode("##CONTENT##", $page);
            $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
            $page = $slices[0] .$cabecera .$seccion.$slices[1];
            echo($page);
        }
        else{
            vMostrarHome($rolUsuario);
        }
    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoSeleccionUsuario($sesionIniciada,$resultado,$rolUsuario)
{
    if ($sesionIniciada  === 1){
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Wild Motors",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
           if($resultado === -3) {
               $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
               $error = file_get_contents("./templates/userAlert/error.html");
               $error = str_replace("##mensaje##", "ha Habido un fallo en la consulta",$error);
               $page = $slices[0] .$cabecera.$error .$seccion.$slices[1];
               echo($page);
           }
           elseif($resultado === -2){
                $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
                $error = file_get_contents("./templates/userAlert/error.html");
                $error = str_replace("##mensaje##", "El usuario existe pero es administrador",$error);
                $page = $slices[0] .$cabecera.$error .$seccion.$slices[1];
                echo($page);}
           elseif ($resultado === -1){
               $seccion = file_get_contents("./templates/formularios/editarUsuario.html");
               $error = file_get_contents("./templates/userAlert/error.html");
               $error = str_replace("##mensaje##", "no existe el usuario introducido",$error);
               $page = $slices[0] .$cabecera.$error.$seccion.$slices[1];
               echo($page);

           }
           else{
               $seccion = file_get_contents("./templates/enlaces/resultadoEditarUsuario.html");
               $seccion = str_replace("##usuario##",$resultado->idUsuario,$seccion);
               $seccion = str_replace("##oidUsuario##",$resultado->id,$seccion);
               $page = $slices[0] .$cabecera .$seccion.$slices[1];
               echo($page);
           }
        }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoNuevoAdministrador($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/admin.html");
    if ($resultado == 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Enhorabuena, has actualizado el rol del usuario correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##", "No se ha podido actualizar el rol del usuario",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert.$seccion.$slices[1];
    echo($page);
}
function vMostrarResultadoBorrarAdministrador($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wild Motors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);
    $seccion = file_get_contents("./templates/secciones/admin.html");
    if ($resultado == 1){
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Enhorabuena, has eliminado el  usuario correctamente",$userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $userAlert = str_replace("##mensaje##", "No se ha podido eliminar el usuario, por favor, intentelo mas tarde",$userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
function vMostrarCargaMasivaUsuarios($sesionIniciada,$rolUsuario)
{
    if ($sesionIniciada  === 1){
        if ($rolUsuario === "admin") {
            $page = file_get_contents("./templates/default_template.html");
            $page = str_replace("##TITLE##","Administrador",$page);
            $cabecera = obtenerCabecera($rolUsuario);
            $slices = explode("##CONTENT##", $page);
            $seccion = file_get_contents("./templates/formularios/cargaMasivaUsuarios.html");
            $page = $slices[0] .$cabecera .$seccion.$slices[1];
            echo($page);
        }
        else{
            vMostrarHome($rolUsuario);
        }
    }
    else{
        vMostrarHome($rolUsuario);
    }

}
function vMostrarResultadoCargaMasivaUsuarios($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wildmotors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);

    if ($resultado == 1) {  //registro correcto;
        $seccion = file_get_contents("./templates/secciones/admin.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Ha realizado con exito la carga de usuarios", $userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        if ($resultado == -1) {  //fallo bbdd csv
            $seccion = file_get_contents("./templates/secciones/admin.html");
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo durante la consulta , por favor revise los datos del csv e intentelo mas tarde.", $userAlert);
        } else if ($resultado == -2) {//fallo obteniendo el csv
            $seccion = file_get_contents("./templates/secciones/admin.html");
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo importando el csv, por favor, intentelo mas tarde .", $userAlert);
        } else {// no es admin
            $seccion = file_get_contents("./templates/secciones/home.html");
            $userAlert = str_replace("##mensaje##", "No tienes permisos para realizar esa accion.", $userAlert);
        }
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
function vMostrarSubirModeloCoche($sesionIniciada,$resultado, $rolUsuario)
{
    if ($sesionIniciada  === 1){
        if ($rolUsuario === "admin") {
            $page = file_get_contents("./templates/default_template.html");
            $seccion = file_get_contents("./templates/formularios/subirVehiculo.html");
            $page = str_replace("##TITLE##","Administrador",$page);
            $cabecera = obtenerCabecera($rolUsuario);
            $datalist = "";
            while( $datos = $resultado -> fetch_assoc()){
                $option =  "<option value=".$datos["marca"]." label=".$datos["marca"]."></option><br>";
                $datalist = $datalist.$option;
            }
            $seccion = str_replace("##datalist##",$datalist,$seccion);


            $slices = explode("##CONTENT##", $page);
            $page = $slices[0] .$cabecera .$seccion.$slices[1];
            echo($page);
        }
        else{
            vMostrarHome($rolUsuario);
        }
    }
    else{
        vMostrarHome($rolUsuario);
    }

}
function vMostrarResultadoSubirModeloCoche($resultado,$rolUsuario)
{
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Wildmotors",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $slices = explode("##CONTENT##", $page);

    if ($resultado === 1) {  // correcto;
        $seccion = file_get_contents("./templates/secciones/admin.html");
        $userAlert = file_get_contents("./templates/userAlert/succes.html");
        $userAlert = str_replace("##mensaje##", "Ha subido el modelo del coche con exito", $userAlert);
    }
    else{
        $userAlert = file_get_contents("./templates/userAlert/error.html");
        $seccion = file_get_contents("./templates/secciones/admin.html");
        $userAlert = str_replace("##mensaje##", "Ha habido un fallo durante la subida , por favor revise los datos introducidos e intentelo mas tarde.", $userAlert);
    }
    $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
    echo($page);
}
function vMostrarDropzone($resultado,$rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    if ($resultado===1 && $rolUsuario === "admin"){
        $seccion = file_get_contents("./templates/enlaces/dropzone.html");
        $seccion = str_replace("##matricula##",$resultado,$seccion);
        $page = str_replace("##TITLE##","Administrador",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
        $page = $slices[0] .$cabecera .$seccion.$slices[1];
        echo($page);

    }
    else{
        echo("voy aqui");
        vMostrarHome($rolUsuario);
    }


}
function vMostrarListadoPersonas($resultado, $sesionIniciada, $rolUsuario){

    if($sesionIniciada === 1){
        $page = file_get_contents(("./templates/default_template.html"));
        $page = str_replace("##TITLE##","Administrador", $page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);
        if(!is_object($resultado)){
            echo("Visualizacion de la lista de personas". "Se ha producido un error, vuelve a intentarlo mas tarde.");
        }
        else{
            $seccion = file_get_contents("./templates/secciones/listaPersonas.html");
            $trozos = explode("##fila##", $seccion);
            $cuerpo = "";
            while($datos = $resultado ->fetch_assoc()){
                $aux = $trozos[1];
                $aux = str_replace("##oidUsuarios##",$datos["id"],$aux);
                $aux = str_replace("##idUsuario##", $datos["idUsuario"], $aux);
                $aux = str_replace("##nombre##", $datos["nombre"], $aux);
                $aux = str_replace("##apellidos##", $datos["apellidos"], $aux);
                $aux = str_replace("##correo##", $datos["correo"], $aux);
                $aux = str_replace("##fechaNacimiento##", $datos["fechaNacimiento"], $aux);
                $aux = str_replace("##telefono##", $datos["telefono"], $aux);
                $aux = str_replace("##password##", $datos["contrasena"], $aux);
                $aux = str_replace("##rol##",$datos["rol"],$aux);
                $cuerpo .= $aux;
            }
            $contenido = $trozos[0] . $cuerpo . $trozos[2];
            $page = $slices[0] .$cabecera .$contenido .$slices[1];
            echo($page);
        }
    }
    else{
        vMostrarHome($rolUsuario);
    }
}
function vMostrarResultadoVenderCoche($resultadoMail,$rolUsuario){
    {
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Wildmotors",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);

        if ($resultadoMail == 1) {  // correcto;
            $seccion = file_get_contents("./templates/secciones/home.html");
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##", "Se ha enviado la solicitud de venta.<br>Te hemos enviado una copia, si no aparece  revisa la bandeja spam del correo vinculado a tu cuenta.<br> En en breves recibira una respuesta para anunciar su coche en nuestra web", $userAlert);
        }
        else{
            $userAlert = file_get_contents("./templates/userAlert/error.html");
            $seccion = file_get_contents("./templates/secciones/home.html");
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo durante la subida , por favor revise los datos introducidos e intentelo mas tarde.", $userAlert);
        }
        $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
        echo($page);
    }

}
function vMostrarResultadoCompraVehiculo($resultadoMail,$rolUsuario){
    {
        $page = file_get_contents("./templates/default_template.html");
        $page = str_replace("##TITLE##","Wildmotors",$page);
        $cabecera = obtenerCabecera($rolUsuario);
        $slices = explode("##CONTENT##", $page);

        if ($resultadoMail == 1) {  // correcto;
            $seccion = file_get_contents("./templates/secciones/home.html");
            $userAlert = file_get_contents("./templates/userAlert/succes.html");
            $userAlert = str_replace("##mensaje##", "Se ha enviado la solicitud de compra. <br> Te hemos enviado una copia, si no aparece  revisa la bandeja spam del correo vinculado a tu cuenta.", $userAlert);
        }
        else{
            $userAlert = file_get_contents("./templates/userAlert/error.html");
            $seccion = file_get_contents("./templates/secciones/home.html");
            $userAlert = str_replace("##mensaje##", "Ha habido un fallo durante la solicitud, por favor intentelo mas tarde.", $userAlert);
        }
        $page = $slices[0] .$cabecera.$userAlert .$seccion.$slices[1];
        echo($page);
    }


}
function vMostrarRegistroCompraVehiculo($rolUsuario){
    $page = file_get_contents("./templates/default_template.html");
    $page = str_replace("##TITLE##","Iniciar Sesion",$page);
    $cabecera = obtenerCabecera($rolUsuario);
    $seccion = file_get_contents("./templates/formularios/iniciarSesion.html");
    $slices = explode("##CONTENT##", $page);;
    $userAlert = file_get_contents("./templates/userAlert/error.html");
    $userAlert = str_replace("##mensaje##", "Para comprar un vehiculo debes iniciar sesion o registrarte,realice este paso e intentelo mas tarde.", $userAlert);
    $page = $slices[0] . $cabecera .$userAlert. $seccion . $slices[1];
    echo($page);
}
/***********************Acciones Administrador**************************/