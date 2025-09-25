import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs5';
import 'datatables.net-select-bs5';
import { Toast } from '../Toast';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const formMovement = document.getElementById('formMovement')
const modalMovementElement = document.getElementById('modalMovement');
const modalMovement = new Modal(modalMovementElement)
const buttonAdd = document.querySelector('#buttonAdd')
const buttonDelete = document.querySelector('#buttonDelete')
const divInputs = document.querySelector('#divInputs')
const modalDetalleElement = document.getElementById('modalDetalle');
const modalDetalle = new Modal(modalDetalleElement)
const productTitle = document.getElementById('productTitle')
const btnAplicar = document.getElementById('btnAplicar')
const spinnerGuardar = document.getElementById('spinnerGuardar')
const spanTotalVentas = document.getElementById('totalVentas')
let counter = 1;
let addedInputs = 0;
let products = [];
let total = 0;
spinnerGuardar.style.display = 'none'
const datatableMovements = new DataTable('#movementsTable',{
    data : null,
    columns : [
        {
            title : 'No.',
            render : () => counter ++
        },
        {
            title : 'Producto',
            data: 'name'
        },
        {
            title : 'Precio',
            data: 'price',
            render : data => `Q. ${data}`
        },
        {
            title : 'Ventas',
            data: 'price',
            render : (data, type, row, meta) => `Q. ${parseFloat(data * row['egresos']).toFixed(2)}`,
        },
        {
            title : 'Ingresos',
            data: 'ingresos',
            render: data => `<span class="${ data > 0 ? 'text-success' : 'text-muted' } fw-bold" >${data}</span>`
        },
        {
            title : 'Egresos/Ventas',
            data: 'egresos',
            render: data => `<span class="${ data > 0 ? 'text-danger' : 'text-muted' } fw-bold" >${data}</span>`
        },
        {
            title : 'Saldo',
            data: 'saldo',
            render: data => `<span class="${ data > 0 ? 'text-success' : 'text-muted' } fw-bold" >${data}</span>`
        },
        {
            title : 'Detalle',
            data: 'id',
            render : (data, type, row, meta) => {
                return ` <button class="btn btn-info" data-id="${data}" data-name="${row.name}" data-bs-toggle="modal" data-bs-target="#modalDetalle" ><i class="bi bi-table me-2"></i>Ver detalle</button>`
            }
        },
    ]
})

const datatableIngresos = new DataTable('#ingresosTable',{
    data : null,
    columns : [
        {
            title : 'No.',
            render : () => counter ++
        },
        {
            title : 'Fecha',
            data: 'date'
        },
        {
            title : 'Cantidad',
            data: 'amount',
        },
    ]
})
const datatableEgresos = new DataTable('#egresosTable',{
    data : null,
    columns : [
        {
            title : 'No.',
            render : () => counter ++
        },
        {
            title : 'Fecha',
            data: 'date'
        },
        {
            title : 'Cantidad',
            data: 'amount',
        }
    ]
})

const saveMovement = async (event) => {
    event.preventDefault();
    spinnerGuardar.style.display = ''
    const buttonSubmitter = event.submitter
    buttonSubmitter.disabled = true
    const url = '/movements'
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept' :'aplication/json',
    })
    const body = new FormData(formMovement)
    const config = {
        method: 'POST',
        headers,
        body,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch (url, config);
        const data = await respuesta.json();
        console.log(data);
        const elements = formMovement.querySelectorAll('input')
        const feedbacks = formMovement.querySelectorAll('[id$="Feedback"]')
        elements.forEach(e=> e.classList.remove('is-invalid'))
        feedbacks.forEach(f => f.textContent = '')
        if(respuesta.status == 422){
            const {errors} =data   
            for (const propiedad in errors) {
                document.getElementById(propiedad).classList.add('is-invalid')
                let contenido = '';
                errors[propiedad].forEach(info => {
                    contenido += info + "<br>"
                });
                document.getElementById(propiedad + "Feedback").innerHTML = contenido
            }
        }else if (respuesta.status == 200){
            const {id} = data;
            let showbutton = id ? true : false;
            Swal.fire({
                icon : 'success',
                title : 'Movimiento ingresado correctamente',
                showConfirmButton :showbutton,
                confirmButtonText : "Imprimir comprobante",
                customClass : {
                    confirmButton : 'btn btn-info',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling : false
            }).then( (result) => {
                if(result.isConfirmed){
                    window.open(`/receipt?id=${id}`, '_blank')
                }
            })
            getMovements();
            formMovement.reset();
            modalMovement.hide()
        }else{
            Toast.fire({
                icon : 'error',
                title : 'Contacte al administrador'
            })
        }

    }catch (error){
        console.log(error);
    }
    spinnerGuardar.style.display = 'none'
    buttonSubmitter.disabled = false
}

const getMovements = async () => {
    total = 0;
    const fechaInicio = document.getElementById('fechaInicio').value.replace('T', ' ')
    const fechaFinal = document.getElementById('fechaFinal').value.replace('T', ' ')
    // console.log(fechaInicio, fechaFinal);
    const url = `/movements?fechaInicio=${fechaInicio}&fechaFin=${fechaFinal}`
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

        const {movements} = data;
        let pageInfo = datatableMovements.page.info(); 
        let currentPage = pageInfo.page; 
        let scrollPosition = window.scrollY;
        datatableMovements.clear().draw()
        if(movements.length > 0){

            movements.forEach(movement => {
                total += parseFloat(movement.egresos) * parseFloat(movement.price) 
            });

            console.log(total);
            counter = 1;
            spanTotalVentas.innerText = total.toFixed(2)
            datatableMovements.rows.add(movements).draw();
            datatableMovements.page(currentPage).draw('page');
            window.scrollTo(0, scrollPosition);
        }else{
            Toast.fire({
                icon : 'info',
                title : 'No se encontraron registros'
            })
        }


    } catch (error) {
        console.log(error);
    }
}

getMovements();

const getProducts = async () => {
    const url = '/products'
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

        if(data){
            products = data.products;
            console.log(data);
        }

        console.log(products);
       
    } catch (error) {
        console.log(error);
    }
}

getProducts();

const addInput = e => {
    const divRow = document.createElement('div')
    const divColProduct = document.createElement('div')
    const divColAmount = document.createElement('div')
    const labelProduct = document.createElement('label')
    const labelAmount = document.createElement('label')
    const selectProduct = document.createElement('select')
    const inputAmount = document.createElement('input')
    const option = document.createElement('option')
    const productFeedback = document.createElement('div')
    const amountFeedback = document.createElement('div')
    // <div class="invalid-feedback" id="dateFeedback"></div>
    productFeedback.classList.add('invalid-feedback')
    productFeedback.id = `products.${addedInputs}Feedback`
    amountFeedback.classList.add('invalid-feedback')
    amountFeedback.id = `amounts.${addedInputs}Feedback`

    option.selected = true
    option.text = "SELECCIONE..."
    option.value = ''

    divRow.classList.add('row','mb-3')
    divColProduct.classList.add('col-lg-8','mb-2','mb-lg-0')
    divColAmount.classList.add('col-lg-4')

    labelProduct.htmlFor = `product_${addedInputs}`
    labelProduct.innerText = `Producto ${addedInputs + 1}`
    labelAmount.htmlFor = `amount_${addedInputs}`
    labelAmount.innerText = `Cantidad ${addedInputs + 1}`

    selectProduct.appendChild(option)

    selectProduct.name = `products[]`
    selectProduct.id = `products.${addedInputs}`
    selectProduct.classList.add('form-control')

    const fragment = document.createDocumentFragment();
    products.forEach(p => {
        let option = document.createElement('option')
        option.value = p.id
        option.text = p.name
        fragment.appendChild(option)
    })
    selectProduct.appendChild(fragment)
    selectProduct.onchange = checkSelection

    inputAmount.type = 'number'
    inputAmount.name = 'amounts[]'
    inputAmount.id = `amounts.${addedInputs}`
    inputAmount.classList.add('form-control')
    inputAmount.min = "1"
    inputAmount.value = "1"
    inputAmount.autocomplete = "off"

    divColProduct.appendChild(labelProduct)
    divColProduct.appendChild(selectProduct)
    divColProduct.appendChild(productFeedback)
    divColAmount.appendChild(labelAmount)
    divColAmount.appendChild(inputAmount)
    divColAmount.appendChild(amountFeedback)

    divRow.appendChild(divColProduct)
    divRow.appendChild(divColAmount)

    divInputs.appendChild(divRow)

    addedInputs++;
}
const deleteInput = () => {
    if(addedInputs > 0){
        divInputs.removeChild(divInputs.lastElementChild)
        addedInputs--
    }else{
        Toast.fire({
            icon : 'warning',
            title : 'No puede quitar mas campos'
        })
    }
}

const checkSelection = e => {
    const idSelected = e.target.value
    let productSelects = formMovement.querySelectorAll('select[id^=product]')
    
    productSelects.forEach(p => {
        if(p.value == idSelected && p.id != e.target.id){
            Toast.fire({
                icon : 'warning',
                title : 'Ya seleccionó este producto'
            })
            e.target.value = ''
        }
    })
}

const clearInputs = e => {
    while (addedInputs > 0) {
        deleteInput();
    }
}

const openModalDetalle = async e => {
    const fechaInicio = document.getElementById('fechaInicio').value.replace('T', ' ')
    const fechaFinal = document.getElementById('fechaFinal').value.replace('T', ' ')
    const button = e.relatedTarget
    const {id, name} = button.dataset
    productTitle.textContent = name
    const url = `/products/${id}?fechaInicio=${fechaInicio}&fechaFin=${fechaFinal}`
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept' :'aplication/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    datatableEgresos.clear().draw()
    datatableIngresos.clear().draw()
    try {
        const respuesta = await fetch (url, config);
        const data = await respuesta.json();

        console.log(data);
        const { movements } = data;
        

        const ingresos = movements.filter( m => m.type == 1 )
        const egresos = movements.filter( m => m.type == 2 )

        counter = 1;
        datatableIngresos.rows.add(ingresos).draw();
        counter = 1;
        datatableEgresos.rows.add(egresos).draw();
        // console.log(ingresos);
        // console.log(egresos);
       
    } catch (error) {
        console.log(error);
    }
}

modalMovementElement.addEventListener('show.bs.modal', clearInputs)
modalDetalleElement.addEventListener('show.bs.modal', openModalDetalle)
formMovement.addEventListener('submit', saveMovement);
buttonAdd.addEventListener('click', addInput)
buttonDelete.addEventListener('click', deleteInput)
btnAplicar.addEventListener('click', getMovements)