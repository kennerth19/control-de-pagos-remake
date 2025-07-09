function verificar_fin_de_semana(fecha) {
  const dia = fecha.getDay();

  return (dia === 0 || dia === 7);
}

const hoy = new Date();
const esSabadoODomingo = verificar_fin_de_semana(hoy);

if (esSabadoODomingo) {
  let boton_corte = document.getElementById('cortes');
  boton_corte.removeAttribute('onclick')
  boton_corte.setAttribute('onclick','nyan()')
}

function nyan(){
  Swal.fire({
    title: "No se pueden realizar los cortes los fines de semana.",
    width: 600,
    padding: "3em",
    color: "#716add",
    imageWidth: 400,
    imageHeight: 200,
    backdrop: `rgb(1,51,104) url("/control_de_pago_remake/public/img/nyan.gif") center top no-repeat`
  });
}