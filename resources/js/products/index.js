import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs5';
import 'datatables.net-select-bs5';
import { Toast } from '../Toast';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const formProduct = document.querySelector('#formProduct');
const modalProductElement = document.getElementById('modalCreateProduct');
const modalProduct = new Modal(modalProductElement)
const modalImagesElement = document.getElementById('modalImages');
const modalImages = new Modal(modalImagesElement)
const bodyCarousel = document.getElementById('bodyCarousel');
const createProductTitle = document.getElementById('createProductTitle')
const btnGuardar = document.getElementById('btnGuardar')
const btnModificar = document.getElementById('btnModificar')
const spinnerGuardar = document.getElementById('spinnerGuardar')
btnModificar.style.display = 'none'
btnModificar.disabled = true

let counter = 1;
let currentUpdateId;
spinnerGuardar.style.display = 'none'
const datatableProduct = new DataTable('#productTable',{
    data : null,
    columns : [
        {
            title : 'No.',
            render : () => counter ++
        },
        {
            title : 'Nombre',
            data: 'name'
        },
        {
            title : 'Precio',
            data: 'price',
            render : (data) => `Q. ${data}`
        },
        {
            title : 'Marca',
            data: 'brand_name'
        },
        {
            title : 'Descripcion',
            data: 'description'
        },
        // {
        //     title : 'Imagenes',
        //     data: 'id',
        //     render : (data, type, row, meta) => {
        //         return ` <button class="btn btn-info" ${row.images > 0 ? '' : 'disabled'} data-id="${data}" data-name="${row.name}" data-bs-toggle="modal" data-bs-target="#modalImages" ><i class="bi bi-eye me-2"></i>Ver</button>`
        //     }
        // },
        {
            title : 'Opciones',
            data: 'id',
            render : (data, type, row, meta) => {
                return `
                <div class="btn-group-vertical" role="group" aria-label="option group">
                    <button class="btn btn-warning" data-id="${data}" data-name="${row.name}" data-brand="${row.brand_id}" data-price="${row.price}" data-description="${row.description}" data-bs-toggle="modal" data-bs-target="#modalCreateProduct" ><i class="bi bi-ui-checks me-2"></i>Editar</button>
                    <button class="btn btn-danger" data-id="${data}"><i class="bi bi-trash me-2"></i>Eliminar</button>
                </div>
                `
            }
        },

    ]
})

const guardarProducto = async (event) => {
    event.preventDefault();
    spinnerGuardar.style.display = ''
    const buttonSubmitter = event.submitter
    buttonSubmitter.disabled = true
    const url = '/products'
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept' :'aplication/json',
    })
    const body = new FormData(formProduct)
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
        const elements = formProduct.querySelectorAll('input')
        const feedbacks = formProduct.querySelectorAll('[id$="Feedback"]')
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
            Toast.fire({
                icon : 'success',
                title : 'Producto creado correctamente'
            })
            getProducts();
            formProduct.reset();
            modalProduct.hide()
        }else{
            Toast.fire({
                icon : 'error',
                title : 'Contacte al administrador'
            })
        }

        console.log(data);
    }
    catch (error){
        console.log(error);
    }
    spinnerGuardar.style.display = 'none'
    buttonSubmitter.disabled = false
}

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
        const {products} = data;
        let pageInfo = datatableProduct.page.info(); 
        let currentPage = pageInfo.page; 
        let scrollPosition = window.scrollY;
        datatableProduct.clear().draw()
        if(products.length > 0){
            counter = 1;
            datatableProduct.rows.add(products).draw();
            datatableProduct.page(currentPage).draw('page');
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

getProducts();

const getImages = async e => {
    const id = e.relatedTarget.dataset.id

    const url = `/products/${id}/images`
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
        bodyCarousel.innerHTML = '';
        if(data){
            const fragment = document.createDocumentFragment();
            let counter = 1;
            data.forEach(d => {
                const div = document.createElement('div')
                const divCaption = document.createElement('div')
                const buttonImage = document.createElement('button')
                const img = document.createElement('img')


                div.classList.add('carousel-item')
                counter == 1 ? div.classList.add('active') : null;
                img.classList.add('d-block','w-100')
                img.alt = "Imagen del producto"
                img.src = `${d.url}`

                divCaption.classList.add('carousel-caption', 'd-block')
                buttonImage.classList.add('btn','btn-danger')
                buttonImage.innerHTML = "<i class='bi bi-trash'></i>"
                buttonImage.addEventListener('click',() => deleteImage(d.id))

                divCaption.appendChild(buttonImage)
                div.appendChild(img)
                div.appendChild(divCaption)
                fragment.appendChild(div)
                console.log(d);
                counter++
            })
            bodyCarousel.appendChild(fragment)

        }
    } catch (error) {
        console.log(error);
    }
}

const editProduct = (e) => {
    let button = e.currentTarget;
    formProduct.name.value = button.dataset.name
    formProduct.price.value = button.dataset.price
    formProduct.description.value = button.dataset.description
    formProduct.brand_id.value = button.dataset.brand
    createProductTitle.textContent = "Editar producto"
    btnGuardar.style.display = 'none'
    btnGuardar.disabled = true
    btnModificar.style.display = ''
    btnModificar.disabled = false
    currentUpdateId = button.dataset.id

}

const resetearModal = () => {
    formProduct.reset();
    createProductTitle.textContent = "Crear producto"
    btnModificar.style.display = 'none'
    btnModificar.disabled = true
    btnGuardar.style.display = ''
    btnGuardar.disabled = false
}

const deleteImage = (id) => {
    Swal.fire({
        icon : 'warning',
        text : '¿Esta seguro que desea eliminar esta imagen?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor : '#591C32',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then( async (result) => {
        if(result.isConfirmed){
            const url = `/products/image/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept' :'aplication/json',
            })
            const body = new FormData()
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
                if (respuesta.status == 200){
                    Toast.fire({
                        icon : 'success',
                        title : 'Imagén eliminada'
                    })
                    modalImages.hide();
                    getProducts();
                }else{
                    Toast.fire({
                        icon : 'error',
                        title : 'Contacte al administrador'
                    })
                }
            }
            catch (error){
                console.log(error);
            }
        }

    })
} 

const updateProduct = async e => {
    e.preventDefault();
    const url = `/products/${currentUpdateId}`
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept' :'aplication/json',
    })
    const body = new FormData(formProduct)
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
        const elements = formProduct.querySelectorAll('input')
        const feedbacks = formProduct.querySelectorAll('[id$="Feedback"]')
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
            Toast.fire({
                icon : 'success',
                title : 'Producto modificado correctamente'
            })
            getProducts();
            formProduct.reset();
            modalProduct.hide()
        }else{
            Toast.fire({
                icon : 'error',
                title : 'Contacte al administrador'
            })
        }
    }
    catch (error){
        console.log(error);
    }
}

const deleteProduct = (e) => {
    let id = e.currentTarget.dataset.id
    Swal.fire({
        icon : 'warning',
        text : '¿Esta seguro que desea eliminar este producto?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor : '#591C32',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then( async (result) => {
        if(result.isConfirmed){
            const url = `/products/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept' :'aplication/json',
            })
            const body = new FormData()
            const config = {
                method: 'DELETE',
                headers,
                body,
                credentials: 'include'
            }
            try {
                const respuesta = await fetch (url, config);
                const data = await respuesta.json();
        
                console.log(data);
                if (respuesta.status == 200){
                    Toast.fire({
                        icon : 'success',
                        title : 'Producto eliminado'
                    })
                    modalImages.hide();
                    getProducts();
                }else{
                    Toast.fire({
                        icon : 'error',
                        title : 'Contacte al administrador'
                    })
                }
            }
            catch (error){
                console.log(error);
            }
        }

    })
} 

formProduct.addEventListener('submit', guardarProducto);
modalImagesElement.addEventListener('show.bs.modal', getImages)
datatableProduct.on('click', '.btn-warning', editProduct )
datatableProduct.on('click', '.btn-danger', deleteProduct )
modalProductElement.addEventListener('show.bs.modal', resetearModal )
btnModificar.addEventListener('click', updateProduct)
