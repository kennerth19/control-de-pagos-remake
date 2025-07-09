try {
    const response = fetch(`/control_de_pago_remake/public/inventario_log_data`)
        .then(response => response.json())
        .then(data => {
            table = $('#main').DataTable({
                responsive: true,
                stateSave: false,
                destroy: true,
                pageLength: -1,
                order: [
                    [0, 'desc'],
                ],
                language: {
                    url: '/control_de_pago_remake/public/js/lenguaje.json',
                },
                lengthMenu: [
                    [100, -1],
                    ['100', 'Todos']
                ],
                data: data,
                columns: [
                    { data: 'id' },
                    { data: 'usuario' },
                    { data: 'evento' },
                    {
                        data: 'fecha',
                        "render": function (data, type, row) {
                            let fecha_formateada = moment(row.fecha).locale('es').format('DD [de] MMMM [de] YYYY [a las] hh:mm:ss a');
                            return `${fecha_formateada}`
                        }
                    },
                    {
                        data: 'id',
                        "render": function (data, type, row) {
                            let estilos = "";
                            let reporte = "";

                            if(row.tipo == 1){
                                estilos = "background-color: green;";
                                reporte = "Categoría creada";
                            }else if(row.tipo == 2){
                                estilos = "background-color: green;";
                                reporte = "Nueva(s) existencia(s)";
                            }else if(row.tipo == 3){
                                estilos = "background-color: red;";
                                reporte = "Categoría eliminada";
                            }else if(row.tipo == 4){
                                estilos = "background-color: orange;";
                                reporte = "Restar inventario";
                            }else if(row.tipo == 5){
                                estilos = "background-color: green;";
                                reporte = "Router asignado";
                            }else if(row.tipo == 6){
                                estilos = "background-color: red;";
                                reporte = "Router desasignado";
                            }else if(row.tipo == 7){
                                estilos = "background-color: orange;";
                                reporte = "Categoría editada";
                            }else if(row.tipo == 8){
                                estilos = "background-color: red;";
                                reporte = "Existencia eliminada";
                            }else if(row.tipo == 9){
                                estilos = "background-color: red;";
                                reporte = "Router no asignado";
                            }else if(row.tipo == 10){
                                estilos = "background-color: green;";
                                reporte = "Salida";
                            }else if(row.tipo == 11){
                                estilos = "background-color: red;";
                                reporte = "Alerta";
                            }

                            return `<p style="${estilos}">${reporte}</p>`
                        }
                    },
                ]
            });

            let searchbar = document.querySelector('#customSearch');

            searchbar.addEventListener('input', function () { table.search(this.value).draw(); })
        })
} catch (error) { 
    console.log(error) 
}