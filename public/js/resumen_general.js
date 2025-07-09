async function tabla_resumen() {
    try {
        await fetch("/control_de_pago_remake/public/resumen_general/get_data")
            .then((response) => response.json())
            .then((data) => {
                table = $("#main").DataTable({
                    responsive: true,
                    destroy: true,
                    stateSave: false,
                    order: [[0, "desc"]],
                    language: {
                        url: "/control_de_pago_remake/public/js/lenguaje.json",
                    },
                    data: data,
                    columns: [
                        { data: "id" },
                        { data: "usuario" },
                        { data: "descripcion" },
                        {
                            data: "fecha",
                            render: function (data, type, row) {
                                return moment(row.fecha).locale("es").format("DD [de] MMMM [de] YYYY [a las] hh:mm:ss A");
                            },
                        },
                        {
                            data: "tipo",
                            className: "tipo_resumen",
                            render: function (data, type, row) {
                                const tipos = {
                                    0: { texto: "MESES AÑADIDOS" },
                                    1: { texto: "EDICIÓN" },
                                    2: { texto: "ELIMINADO" },
                                    3: { texto: "CLIENTE AÑADIDO" },
                                    4: { texto: "SERVICIO AÑADIDO" },
                                    5: { texto: "CLIENTE Y SERVICIOS ELIMINADOS" },
                                    6: { texto: "SERVICIO ELIMINADO" },
                                    7: { texto: "UNION DE SERVICIOS" },
                                    8: { texto: "PAGO ELIMINADO" },
                                    9: { texto: "CLIENTE CONGELADO" },
                                    10: { texto: "CLIENTE DESCONGELADO" },
                                    11: { texto: "PRORROGA REGISTRADA" },
                                    12: { texto: "PRORROGA ELIMINADA" },
                                    13: { texto: "SESIÓN INICIADA" },
                                    14: { texto: "SESIÓN CERRADA" },
                                    15: { texto: "DEUDA PAGADA" },
                                    16: { texto: "ABONO DE DEUDA" },
                                    17: { texto: "USUARIO CREADO" },
                                    18: { texto: "PRORROGA ELIMINADA (AUTOMÁTICAMENTE)" },
                                    19: { texto: "INSTALACIÓN NO FUE AGREGADA AL PRE-REGISTRO" },
                                    20: { texto: "REAJUSTE DE FECHA" },
                                    21: { texto: "DESACTIVADO AUTOMÁTICO" },
                                    22: { texto: "DEUDA AGREGADA" },
                                    23: { texto: "SERVICIO SEPARADO" },
                                    24: { texto: "MARCADO" },
                                    25: { texto: "PAGO DE DEUDA DE INSTALACIÓN" },
                                    26: { texto: "TRAMPA DETECTADA" },
                                    27: { texto: "PLAN MODIFICADO" },
                                    28: { texto: "PLAN CREADO" },
                                    29: { texto: "SERVIDOR CREADO" },
                                    30: { texto: "SERVIDOR MODIFICADO" },
                                    31: { texto: "PLAN ELIMINADO" },
                                    32: { texto: "SERVIDOR ELIMINADO" },
                                    33: { texto: "INSTALACIÓN AGREGADA" },
                                    34: { texto: "INSTALACIÓN ELIMINADA" },
                                    35: { texto: "INSTALACIÓN MODIFICADA" },
                                    36: { texto: "USUARIO MODIFICADO" },
                                    37: { texto: "USUARIO CREADO" },
                                };

                                return tipos[row.tipo]?.texto || "DESCONOCIDO";
                            },

                            createdCell: function (td, cellData, rowData, row, col) {
                                const tipos = {
                                    0: { color: "green" },
                                    1: { color: "orange" },
                                    2: { color: "red" },
                                    3: { color: "green" },
                                    4: { color: "green" },
                                    5: { color: "red" },
                                    6: { color: "red" },
                                    7: { color: "orange" },
                                    8: { color: "red" },
                                    9: { color: "#1CB3FF" },
                                    10: { color: "#FF8231" },
                                    11: { color: "green" },
                                    12: { color: "red" },
                                    13: { color: "#0303b5" },
                                    14: { color: "red" },
                                    15: { color: "green" },
                                    16: { color: "green" },
                                    17: { color: "green" },
                                    18: { color: "red" },
                                    19: { color: "red" },
                                    20: { color: "green" },
                                    21: { color: "red" },
                                    22: { color: "green" },
                                    23: { color: "red" },
                                    24: { color: "green" },
                                    25: { color: "green" },
                                    26: { color: "red" },
                                    27: { color: "#1CB3FF" },
                                    28: { color: "red" },
                                    29: { color: "green" },
                                    30: { color: "orange" },
                                    31: { color: "red" },
                                    32: { color: "red" },
                                    33: { color: "green" },
                                    34: { color: "red" },
                                    35: { color: "orange" },
                                    36: { color: "green" },
                                    37: { color: "green" },
                                };

                                const color = tipos[rowData.tipo]?.color || "gray";

                                td.style.backgroundColor = color;
                            },
                        },
                    ],
                });
            });
    } catch (err) {
        console.log(err);
    }
}

Swal.fire({
    title: "Cargando tabla...",
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
    didOpen: () => {
        Swal.showLoading();
        tabla_resumen();
    },
});
