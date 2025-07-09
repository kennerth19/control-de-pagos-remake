async function showMainTable(id) {
    try {
        const response = await fetch(`/control_de_pago_remake/public/obtener_cambios/${id}`)
            .then(response => response.json())
            .then(data => {
                table = $('#main').DataTable({
                    responsive: true,
                    stateSave: false,
                    language: { url: '/control_de_pago_remake/public/js/lenguaje.json', },
                    order: [
                        [0, 'desc'],
                    ],
                    lengthMenu: [
                        [100, 250, 500, 1000, 1500, -1],
                        ['100', '250', '500', '1000', '1500', 'Todos']
                    ],
                    data: data,
                    columns: [
                        { data: 'id' },
                        { data: 'usuario' },
                        { data: 'descripcion' },
                        {
                            data: 'fecha',
                            "render": function (data, type, row) {
                                let fecha_formateada = moment(row.fecha).locale('es').format('DD [de] MMMM [de] YYYY [a las] hh:mm:ss a');
                                return `${fecha_formateada}`
                            }
                        },
                    ]
                });
            })
    } catch (error) {
        console.log(error)
    }
}