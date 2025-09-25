import { Chart } from "chart.js/auto"

const btnAplicar = document.getElementById('btnAplicar')
const canvasMovements = document.getElementById('chartMovements')
const contextMovements = canvasMovements.getContext('2d')
const yearChartSales = document.getElementById('yearChartSales')
const chartMovements = new Chart(contextMovements, {
    type : 'bar',
    data : {
        labels: [],
        datasets : []
    }
})
const canvasSales = document.getElementById('chartSales')
const contextSales = canvasSales.getContext('2d')
const chartSales = new Chart(contextSales, {
    type : 'line',
    data : {
        labels: [],
        datasets : []
    }
})

const getMovements = async () => {
    const fechaInicio = document.getElementById('fechaInicio').value.replace('T', ' ')
    const fechaFinal = document.getElementById('fechaFinal').value.replace('T', ' ')
    const url = `/chart/movements?fechaInicio=${fechaInicio}&fechaFin=${fechaFinal}`
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept' :'aplication/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch (url, config);
        const data = await respuesta.json();
        console.log(data);
        
        let labels = [];
        let datasets = [];
        let ingresos = {
            label : 'Ingresos',
            data : [],
            borderWidth : 1,
            backgroundColor : 'rgba(6,204,58,0.2)'
        }
        let egresos = {
            label : 'Egresos',
            data : [],
            borderWidth : 1,
            backgroundColor : 'rgba(204,33,6,0.2)'
        }
        chartMovements.data.labels = []
        chartMovements.data.datasets = []
        if(data){
            data.forEach(m => {
                labels = [...labels, m.name]
                m.movements.forEach( l => {
                    l.type == 1 ?
                        ingresos.data = [...ingresos.data , l.amount]
                    :    egresos.data = [...egresos.data , l.amount]
                                        
                })
            });
            datasets = [ingresos, egresos]
            chartMovements.data.labels = labels
            chartMovements.data.datasets = datasets
        }
        chartMovements.update();
    } catch (error) {
        console.log(error);
    }
}
const getSales = async () => {
    let fechaInicio = document.getElementById('fechaInicio').value.replace('T', ' ')

    const year = new Date(fechaInicio ? fechaInicio : Date.now()).getFullYear();
    yearChartSales.textContent = year
    console.log(year);
    const url = `/chart/sales?year=${year}`
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept' :'aplication/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch (url, config);
        const data = await respuesta.json();
        console.log(data);

        const {months, amount} = data
        
      
      
        chartSales.data.labels = []
        chartSales.data.datasets = []
        if(data){
            chartSales.data.labels = months
            chartSales.data.datasets = [{
                label : 'Ventas',
                data : amount,
                fill: false,
                backgroundColor : 'rgb(75, 192, 192)',
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
        chartSales.update();
    } catch (error) {
        console.log(error);
    }
}


getMovements()
getSales()

btnAplicar.addEventListener('click', () => {
    getMovements()
    getSales()
} )