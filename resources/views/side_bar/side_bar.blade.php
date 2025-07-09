<style>
	.side_bar {
		background-color: #212529;
		position: fixed;
		top: 0;
		color: #fff;
		height: -webkit-fill-available;
		width: 315px;
		display: grid;
		justify-items: center;
		align-content: space-between;
		font-size: 17px !important;
		font-weight: 300 !important;
		font-family: system-ui !important;
		padding: 15px;
		transition: left 1s cubic-bezier(0.65, 0.05, 0.36, 1);
		overflow: auto;
	}

	.side_bar::-webkit-scrollbar {
		-webkit-appearance: none;
	}

	.side_bar::-webkit-scrollbar:vertical {
		width: 10px;
	}

	.side_bar::-webkit-scrollbar-button:increment,
	.side_bar::-webkit-scrollbar-button {
		display: none;
	}

	.side_bar::-webkit-scrollbar:horizontal {
		height: 10px;
	}

	.side_bar::-webkit-scrollbar-thumb {
		background-color: #797979;
		border-radius: 20px;
		border: 2px solid #f1f2f3;
	}

	.side_bar::-webkit-scrollbar-track {
		border-radius: 10px;
	}

	.side_bar {left: {{$left}};/* Ignorar esto :V no hay error */}

	.side_bar_hovered {
		left: 0px;
	}

	.side_item {
		cursor: pointer;
		display: grid;
		grid-template-columns: 35px 190px 30px;
		align-items: center;
		justify-items: center;
		color: #fff;
		padding: 15px;
	}

	.side_item:hover {
		color: aliceblue;
	}

	img {
		width: -webkit-fill-available;
	}

	.img_sidebar {
		transition: transform 1s;
	}

	.bar_div {
		position: relative;
		bottom: 1px;
	}

	.side_item_contenedor {
		display: grid;
		grid-template-columns: 35px 200px 30px;
		grid-template-rows: 1fr;
		grid-column-gap: 0px;
		grid-row-gap: 18px;
		align-items: center;
		justify-items: center;
		text-decoration: none !important;
	}

	.em1 {
		grid-area: 1 / 1 / 2 / 2;
	}

	.em2 {
		grid-area: 1 / 2 / 2 / 3;
		margin: 0;
		color: #fff;
		font-family: system-ui !important;
	}

	.em3 {
		grid-area: 1 / 3 / 2 / 4;
	}

	.em4 {
		grid-area: 2 / 1 / 3 / 4;
		height: 1px;
		background: radial-gradient(circle, rgba(255, 255, 255, 1) 50%, rgba(0, 0, 0, 1) 100%);
		width: 0%;
		transition: width 1s;
	}

	#id_img_6{
		filter: grayscale(1);
	}

	#id_img_7{
		filter: grayscale(1);
	}

	.perfil_side_bar{
		width: 64px;
		height: 64px;
    	border-radius: 50%;
	}

	.user{
		width: 100%;
    	grid-template-columns: 1fr 65%;
	}
</style>

<script>
	let side_bar = document.getElementById('side_bar');

	function side_bar_in() {
		let side_bar = document.getElementById('side_bar');
		let tuerquita = document.getElementById('tuerquita');

		side_bar.classList.add('side_bar_hovered');
		side_bar.removeAttribute('onmouseover');
		side_bar.setAttribute('onmouseleave', 'side_bar_out()');

		side_bar_tuerquita_in();
	}

	function side_bar_out() {
		let side_bar = document.getElementById('side_bar');
		let tuerquita = document.getElementById('tuerquita');

		side_bar.classList.remove('side_bar_hovered');
		side_bar.removeAttribute('onmouseleave');
		side_bar.setAttribute('onmouseover', 'side_bar_in()');

		side_bar_tuerquita_out();
	}

	function efecto(id_side, id_img, dec, op) {
		let side = document.getElementById(id_side);
		let img = document.getElementById(id_img);
		let decoracion = document.getElementById(dec);

		if (op == 0) {
			side.style.transform = 'scale(1.2)';
			img.style.transform = 'scale(1.2)';
			decoracion.style.width = '100%';
		} else {
			side.style.transform = 'scale(1.0)';
			img.style.transform = 'scale(1.0)';
			decoracion.style.width = '0%';
		}
	}
</script>

<div class="side_bar" id="side_bar" onmouseover="side_bar_in()">
	<p class="side_item user" onclick="administrativo()" title="Ir al perfil de {{ Auth::user()->name }}"><img class="img_sidebar perfil_side_bar" src="/control_de_pago_remake/public/storage/perfil/{{ Auth::user()->perfil }}">Bienvenid@ {{ Auth::user()->name }}</p>
	<p id="servidor_fecha"></p>

	<a href="{{route('inicio')}}" class="side_item_contenedor" onmouseover="efecto('side_0', 'id_img_0', 'em4_0', 0)" onmouseleave="efecto('side_0', 'id_img_0', 'em4_0', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/homew.png" id="id_img_0">
		<p class="em2">Inicio</p><img class="img_sidebar em3" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_0">
		<div class="em4" id="em4_0"></div>
	</a>
	<a href="" class="side_item_contenedor" onmouseover="efecto('side_1', 'id_img_1', 'em4_1', 0)" onmouseleave="efecto('side_1', 'id_img_1', 'em4_1', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/resw.png" id="id_img_1">
		<p class="em2">Resumen</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_1">
		<div class="em4" id="em4_1"></div>
	</a>
	<a href="{{route('resumen_general')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_2', 'id_img_2', 'em4_2', 0)" onmouseleave="efecto('side_2', 'id_img_2', 'em4_2', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/mpw.png" id="id_img_2">
		<p class="em2">Log</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_2">
		<div class="em4" id="em4_2"></div>
	</a>
	<a href="{{route('pagos_resumen')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_3', 'id_img_3', 'em4_3', 0)" onmouseleave="efecto('side_3', 'id_img_3', 'em4_3', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/payw.png" id="id_img_3">
		<p class="em2">Reporte de Pagos</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_3">
		<div class="em4" id="em4_3"></div>
	</a>
	<a href="{{route('modulo_de_cortes')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_4', 'id_img_4', 'em4_4', 0)" onmouseleave="efecto('side_4', 'id_img_4', 'em4_4', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/cortesw.png" id="id_img_4">
		<p class="em2">Modulo de estados</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_4">
		<div class="em4" id="em4_4"></div>
	</a>
	<a href="{{route('evento_diario')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_5', 'id_img_5', 'em4_5', 0)" onmouseleave="efecto('side_5', 'id_img_5', 'em4_5', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/gastosw.png" id="id_img_5">
		<p class="em2">Evento del Dia</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_5">
		<div class="em4" id="em4_5"></div>
	</a>
	<a href="{{route('pre_registro')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_6', 'id_img_6', 'em4_6', 0)" onmouseleave="efecto('side_6', 'id_img_6', 'em4_6', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/inicio/pre_registro.png" id="id_img_6">
		<p class="em2">Pre-Registro</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_6">
		<div class="em4" id="em4_6"></div>
	</a>
	<a href="{{route('evento_admin')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_7', 'id_img_7', 'em4_7', 0)" onmouseleave="efecto('side_7', 'id_img_7', 'em4_7', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/safe.png" id="id_img_7">
		<p class="em2">Caja Fuerte</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_7">
		<div class="em4" id="em4_7"></div>
	</a>
	<a href="{{route('inventario')}}" target="_blank" class="side_item_contenedor" onmouseover="efecto('side_8', 'id_img_8', 'em4_8', 0)" onmouseleave="efecto('side_8', 'id_img_8', 'em4_8', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/caja.png" id="id_img_8">
		<p class="em2">Inventario</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_8">
		<div class="em4" id="em4_8"></div>
	</a>
	<a href="{{route('logout')}}" class="side_item_contenedor" onmouseover="efecto('side_9', 'id_img_9', 'em4_9', 0)" onmouseleave="efecto('side_9', 'id_img_9', 'em4_9', 1)"><img class="img_sidebar em1" src="/control_de_pago_remake/public/img/sidebar/e.png" id="id_img_9">
		<p class="em2">Cerrar Sesión</p><img class="img_sidebar" src="/control_de_pago_remake/public/img/sidebar/ab.png" id="side_9">
		<div class="em4" id="em4_9"></div>
	</a>
</div>

<script>
	function fecha_servidor() {
		let fecha_side = document.getElementById('servidor_fecha');
		let fecha_formateada = moment().locale('es').format('DD/MM/YYYY');  

		fecha_side.innerHTML = `Fecha Del Servidor ${fecha_formateada}`;
	}

	fecha_servidor();
</script>