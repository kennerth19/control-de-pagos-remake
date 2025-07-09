function selected_in(div) {
    let adorno = document.getElementById("adorno_" + div);

    adorno.style.width = "90%";
}

function selected_out(div) {
    let adorno = document.getElementById("adorno_" + div);

    adorno.style.width = "0";
}

function confirmar_eliminar_in() {
    confirmar.style.display = "block";
}

function confirmar_eliminar_out() {
    confirmar.style.display = "none";
}

function eliminar_foto(id) {
    fetch(`/control_de_pago_remake/public/administrar/eliminar_perfil/${id}`).then(function (response) {
        Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Foto de perfil eliminada",
            showConfirmButton: false,
            timer: 1500,
        });

        setTimeout(function () {
            window.location.reload();
        }, 1500);
    });
}

function cambiar_eliminar(id) {
    let confirmar = document.getElementById("confirmar");
    let boton_eliminar_foto = document.getElementById("boton_eliminar_foto");

    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: ` <div class="">
                    <h1>Configurar foto de perfil <i class="fa-solid fa-trash-can" id="boton_eliminar_foto" onclick="confirmar_eliminar_in()" title="Eliminar foto" style="cursor: pointer;"></i></h1>
                        <form action="/control_de_pago_remake/public/administrar/cambiar_perfil" method="POST" enctype="multipart/form-data" class="formulario_perfil">
                        <input type="hidden" name="id" value="${id}">
                        <input type="file" class="input_file" name="foto" accept="image/*" required>
                            <input type="submit" class="boton_pago cambiar_perfil_b" value="Cambiar perfil">
                        </form>

                    <div id="confirmar" style="display: none;">
                        <p>¿Confirmar eliminación?</p>
                        <button class="eliminar_cliente" onclick="eliminar_foto(${id})">Si</button>
                        <button class="boton_pago cambiar_perfil_b" onclick="confirmar_eliminar_out()">No</button>
                    </div>

                </div>`,
    });
}

async function enviarActualizarUsuario() {
    try {
        await fetch(`/control_de_pago_remake/public/administrar/getUsers/0`)
            .then((response) => response.json())
            .then(async function (data) {
                //quitamos el mensaje si ya existe
                document.getElementById("container_msg").style.display = "none";

                //Obtenemos el botón de envió y lo desactivamos
                let btonEnvio = document.getElementById("enviar_usuario");
                btonEnvio.disabled = true;

                //obtener datos del formulario y sanitizar.

                const firstNameValue = document.getElementById("firstName").value.trim();
                const passValue = document.getElementById("pass").value.trim();
                const userId = document.getElementById("userId").value;

                // Validar formato de caracteres extraños
                const ipRegex = /^[a-zA-Z0-9ñÑ]+$/;
                if (!ipRegex.test(firstNameValue)) {
                    mostrarMensaje("No se permiten caracteres extraños", "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //validar si el nombre existe
                const existe = data.some((usuario) => usuario.name.toLowerCase() === firstNameValue);

                if (existe) {
                    mostrarMensaje(`El usuario ${firstNameValue} ya esta en uso`, "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //proceso de validación de datos.
                if (!firstNameValue) {
                    mostrarMensaje("El campo 'Nuevo nombre de usuario' no debe quedar vació", "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //petición PUT para el envió de los nuevos datos.
                const response = await fetch(`/control_de_pago_remake/public/administrar/actualizar_usuarios/${userId}`, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        nombre: firstNameValue.toLowerCase(),
                        pass: passValue,
                    }),
                });

                //si falla la petición lanzamos mensaje de error.
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                //capturamos la respuesta y parseamos en un JSON.
                const result = await response.json();

                setTimeout(function () {
                    Swal.close();
                }, 2000);

                //Refrescamos tabla para mostrar cambios, mostramos mensaje de éxito, deshabilitamos botón de envió y editamos el mensaje de bienvenida con el nuevo nombre.
                document.getElementById("username").innerText = firstNameValue.toLowerCase();
                tabla_usuarios();
                mostrarMensaje("Usuario actualizado correctamente!", "exito");
                btonEnvio.disabled = true;
            });
    } catch (error) {
        //bloque catch que consiste en mostrar mensaje (de interfaz y por consola) ademas de habilitar el botón de envió.
        mostrarMensaje("Error al actualizar el usuario!", "error");
        let btonEnvio = document.getElementById("enviar_usuario");
        btonEnvio.disabled = false;
        console.error(error);
    }
}

function editar_usuario(id) {
    const username = document.getElementById("username").innerText;

    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: ` <div class="">
                    <h1>Configurar usuario</h1>
                    <div id="formUser" class="formulario_perfil">
                        <input type="hidden" name="id" value="${id}" id="userId">

                        <input type="text" class="input_file" name="name" value="${username}" id="firstName" autocomplete="given-name" pattern="[A-Za-z][A-Za-z0-9]{3,}" placeholder="Nuevo nombre de usuario" title="Ingrese al menos 4 caracteres, comenzando con una letra. Solo se permiten letras y números." required>
                        <input type="password" class="input_file" name="password" id="pass" pattern="[A-Za-z0-9]{4,}" placeholder="Nueva contraseña" title="Solo letras y números, mínimo 4 caracteres" required>
                        <input type="submit" class="boton_pago cambiar_perfil_b" id="enviar_usuario" cambiar_perfil_b" value="Actualizar usuario" onclick="enviarActualizarUsuario()">

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Usuario actualizado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

async function enviarCrearUsuario() {
    try {
        await fetch(`/control_de_pago_remake/public/administrar/getUsers/1`)
            .then((response) => response.json())
            .then(async function (data) {
                //quitamos el mensaje si ya existe
                document.getElementById("container_msg").style.display = "none";

                //Obtenemos el botón de envió y lo desactivamos
                let btonEnvio = document.getElementById("enviar_usuario");
                btonEnvio.disabled = true;

                //obtener datos del formulario y sanitizar.

                const firstNameValue = document.getElementById("firstName").value.trim();
                const passValue = document.getElementById("pass").value.trim();

                // Validar formato de caracteres extraños
                const ipRegex = /^[a-zA-Z0-9ñÑ]+$/;
                if (!ipRegex.test(firstNameValue)) {
                    mostrarMensaje("No se permiten caracteres extraños", "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //validar si el nombre existe
                const existe = data.some((usuario) => usuario.name.toLowerCase() === firstNameValue);

                if (existe) {
                    mostrarMensaje(`El usuario ${firstNameValue} ya esta en uso`, "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //proceso de validación de datos.
                if (!firstNameValue) {
                    mostrarMensaje("El campo 'Nombre de usuario' no debe quedar vació", "advertencia");
                    btonEnvio.disabled = false;
                    return;
                }

                //petición PUT para el envió de los nuevos datos.
                const response = await fetch(`/control_de_pago_remake/public/administrar/crear_usuarios`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({
                        nombre: firstNameValue.toLowerCase(),
                        pass: passValue,
                    }),
                });

                //si falla la petición lanzamos mensaje de error.
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                //capturamos la respuesta y parseamos en un JSON.
                const result = await response.json();

                setTimeout(function () {
                    Swal.close();
                }, 2000);

                //Refrescamos tabla para mostrar cambios, mostramos mensaje de éxito, deshabilitamos botón de envió y editamos el mensaje de bienvenida con el nuevo nombre.
                document.getElementById("username").innerText = firstNameValue.toLowerCase();
                tabla_usuarios();
                mostrarMensaje("Usuario creado correctamente!", "exito");
                btonEnvio.disabled = true;
            });
    } catch (error) {
        //bloque catch que consiste en mostrar mensaje (de interfaz y por consola) ademas de habilitar el botón de envió.
        mostrarMensaje("Error al actualizar el usuario!", "error");
        let btonEnvio = document.getElementById("enviar_usuario");
        btonEnvio.disabled = false;
        console.error(error);
    }
}

function crear_usuario() {
    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: ` <div class="">
                    <h1>Creacion de usuario</h1>
                    <div id="formUser" class="formulario_perfil">

                        <input type="text" class="input_file" name="name" value="" id="firstName" autocomplete="given-name" pattern="[A-Za-z][A-Za-z0-9]{3,}" placeholder="Nombre de usuario" title="Ingrese al menos 4 caracteres, comenzando con una letra. Solo se permiten letras y números." required>
                        <input type="password" class="input_file" name="password" id="pass" pattern="[A-Za-z0-9]{4,}" placeholder="Contraseña" title="Solo letras y números, mínimo 4 caracteres" required>
                        <input type="submit" class="boton_pago cambiar_perfil_b" id="enviar_usuario" cambiar_perfil_b" value="Crear usuario" onclick="enviarCrearUsuario()">

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Usuario creado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

async function crear_servidor_enviar() {
    try {
        const servidorInput = document.getElementById("servidor_value_crear");
        const ipInput = document.getElementById("ip_value_crear");

        const servidor = servidorInput.value.trim();
        const ip = ipInput.value.trim();

        // Validar que plan no esté vacío
        if (servidor === "") {
            mostrarMensaje("Debes llenar el campo 'Nombre de servidor'!", "advertencia");
            return;
        }

        // Validar formato IP
        const ipRegex = /^(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)){3}$/;
        if (!ipRegex.test(ip)) {
            mostrarMensaje("La dirección IP no es válida!", "advertencia");
            return;
        }

        const response = await fetch("/control_de_pago_remake/public/administrar/crear_servidor", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                servidor: servidor,
                ip: ip,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response}`);
        }

        const result = await response.json();

        tabla_servidores();
        mostrarMensaje("Servidor creado correctamente!", "exito");
        document.getElementById("enviar_servidor").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al crear el servidor!", "error");
        console.error(error);
    }
}

async function enviar_plan() {
    try {
        const planInput = document.getElementById("plan_value");
        const valorInput = document.getElementById("valor_value");
        const idInput = document.getElementById("id_value_plan_editar");
        const tipoInput = document.getElementById("tipo_value");

        const plan = planInput.value.trim();
        const valorStr = valorInput.value.trim();
        const valor = Number(valorStr);
        const id = idInput.value.trim();
        const tipo = tipoInput.value;

        // Validaciones
        if (plan === "") {
            mostrarMensaje("El campo 'plan' no puede estar vacío.", "advertencia");
            return;
        }

        if (!Number.isInteger(valor) || valor <= 0) {
            mostrarMensaje("El campo 'valor' debe ser un número entero positivo.", "advertencia");
            return;
        }

        if (id === "") {
            mostrarMensaje("ID inválido o vacío.", "advertencia");
            return;
        }

        // Si pasa validaciones, enviar datos
        const response = await fetch("/control_de_pago_remake/public/administrar/editar_plan", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id: id,
                plan: plan,
                valor: valor,
                tipo: tipo,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        tabla_planes();
        mostrarMensaje("Plan actualizado correctamente!", "exito");
        document.getElementById("enviar_plan").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al actualizar el plan!", "error");
        console.error(error);
    }
}

async function crear_plan_enviar() {
    try {
        const planInput = document.getElementById("plan_value_crear");
        const valorInput = document.getElementById("valor_value_crear");
        const tipoInput = document.getElementById("tipo_value_crear");

        const plan = planInput.value.trim();
        const valorStr = valorInput.value.trim();
        const tipo = tipoInput.value;

        // Validar que plan no esté vacío
        if (plan === "") {
            mostrarMensaje("Debes llenar el campo 'plan'!", "advertencia");
            return;
        }

        // Validar que valor sea un número entero positivo
        const valor = Number(valorStr);
        if (!Number.isInteger(valor) || valor <= 0) {
            mostrarMensaje("El campo 'valor' debe ser un número entero positivo!", "advertencia");
            return;
        }

        const response = await fetch("/control_de_pago_remake/public/administrar/crear_plan", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                plan: plan,
                valor: valor,
                tipo: tipo,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP() error! status: ${response.status}`);
        }

        const result = await response.json();

        tabla_planes();
        mostrarMensaje("Plan creado correctamente!", "exito");
        document.getElementById("enviar_plan").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al crear el plan!", "error");
        console.error(error);
    }
}

async function enviar_servidor() {
    try {
        const id_value = document.getElementById("id_value_servidor").value;
        const nombre_de_servidor_value = document.getElementById("nombre_de_servidor_value").value;
        const ip_value = document.getElementById("ip_value").value;
        const puerto_value = document.getElementById("puerto_value").value;
        const active_value = document.getElementById("active_value").value;

        const nombre = nombre_de_servidor_value.trim();
        //const tipo = tipoInput.value;

        // Validar que servidor no esté vacío
        if (nombre === "") {
            mostrarMensaje("Debes llenar el campo 'nombre'!", "advertencia");
            return;
        }

        // Validar formato IP
        const ipRegex = /^(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d{2}|[1-9]?\d)){3}$/;
        if (!ipRegex.test(ip_value)) {
            mostrarMensaje("La dirección IP no es válida!", "advertencia");
            return;
        }

        // Validar que el puerto sea un número entero positivo
        const valor = Number(puerto_value);
        if (!Number.isInteger(valor) || valor <= 0) {
            mostrarMensaje("El campo 'puerto' debe ser un número entero positivo!", "advertencia");
            return;
        }

        const response = await fetch("/control_de_pago_remake/public/administrar/editar_servidor", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id: id_value,
                nombre_de_servidor: nombre,
                ip: ip_value,
                puerto: puerto_value,
                active: active_value,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        tabla_servidores();
        mostrarMensaje("Servidor editado correctamente!", "exito");
        document.getElementById("enviar_servidor").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al editar el servidor!", "error");
        console.error(error.stack);
    }
}

async function crear_instalacion_enviar() {
    try {
        const instalacionInput = document.getElementById("instalacion_value_crear");
        const valorInput = document.getElementById("valor_value_crear");
        const categoriaInput = document.getElementById("categoria_value_crear");

        const instalacion = instalacionInput.value.trim();
        const valorStr = valorInput.value.trim();
        const categoria = categoriaInput.value;

        // Validar que plan no esté vacío
        if (instalacion === "") {
            mostrarMensaje("Debes llenar el campo 'instalación'!", "advertencia");
            return;
        }

        // Validar que valor sea un número entero positivo
        const valor = Number(valorStr);
        if (!Number.isInteger(valor) || valor <= 0) {
            mostrarMensaje("El campo 'valor' debe ser un número entero positivo!", "advertencia");
            return;
        }

        const response = await fetch("/control_de_pago_remake/public/administrar/crear_instalacion", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                instalacion: instalacion,
                valor: valor,
                categoria: categoria,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP() error! status: ${response.status}`);
        }

        const result = await response.json();

        tabla_instalaciones();
        mostrarMensaje("Instalación creada correctamente!", "exito");
        document.getElementById("enviar_instalacion").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al crear la instalación!", "error");
        console.error(error);
    }
}

// Función auxiliar para mostrar mensajes con iconos y estilos
function mostrarMensaje(texto, tipo) {
    const container = document.getElementById("container_msg");
    const mensaje = document.getElementById("mensaje_fetch");
    const img = document.getElementById("msg_img");

    container.style.display = "grid";
    mensaje.innerText = texto;

    switch (tipo) {
        case "exito":
            img.src = "/control_de_pago_remake/public/img/configuracion/enviado.png";
            setTimeout(() => {
                Swal.close();
            }, 3000);
            break;
        case "advertencia":
            img.src = "/control_de_pago_remake/public/img/configuracion/advertencia.png";
            break;
        case "error":
        default:
            img.src = "/control_de_pago_remake/public/img/configuracion/error.png";
            break;
    }
}

async function crear_servidor() {
    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `<div class="">
                    <h1>Creación de servidor</h1>
                    <div class="formulario_perfil">

                        <input type="text" class="input_file" name="servidor" id="servidor_value_crear" placeholder="Nombre del servidor" required>
                        <input type="text" class="input_file" name="ip" id="ip_value_crear" placeholder="Dirección IP" required>

                        <button id="enviar_servidor" class="boton_pago cambiar_perfil_b" onclick="crear_servidor_enviar()">Crear servidor</button>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Servidor creado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

const opcionesArr = [
    { value: 0, label: "Inalámbrico" },
    { value: 1, label: "Fibra" },
    { value: 2, label: "Empresarial" },
    { value: 3, label: "Especial" },
    { value: 4, label: "Noria" },
];

const opcionesActive = [
    { value: 0, label: "Inactivo" },
    { value: 1, label: "Activo" },
];

async function crear_plan() {
    let opciones = opcionesArr.map((op) => `<option value="${op.value}">${op.label}</option>`);

    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `<div class="">
                    <h1>Creación de plan</h1>
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value">

                        <input type="text" class="input_file" name="plan" id="plan_value_crear" placeholder="Nombre del plan" required>
                        <input type="number" class="input_file" name="valor" id="valor_value_crear" placeholder="valor" required>

                        <select name="tipo" id="tipo_value_crear" class="input_file">
                            ${opciones}
                        </select>

                        <button id="enviar_plan" class="boton_pago cambiar_perfil_b" onclick="crear_plan_enviar()">Crear plan</button>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Plan creado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

async function editar_plan(id) {
    await fetch(`/control_de_pago_remake/public/administrar/plan/${id}`)
        .then((response) => response.json())
        .then(function (data) {
            let opciones = opcionesArr.map((op) => `<option value="${op.value}"${op.value === data.tipo ? " selected" : ""}>${op.label}</option>`);

            Swal.fire({
                showConfirmButton: false,
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `<div class="">
                    <h1>Edición de plan</h1>
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value_plan_editar" value="${id}">

                        <input type="text" class="input_file" name="plan" id="plan_value" value="${data.plan}" placeholder="Nombre del plan" required>
                        <input type="number" class="input_file" name="valor" id="valor_value" value="${data.valor}" required>

                        <select name="tipo" id="tipo_value" class="input_file">
                            ${opciones}
                        </select>

                        <button id="enviar_plan" class="boton_pago cambiar_perfil_b" onclick="enviar_plan()">Actualizar plan</button>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Plan actualizado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
            });
        });
}

//este
async function enviar_instalacion() {
    try {
        const idInput = document.getElementById("id_value_instalacion_editar"); // ID
        const instalacionInput = document.getElementById("instalacion_value");
        const valorInput = document.getElementById("valor_instalacion_value"); // VALOR
        const activeInput = document.getElementById("instalacion_active_value"); // ACTIVE
        const instalacion_categoria_value = document.getElementById("instalacion_categoria_value");

        const id = idInput.value.trim();
        const valorStr = valorInput.value.trim();
        const valor = Number(valorStr);
        const instalacion = instalacionInput.value.trim();
        const active = activeInput.value;
        const categoria = instalacion_categoria_value.value;

        // Validaciones
        if (instalacion === "") {
            mostrarMensaje("El campo 'instalación' no puede estar vacío.", "advertencia");
            return;
        }

        if (!Number.isInteger(valor) || valor <= 0) {
            mostrarMensaje("El campo 'valor' debe ser un número entero positivo.", "advertencia");
            return;
        }

        if (id === "") {
            mostrarMensaje("ID inválido o vacío.", "advertencia");
            return;
        }

        // Si pasa validaciones, enviar datos
        const response = await fetch("/control_de_pago_remake/public/administrar/editar_instalacion", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                id: id,
                instalacion: instalacion,
                valor: valor,
                categoria: categoria,
                active: active,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        tabla_instalaciones();
        mostrarMensaje("Instalación actualizada correctamente!", "exito");
        document.getElementById("enviar_plan").disabled = true;
    } catch (error) {
        mostrarMensaje("Error al actualizar la instalación!", "error");
        console.error(error);
    }
}

const categoriaOpciones = [
    { value: 0, label: "Sencilla" },
    { value: 1, label: "Intermedia" },
    { value: 2, label: "Avanzada" },
];

async function editar_instalacion(id) {
    await fetch(`/control_de_pago_remake/public/administrar/instalacion/${id}`)
        .then((response) => response.json())
        .then(function (data) {
            let activeOpciones = opcionesActive.map((op) => `<option value="${op.value}"${op.value === data.active ? " selected" : ""}>${op.label}</option>`);
            let opcionesCategoria = categoriaOpciones.map((op) => `<option value="${op.value}"${op.value === data.categoria ? " selected" : ""}>${op.label}</option>`);

            Swal.fire({
                showConfirmButton: false,
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: ` <div class="">
                            <h1>Edición de instalación</h1>
                            <div class="formulario_perfil">
                                <input type="hidden" name="id" id="id_value_instalacion_editar" value="${id}">

                                <input type="text" class="input_file" id="instalacion_value" value="${data.router}" placeholder="Nombre de la instalación" required>
                                <input type="number" class="input_file" placeholder="Valor" id="valor_instalacion_value" value="${data.valor}" required>                        

                                <select name="active" id="instalacion_active_value" class="input_file">
                                    ${activeOpciones}
                                </select>

                                <select name="categoria" id="instalacion_categoria_value" class="input_file" onchange="console.log(this.value)">
                                    ${opcionesCategoria}
                                </select>

                                <button id="enviar_plan" class="boton_pago cambiar_perfil_b" onclick="enviar_instalacion()">Actualizar instalación</button>

                                <div id="container_msg" style="display: none;">
                                    <p id="mensaje_fetch">Instalación actualizada correctamente!</p>
                                    <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                                </div>
                            </div>
                        </div>`,
            });
        });
}

const opcionesCat = [
    { value: 0, label: "Sencilla" },
    { value: 1, label: "Intermedia" },
    { value: 2, label: "Avanzada" },
];

async function crear_instalacion() {
    let opciones = opcionesCat.map((op) => `<option value="${op.value}">${op.label}</option>`);

    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `<div class="">
                    <h1>Creación de instalación</h1>
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value">

                        <input type="text" class="input_file" name="instalacion" id="instalacion_value_crear" placeholder="Nombre de la instalación" required>
                        <input type="number" class="input_file" name="valor" id="valor_value_crear" placeholder="valor" required>

                        <select name="categoria" id="categoria_value_crear" class="input_file">
                            ${opciones}
                        </select>

                        <button id="enviar_instalacion" class="boton_pago cambiar_perfil_b" onclick="crear_instalacion_enviar()">Crear instalación</button>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Instalación creada correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

async function editar_servidor(id) {
    await fetch(`/control_de_pago_remake/public/administrar/servidor/${id}`)
        .then((response) => response.json())
        .then(function (data) {
            let opciones = opcionesActive.map((op) => `<option value="${op.value}"${op.value === data.active ? " selected" : ""}>${op.label}</option>`);

            Swal.fire({
                showConfirmButton: false,
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `<div class="">
                    <h1>Edición de servidor</h1>
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value_servidor" value="${id}">

                        <input type="text" class="input_file" name="nombre_de_servidor" id="nombre_de_servidor_value" value="${data.nombre_de_servidor}" placeholder="Nombre del servidor" required>
                        <input type="text" class="input_file" name="ip" id="ip_value" value="${data.ip}" placeholder="Dirección IP" required>
                        <input type="text" class="input_file" name="puerto" id="puerto_value" value="${data.puerto}" placeholder="Puerto" required>

                        <select name="active" id="active_value" class="input_file">
                            ${opciones}
                        </select>

                        <button id="enviar_servidor" class="boton_pago cambiar_perfil_b" onclick="enviar_servidor()">Actualizar Servidor</button>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Servidor actualizado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
            });
        });
}

async function enviarEliminarPlan(id, op) {
    try {
        const container_load = document.getElementById("container_load");
        const id_value_plan = document.getElementById("id_value_plan").value;

        container_load.style.display = "none";
        container_load.style.display = "grid";

        if (!id) {
            Swal.fire("Error", "ID inválido para eliminar el plan", "error");
            return;
        }

        let plan_seleccion;

        if (op == 1) {
            // Significa que no hay que reasignar plan.
            plan_seleccion = 0;
        } else {
            // reasignar plan
            plan_seleccion = document.getElementById("plan_seleccion");

            if (!plan_seleccion.value) {
                document.getElementById("container_msg_borrar").style.display = "grid";
                document.getElementById("mensaje_fetch").innerText = "Debes seleccionar algún plan";
                document.getElementById("msg_img").src = "/control_de_pago_remake/public/img/configuracion/advertencia.png";
                container_load.style.display = "none";
                return;
            }
        }

        let body = JSON.stringify({
            id: id_value_plan,
            nuevo_plan: plan_seleccion.value,
            op: op,
        });

        const response = await fetch(`/control_de_pago_remake/public/administrar/eliminar_plan`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: body,
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }

        container_load.style.display = "none";

        document.getElementById("container_msg_borrar").style.display = "grid";
        document.getElementById("mensaje_fetch").innerText = "Plan eliminado con exito!";
        document.getElementById("msg_img").src = "/control_de_pago_remake/public/img/configuracion/enviado.png";
        document.getElementById("enviar_plan").disabled = true;

        setTimeout(function () {
            Swal.close();
        }, 2000);

        tabla_planes();
    } catch (error) {
        container_load.style.display = "none";
        console.error("Error al eliminar el elemento:", error);
        Swal.fire({
            icon: "error",
            title: "Error al eliminar",
            text: error.message,
        });
    }
}

async function eliminar_plan(id) {
    await fetch(`/control_de_pago_remake/public/administrar/retornarCantidad/${id}`)
        .then((response) => response.json())
        .then(function (data) {
            let opciones = `<select name="plan" id="plan_seleccion" class="input_file"><option value="">Selecciona un plan</option>`;
            let mensaje = "";
            let op = 0;

            opciones += data[1].map((op) => `<option value="${op.id}">${op.plan}</option>`);
            opciones += `</select>`;

            mensaje += `<div class="advertenciaEliminar">`;

            if (data[0]["cantidad"] == 0) {
                opciones = "";
                mensaje += `Ningún cliente posee este plan<br>(puedes eliminarlo directamente)`;
                op = 1;
            } else if (data[0]["cantidad"] == 1) {
                mensaje += `<p>CUIDADO! <b>UN</b> cliente posee este plan!</p><p>Antes debes seleccionar uno de esta lista</p>`;
            } else {
                mensaje += `<p>CUIDADO! <b>${data[0]["cantidad"]}</b> clientes poseen este plan!</p><p>Antes debes seleccionar uno de esta lista</p>`;
            }

            mensaje += "</div>";

            Swal.fire({
                showConfirmButton: false,
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `<div class="">
                    <h1>Eliminar plan</h1>
                    ${mensaje}
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value_plan" value="${id}">
                        ${opciones}
                        <button id="enviar_plan" class="boton_pago cambiar_perfil_b" onclick="enviarEliminarPlan(${id}, ${op})">Eliminar plan</button>

                        <div id="container_load" style="display: none;">
                            <img src="/control_de_pago_remake/public/img/configuracion/cargando.png" id="cargando">
                        </div>

                        <div id="container_msg_borrar" style="display: none;">
                            <p id="mensaje_fetch">Plan eliminado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
            });
        });
}

async function enviarEliminarServidor(id, op) {
    try {
        const container_load = document.getElementById("container_load");
        const id_value_server = document.getElementById("id_value_server").value;

        container_load.style.display = "none";
        container_load.style.display = "grid";

        if (!id) {
            Swal.fire("Error", "ID inválido para eliminar el plan", "error");
            return;
        }

        let servidor_seleccion;

        if (op == 1) {
            // Significa que no hay que reasignar servidor.
            servidor_seleccion = 0;
        } else {
            // reasignar servidor
            servidor_seleccion = document.getElementById("servidor_seleccion");

            if (!servidor_seleccion.value) {
                document.getElementById("container_msg_borrar").style.display = "grid";
                document.getElementById("mensaje_fetch").innerText = "Debes seleccionar algún servidor";
                document.getElementById("msg_img").src = "/control_de_pago_remake/public/img/configuracion/advertencia.png";
                container_load.style.display = "none";
                return;
            }
        }

        let body = JSON.stringify({
            id: id_value_server,
            nuevo_servidor: servidor_seleccion.value,
            op: op,
        });

        const response = await fetch(`/control_de_pago_remake/public/administrar/eliminar_servidor`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: body,
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
        }

        container_load.style.display = "none";

        document.getElementById("container_msg_borrar").style.display = "grid";
        document.getElementById("mensaje_fetch").innerText = "Servidor eliminado con éxito!";
        document.getElementById("msg_img").src = "/control_de_pago_remake/public/img/configuracion/enviado.png";
        document.getElementById("enviar_servidor").disabled = true;

        setTimeout(function () {
            Swal.close();
        }, 2000);

        tabla_servidores();
    } catch (error) {
        container_load.style.display = "none";
        console.error("Error al eliminar el elemento:", error);
        Swal.fire({
            icon: "error",
            title: "Error al eliminar",
            text: error.message,
        });
    }
}

async function eliminar_servidor(id) {
    await fetch(`/control_de_pago_remake/public/administrar/retornarCantidadServidor/${id}`)
        .then((response) => response.json())
        .then(function (data) {
            let opciones = `<select name="servidor" id="servidor_seleccion" class="input_file"><option value="">Selecciona un servidor</option>`;
            let mensaje = "";
            let op = 0;

            opciones += data["servidores"].map((op) => `<option value="${op.id}">${op.nombre_de_servidor}</option>`);
            opciones += `</select>`;

            mensaje += `<div class="advertenciaEliminar">`;

            if (data["cantidad"] == 0) {
                opciones = "";
                mensaje += `Ningún cliente esta en este servidor<br>(puedes eliminarlo directamente)`;
                op = 1;
            } else if (data["cantidad"] == 1) {
                mensaje += `<p>CUIDADO! hay <b>UN</b> cliente en este servidor!</p><p>Antes debes seleccionar uno de esta lista</p>`;
            } else {
                mensaje += `<p>CUIDADO! hay <b>${data["cantidad"]}</b> clientes en este servidor!</p><p>Antes debes seleccionar uno de esta lista</p>`;
            }

            mensaje += "</div>";

            Swal.fire({
                showConfirmButton: false,
                heightAuto: false,
                customClass: {
                    container: "container_modal",
                    htmlContainer: "contenedor_add",
                },
                html: `<div class="">
                    <h1>Eliminar servidor</h1>
                    ${mensaje}
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value_server" value="${id}">
                        ${opciones}
                        <button id="enviar_servidor" class="boton_pago cambiar_perfil_b" onclick="enviarEliminarServidor(${id}, ${op})">Eliminar servidor</button>

                        <div id="container_load" style="display: none;">
                            <img src="/control_de_pago_remake/public/img/configuracion/cargando.png" id="cargando">
                        </div>

                        <div id="container_msg_borrar" style="display: none;">
                            <p id="mensaje_fetch">Servidor eliminado correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
            });
        });
}

async function enviarEliminarInstalacion(id) {
    fetch(`/control_de_pago_remake/public/administrar/eliminar_instalacion/${id}`, { method: "DELETE" })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            } else {
                setTimeout(function () {
                    Swal.close();
                }, 2000);

                mostrarMensaje("Instalación eliminada con éxito!", "exito");
                tabla_instalaciones();
            }
        })
        .catch((error) => {
            mostrarMensaje("Error al eliminar el elemento", "error");
        });
}

async function eliminar_instalacion(id) {
    Swal.fire({
        showConfirmButton: false,
        heightAuto: false,
        customClass: {
            container: "container_modal",
            htmlContainer: "contenedor_add",
        },
        html: `<div class="">
                    <h1>Eliminar Instalación</h1>
                    <div class="formulario_perfil">
                        <input type="hidden" name="id" id="id_value_server" value="${id}">
                        <button id="enviar_servidor" class="boton_pago cambiar_perfil_b" onclick="enviarEliminarInstalacion(${id})">Eliminar</button>

                        <div id="container_load" style="display: none;">
                            <img src="/control_de_pago_remake/public/img/configuracion/cargando.png" id="cargando">
                        </div>

                        <div id="container_msg" style="display: none;">
                            <p id="mensaje_fetch">Instalación eliminada correctamente!</p>
                            <img src="/control_de_pago_remake/public/img/configuracion/enviado.png" id="msg_img">
                        </div>
                    </div>
                </div>`,
    });
}

function cambiar(id) {
    let secciones = document.getElementsByClassName("seccion");

    let contenedor = document.getElementById(id);

    for (let i = 0; i <= secciones.length - 1; i++) {
        secciones[i].style.display = "none";
    }

    contenedor.style.display = "block";
}

async function tabla_usuarios() {
    // tabla para mostrar las cuentas.
    try {
        const response = await fetch("/control_de_pago_remake/public/administrar/users")
            .then((response) => response.json())
            .then((data) => {
                table = $("#tabla_usuarios").DataTable({
                    responsive: true,
                    destroy: true,
                    stateSave: true,
                    language: {
                        url: "/control_de_pago_remake/public/js/lenguaje.json",
                    },
                    lengthMenu: [
                        [10, 25, -1],
                        ["10", "25", "Todos"],
                    ],
                    data: data,
                    columns: [
                        { data: "id", className: "columnaId" },
                        { data: "name" },
                        {
                            data: "roles",
                            render: function (data, type, row) {
                                let roles = 0;

                                if (row.roles == 0) {
                                    roles = "Usuario regular";
                                } else if (row.roles == 1) {
                                    roles = "Administrador";
                                }

                                return roles;
                            },
                        },
                        {
                            data: "grupo",
                            render: function (data, type, row) {
                                let grupo = 0;

                                if (row.grupo == 0) {
                                    grupo = "Sin grupo";
                                } else if (row.grupo == 1) {
                                    grupo = "Oficina";
                                } else if (row.grupo == 2) {
                                    grupo = "Caja fuerte";
                                }

                                return grupo;
                            },
                        },
                    ],
                });
            });
    } catch (error) {
        console.log(error);
    }
}

async function tabla_planes() {
    // tabla para mostrar los planes
    try {
        const response = await fetch("/control_de_pago_remake/public/administrar/planes")
            .then((response) => response.json())
            .then((data) => {
                table = $("#tabla_planes").DataTable({
                    responsive: true,
                    destroy: true,
                    stateSave: true,
                    language: { url: "/control_de_pago_remake/public/js/lenguaje.json" },
                    lengthMenu: [
                        [15, 25, -1],
                        ["15", "25", "Todos"],
                    ],
                    data: data,
                    columns: [
                        { data: "id", className: "columnaId" },
                        { data: "plan" },
                        {
                            data: "valor",
                            render: function (data, type, row) {
                                return `${row.valor}$`;
                            },
                        },
                        {
                            data: "tipo",
                            render: function (data, type, row) {
                                let tipo = "";

                                if (row.tipo == 0) {
                                    tipo = "Inalámbricos";
                                } else if (row.tipo == 1) {
                                    tipo = "Fibra";
                                } else if (row.tipo == 2) {
                                    tipo = "Empresarial";
                                } else if (row.tipo == 3) {
                                    tipo = "Especiales";
                                } else if (row.tipo == 4) {
                                    tipo = "Noria";
                                }

                                return tipo;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<div class="opciones">
                                            <button class="button edit" title="Editar plan ${row.plan}" onclick="editar_plan(${row.id})">Editar</button>
                                            <img class="eliminar" src="/control_de_pago_remake/public/img/configuracion/eliminar.png" title="Eliminar plan ${row.plan}" onclick="eliminar_plan(${row.id})">
                                        </div>`;
                            },
                        },
                    ],
                });
            });
    } catch (error) {
        console.log(error);
    }
}

async function tabla_servidores() {
    // tabla para mostrar los servidores
    try {
        const response = await fetch("/control_de_pago_remake/public/administrar/servidores")
            .then((response) => response.json())
            .then((data) => {
                table = $("#tabla_servidores").DataTable({
                    responsive: true,
                    destroy: true,
                    stateSave: true,
                    language: { url: "/control_de_pago_remake/public/js/lenguaje.json" },
                    lengthMenu: [
                        [25, -1],
                        ["25", "Todos"],
                    ],
                    data: data,
                    columnDefs: [{ targets: 0, width: "40px" }],
                    columns: [
                        { data: "id", className: "columnaId" },
                        { data: "nombre_de_servidor" },
                        { data: "ip" },
                        { data: "puerto" },
                        {
                            data: "active",
                            render: function (data, type, row) {
                                let estado = "";

                                if (row.active) {
                                    estado = `<img class="server_status" src="/control_de_pago_remake/public/img/configuracion/encendido.png" title="Estado: Activo">`;
                                } else {
                                    estado = `<img class="server_status" src="/control_de_pago_remake/public/img/configuracion/apagado.png" title="Estado: Inactivo">`;
                                }

                                return estado;
                            },
                        },
                        {
                            data: "id",
                            render: function (data, type, row) {
                                return `<div class="opciones">
                                            <button class="button edit" title="Editar servidor ${row.nombre_de_servidor}" onclick="editar_servidor(${row.id})">Editar</button>
                                            <img class="eliminar" src="/control_de_pago_remake/public/img/configuracion/eliminar.png" title="Eliminar servidor ${row.nombre_de_servidor}" onclick="eliminar_servidor(${row.id})">
                                        </div>`;
                            },
                        },
                    ],
                });
            });
    } catch (error) {
        console.log(error);
    }
}

async function tabla_nodos() {
    try {
        const response = await fetch("/control_de_pago_remake/public/administrar/nodos");

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const tabla_nodos = $("#tabla_nodos").DataTable({
            responsive: true,
            destroy: true,
            stateSave: true,
            language: { url: "/control_de_pago_remake/public/js/lenguaje.json" },
            lengthMenu: [
                [15, 25, -1],
                ["15", "25", "Todos"],
            ],
            data: data,
            columns: [{ data: "id", className: "columnaId" }, { data: "nombre" }, { data: "ip" }, { data: "mac" }],
        });
    } catch (error) {
        console.log("Error al cargar los nodos:", error);
    }
}

async function tabla_instalaciones() {
    try {
        const response = await fetch("/control_de_pago_remake/public/administrar/instalaciones");

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        const tabla_instalaciones = $("#tabla_instalaciones").DataTable({
            responsive: true,
            destroy: true,
            stateSave: true,
            language: { url: "/control_de_pago_remake/public/js/lenguaje.json" },
            lengthMenu: [
                [15, 25, -1],
                ["15", "25", "Todos"],
            ],
            data: data,
            columns: [
                { data: "id", className: "columnaId" },
                {
                    data: "router",
                    className: "columnaRouter",
                    render: function (data, type, row) {
                        return `${row.router} <b>(VALOR: ${row.valor}$)</b>`;
                    },
                },
                {
                    data: "active",
                    render: function (data, type, row) {
                        let estado = "";

                        if (row.active) {
                            estado = `<img class="server_status" src="/control_de_pago_remake/public/img/configuracion/encendido.png" title="Estado: Activo">`;
                        } else {
                            estado = `<img class="server_status" src="/control_de_pago_remake/public/img/configuracion/apagado.png" title="Estado: Inactivo">`;
                        }

                        return estado;
                    },
                },
                {
                    data: "id",
                    render: function (data, type, row) {
                        return `<div class="opciones">
                                    <button class="button edit" title="Editar instalacion ${row.router}" onclick="editar_instalacion(${row.id})">Editar</button>
                                    <img class="eliminar" src="/control_de_pago_remake/public/img/configuracion/eliminar.png" title="Eliminar instalacion ${row.router}" onclick="eliminar_instalacion(${row.id})">
                                </div>`;
                    },
                },
            ],
        });
    } catch (error) {
        console.log("Error al cargar los nodos:", error);
    }
}

async function procesarClientes(clientes, servidorId, cantidadServidores, contador) {
    try {
        const titleProcess = document.getElementById("titleProcess");
        const progressBar = document.getElementById("progressBar");
        progressBar.max = cantidadServidores;

        const response = await fetch("/control_de_pago_remake/public/makeCut", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                clientes: clientes,
                servidorId: servidorId,
            }),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! estado: ${response.status}`);
        }

        const data = await response.json();

        titleProcess.innerText = `Procesando datos del servidor ${servidorId} (${clientes.length} clientes) progreso ${contador}/${cantidadServidores}`;
        progressBar.value = contador;

        setTimeout(() => {
            if (cantidadServidores == contador) {
                titleProcess.innerText = "Procesos terminados, generando reporte...";

                setTimeout(Swal.close(), 500);
            }
        }, 1500)

    } catch (error) {
        console.log("Error al cargar los datos:", error);
    }
}

async function realizarCortes() {
    try {
        const response = await fetch("/control_de_pago_remake/public/getDataToCutOff");

        if (!response.ok) {
            throw new Error(`HTTP error! estado: ${response.status}`);
        }

        const data = await response.json();

        Swal.fire({
            allowOutsideClick: false,
            showConfirmButton: false,
            html: `
                    <p id='titleProcess'>Preparando clientes...</p> 
                    <div id="container_load">
                        <progress id="progressBar" value="" max=""></progress>
                        <img src="/control_de_pago_remake/public/img/configuracion/cargando.png" id="cargando">
                    </div>
            `,
        });

        const clientesPorServidor = data.reduce((acumulador, cliente) => {
            const servidor = cliente.servidor;
            if (!acumulador[servidor]) {
                acumulador[servidor] = [];
            }
            acumulador[servidor].push(cliente);
            return acumulador;
        }, {});

        const cantidadServidores = Object.keys(clientesPorServidor).length;

        let contador = 0;

        for (const [servidorId, clientes] of Object.entries(clientesPorServidor)) {
            contador++;
            await procesarClientes(clientes, servidorId, cantidadServidores, contador);
        }

        /*  con esto podemos procesar en paralelo EXPERIMENTAL (mas rapido) 
            await Promise.all(
            Object.entries(clientesPorServidor).map(([servidorId, clientes]) =>
                procesarClientes(clientes, servidorId)
            )
        );*/

    } catch (error) {
        console.log("Error al cargar los datos:", error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    Swal.fire({
        title: "Cargando modulo administrativo",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
});

Promise.all([tabla_usuarios(), tabla_planes(), tabla_servidores(), tabla_nodos(), tabla_instalaciones()]).then(() => {
    Swal.close();
});
