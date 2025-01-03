$(document).ready(async function (params) {
    let datosUsu = {};
    let usu_id;

    async function obtenerSolicitud(params) {
        return new Promise((resolve, reject)=>{
            $.ajax({
                url : "../ajax/usuario.php?op=estadoSolicitud",
                type: "POST",
                success: function(response){
                    datosUsu = JSON.parse(response);
                    usu_id = datosUsu.usu_id;
                    console.log("Datos del usuario obtenidos correctamente: ", datosUsu);
                    resolve();
                },
                error: function(){
                    console.error("Error al obtener los datos del usuario.");
                    reject("Error al obtener datos del usuario.");
                }
                
            });
        });
    }
    try{
        await obtenerSolicitud();
    }catch(error){
        console.error(error);
        return;
    }
});