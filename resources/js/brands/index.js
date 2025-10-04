import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs5';
import 'datatables.net-select-bs5';
import { Toast } from '../Toast';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const formBrand = document.querySelector('#formBrand');
const modalBrandElement = document.getElementById('modalCreateBrand');
const modalBrand = new Modal(modalBrandElement)
const createBrandTitle = document.getElementById('createBrandTitle')
const btnGuardar = document.getElementById('btnGuardar')
const btnModificar = document.getElementById('btnModificar')
const spinnerGuardar = document.getElementById('spinnerGuardar')
btnModificar.style.display = 'none'
btnModificar.disabled = true

let counter = 1;
let currentUpdateId;
spinnerGuardar.style.display = 'none'
const datatableBrand = new DataTable('#brandTable', {
    data: null,
    columns: [
        {
            title: 'No.',
            render: () => counter++
        },
        {
            title: 'Nombre',
            data: 'name'
        },
        {
            title: 'Opciones',
            data: 'id',
            render: (data, type, row, meta) => {
                return `
                <div class="btn-group-vertical" role="group" aria-label="option group">
                    <button class="btn btn-warning" data-id="${data}" data-name="${row.name}" data-bs-toggle="modal" data-bs-target="#modalCreateBrand" ><i class="bi bi-ui-checks me-2"></i>Editar</button>
                    <button class="btn btn-danger" data-id="${data}"><i class="bi bi-trash me-2"></i>Eliminar</button>
                </div>
                `
            }
        },

    ]
})

const saveBrand = async (event) => {
    event.preventDefault();
    spinnerGuardar.style.display = ''
    const buttonSubmitter = event.submitter
    buttonSubmitter.disabled = true
    const url = '/brands'
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'aplication/json',
    })
    const body = new FormData(formBrand)
    const config = {
        method: 'POST',
        headers,
        body,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const elements = formBrand.querySelectorAll('input')
        const feedbacks = formBrand.querySelectorAll('[id$="Feedback"]')
        elements.forEach(e => e.classList.remove('is-invalid'))
        feedbacks.forEach(f => f.textContent = '')
        if (respuesta.status == 422) {
            const { errors } = data
            for (const propiedad in errors) {
                document.getElementById(propiedad).classList.add('is-invalid')
                let contenido = '';
                errors[propiedad].forEach(info => {
                    contenido += info + "<br>"
                });
                document.getElementById(propiedad + "Feedback").innerHTML = contenido
            }
        } else if (respuesta.status == 200) {
            Toast.fire({
                icon: 'success',
                title: 'Marca creada correctamente'
            })
            getBrands();
            formBrand.reset();
            modalBrand.hide()
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Contacte al administrador'
            })
        }

        console.log(data);
    }
    catch (error) {
        console.log(error);
    }
    spinnerGuardar.style.display = 'none'
    buttonSubmitter.disabled = false
}

const getBrands = async () => {
    const url = '/brands'
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept': 'aplication/json'
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { brands } = data;
        let pageInfo = datatableBrand.page.info();
        let currentPage = pageInfo.page;
        let scrollPosition = window.scrollY;
        datatableBrand.clear().draw()
        if (brands.length > 0) {
            counter = 1;
            datatableBrand.rows.add(brands).draw();
            datatableBrand.page(currentPage).draw('page');
            window.scrollTo(0, scrollPosition);
        } else {
            Toast.fire({
                icon: 'info',
                title: 'No se encontraron registros'
            })
        }


    } catch (error) {
        console.log(error);
    }

}

getBrands();


const editBrand = (e) => {
    let button = e.currentTarget;
    formBrand.name.value = button.dataset.name
    createBrandTitle.textContent = "Editar producto"
    btnGuardar.style.display = 'none'
    btnGuardar.disabled = true
    btnModificar.style.display = ''
    btnModificar.disabled = false
    currentUpdateId = button.dataset.id

}

const resetearModal = () => {
    formBrand.reset();
    createBrandTitle.textContent = "Crear marca"
    btnModificar.style.display = 'none'
    btnModificar.disabled = true
    btnGuardar.style.display = ''
    btnGuardar.disabled = false
}



const updateBrand = async e => {
    e.preventDefault();
    const url = `/brands/${currentUpdateId}`
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'aplication/json',
    })
    const body = new FormData(formBrand)
    const config = {
        method: 'POST',
        headers,
        body,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        console.log(data);
        const elements = formBrand.querySelectorAll('input')
        const feedbacks = formBrand.querySelectorAll('[id$="Feedback"]')
        elements.forEach(e => e.classList.remove('is-invalid'))
        feedbacks.forEach(f => f.textContent = '')
        if (respuesta.status == 422) {
            const { errors } = data
            for (const propiedad in errors) {
                document.getElementById(propiedad).classList.add('is-invalid')
                let contenido = '';
                errors[propiedad].forEach(info => {
                    contenido += info + "<br>"
                });
                document.getElementById(propiedad + "Feedback").innerHTML = contenido
            }
        } else if (respuesta.status == 200) {
            Toast.fire({
                icon: 'success',
                title: 'Producto modificado correctamente'
            })
            getBrands();
            formBrand.reset();
            modalBrand.hide()
        } else {
            Toast.fire({
                icon: 'error',
                title: 'Contacte al administrador'
            })
        }
    }
    catch (error) {
        console.log(error);
    }
}

const deleteBrand = (e) => {
    let id = e.currentTarget.dataset.id
    Swal.fire({
        icon: 'warning',
        text: '¿Esta seguro que desea eliminar esta marca?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor: '#E5533D',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const url = `/brands/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'aplication/json',
            })
            const body = new FormData()
            const config = {
                method: 'DELETE',
                headers,
                body,
                credentials: 'include'
            }
            try {
                const respuesta = await fetch(url, config);
                const data = await respuesta.json();

                console.log(data);
                if (respuesta.status == 200) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Marca eliminada'
                    })
                    getBrands();
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Contacte al administrador'
                    })
                }
            }
            catch (error) {
                console.log(error);
            }
        }

    })
}

formBrand.addEventListener('submit', saveBrand);
datatableBrand.on('click', '.btn-warning', editBrand)
datatableBrand.on('click', '.btn-danger', deleteBrand)
modalBrandElement.addEventListener('show.bs.modal', resetearModal)
btnModificar.addEventListener('click', updateBrand)
