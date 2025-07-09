async function evento_log() {
    try {
        const response = await fetch(`/control_de_pago_remake/public/evento_log_admin`)
            .then(response => response.json())
            .then(data => {
                table = $('#log').DataTable({
                    responsive: true,
                    stateSave: false,
                    language: { url: '/control_de_pago_remake/public/js/lenguaje.json', },
                    lengthMenu: [
                        [100, 250, 500, 1000, 1500, -1],
                        ['100', '250', '500', '1000', '1500', 'Todos']
                    ],
                    data: data,
                    columns: [
                        { data: 'id' },
                        { data: 'eliminado_por' },
                        { data: 'evento' },
                        {
                            data: 'fecha',
                            "render": function (data, type, row) {
                                let fecha_formateada = moment(row.fecha).locale('es').format('DD [de] MMMM [de] YYYY [a las] hh:mm:ss a');
                                return `${fecha_formateada}`
                            }
                        },
                        { data: 'dolares' },
                        { data: 'bolivares' },
                        { data: 'pagomovil' },
                        { data: 'euro' },
                        { data: 'zelle_a' },
                        
                    ]
                });
            })
    } catch (error) {
        console.log(error)
    }
}