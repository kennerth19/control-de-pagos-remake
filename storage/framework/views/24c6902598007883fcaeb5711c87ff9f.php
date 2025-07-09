<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <link href="<?php echo e(asset('css/sweetalert2.css')); ?>" rel="stylesheet">

    <script src="<?php echo e(asset('js/sweetalert2.js')); ?>"></script>
    <link rel="shortcut icon" sizes="60x60" href="<?php echo e(asset('img/favicon-16x16.png')); ?>">
    <link href="<?php echo e(asset('css/login.css')); ?>" rel="stylesheet">
    <title>Inicio de sesión</title>
</head>

<body style="background-image: url('/control_de_pago_remake/public/img/inicio/background.png');">
<?php if(session('incorrecto') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Datos incorrectos.',
            background: '#313338',
            color: '#ffffff',
            showConfirmButton: false,
        })
    </script>
    <?php endif; ?>

    <?php if(session('creado') == 'ok'): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'El usuario fue creado con éxito!.',
            background: '#313338',
            color: '#ffffff',
            showConfirmButton: false,
        })
    </script>
    <?php endif; ?>

    <form action="<?php echo e(route('login')); ?>" method="post" class="main_form">

        <img src="/control_de_pago_remake/public/img/logo.png" class="header_item logo_item" style="width: 124px;">
        
        <h1>Inicio de sesión</h1>
        <?php echo csrf_field(); ?>

        <div class="inputs">
            <sup>NOMBRE DE USUARIO <span style="color: red;">*</span></sup>
            <input type="name" name="name" required>
        </div>

        <div class="inputs">
            <sup for="password">CONTRASEÑA <span style="color: red;">*</span></sup>
            <input type="password" name="password" required>
        </div>

        <button type="submit" id="submit">Iniciar sesión</button>

        <p class="registrarse" style="display: none;">¿Necesitas una cuenta? <span id="crear">Registrarse</span></p>
    </form>

    <script>
        const boton = document.querySelector("#crear");

        boton.addEventListener("click", () =>
            fetch('/control_de_pago_remake/public/login/data')
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    title: 'Creación de usuario:',
                    html: `
                        <form method="POST" action="<?php echo e(route('nuevo_usuario')); ?>" class="">
                            <?php echo csrf_field(); ?>
                            <label for="name">Nombre</label>
                            <input type="text" name="nombre" id="nombre" required>
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" required>
                            <button type="submit" id="crear_usuario">Crear Usuario</button>
                            <p style="color: red; display: none;" id="alerta">El nombre ingresado esta en uso.</p>
                        </form>
                    `,
                    showConfirmButton: false,
                });

                const submit_boton = document.querySelector("#crear_usuario");

                submit_boton.addEventListener("click", (event) => {
                    const nombre = document.querySelector("#nombre");
                    const alerta = document.querySelector("#alerta");
                    if (data.some(item => item.name === nombre.value.toLowerCase())) {
                        console.log(`El nombre ingresado: ${nombre} ya se encuentra registrado en la base de datos`);
                        nombre.style.borderColor = "red";
                        alerta.style.display = "block";
                        event.preventDefault();
                    }
                });
            })
        );
    </script>
</body>

</html><?php /**PATH C:\xampp\htdocs\control_de_pago_remake\resources\views/sistema_login/login.blade.php ENDPATH**/ ?>