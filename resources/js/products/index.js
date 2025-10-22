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
const imagesContainer = document.getElementById('imagesContainer');
const imageTitle = document.getElementById('imageTitle');
const createProductTitle = document.getElementById('createProductTitle')
const btnGuardar = document.getElementById('btnGuardar')
const btnModificar = document.getElementById('btnModificar')
const spinnerGuardar = document.getElementById('spinnerGuardar')
btnModificar.style.display = 'none'
btnModificar.disabled = true

let counter = 1;
let currentUpdateId;
let currentImagesProductId;
spinnerGuardar.style.display = 'none'
const datatableProduct = new DataTable('#productTable', {
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
            title: 'Precio',
            data: 'price',
            render: (data) => `Q. ${data}`
        },
        {
            title: 'Marca',
            data: 'brand_name'
        },
        {
            title: 'Descripcion',
            data: 'description'
        },
        {
            title: 'Imagenes',
            data: 'id',
            orderable: false,
            searchable: false,
            render: (data, type, row) => {
                return ` <button class="btn btn-info" ${row.images > 0 ? '' : 'disabled'} data-id="${data}" data-name="${row.name}" data-bs-toggle="modal" data-bs-target="#modalImages" ><i class="bi bi-eye me-2"></i>Ver</button>`
            }
        },
        {
            title: 'Opciones',
            data: 'id',
            render: (data, type, row, meta) => {
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
        'Accept': 'application/json',
    })
    const body = new FormData(formProduct)
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
        const elements = formProduct.querySelectorAll('.form-control')
        const feedbacks = formProduct.querySelectorAll('[id$="Feedback"]')
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
                title: 'Producto creado correctamente'
            })
            getProducts();
            formProduct.reset();
            modalProduct.hide()
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

const getProducts = async () => {
    const url = '/products'
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { products } = data;
        let pageInfo = datatableProduct.page.info();
        let currentPage = pageInfo.page;
        let scrollPosition = window.scrollY;
        datatableProduct.clear().draw()
        if (products.length > 0) {
            counter = 1;
            datatableProduct.rows.add(products).draw();
            datatableProduct.page(currentPage).draw('page');
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

getProducts();

const showImagesMessage = (message) => {
    imagesContainer.innerHTML = ''
    const messageCol = document.createElement('div')
    messageCol.classList.add('col-12', 'text-center', 'text-muted')
    messageCol.textContent = message
    imagesContainer.appendChild(messageCol)
}

const renderImages = (images = []) => {
    imagesContainer.innerHTML = ''

    if (!images || images.length === 0) {
        showImagesMessage('No hay imágenes registradas para este producto.')
        return
    }

    const fragment = document.createDocumentFragment()

    images.forEach(image => {
        const column = document.createElement('div')
        column.classList.add('col-12', 'col-sm-6', 'col-md-4')

        const card = document.createElement('div')
        card.classList.add('card', 'h-100', 'shadow-sm')

        const img = document.createElement('img')
        img.classList.add('card-img-top', 'object-fit-cover')
        img.alt = 'Imagen del producto'
        img.src = image.url
        img.loading = 'lazy'
        img.style.height = '200px'

        const cardBody = document.createElement('div')
        cardBody.classList.add('card-body', 'd-flex', 'justify-content-center')

        const deleteButton = document.createElement('button')
        deleteButton.classList.add('btn', 'btn-danger', 'btn-sm')
        deleteButton.type = 'button'
        deleteButton.innerHTML = "<i class='bi bi-trash me-2'></i>Eliminar"
        deleteButton.addEventListener('click', () => deleteImage(image.id))

        cardBody.appendChild(deleteButton)
        card.appendChild(img)
        card.appendChild(cardBody)
        column.appendChild(card)

        fragment.appendChild(column)
    })

    imagesContainer.appendChild(fragment)
}

const fetchProductImages = async () => {
    if (!currentImagesProductId) {
        showImagesMessage('Selecciona un producto para visualizar sus imágenes.')
        return
    }

    const url = `/products/${currentImagesProductId}/images`
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept': 'application/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }

    try {
        const respuesta = await fetch(url, config)
        if (!respuesta.ok) {
            throw new Error('Error al obtener las imágenes')
        }
        const data = await respuesta.json()
        renderImages(Array.isArray(data) ? data : [])
    } catch (error) {
        console.log(error)
        showImagesMessage('No fue posible cargar las imágenes del producto.')
    }
}

const handleImagesModalShow = async e => {
    const trigger = e.relatedTarget
    currentImagesProductId = trigger?.dataset.id
    const productName = trigger?.dataset.name

    if (productName) {
        imageTitle.textContent = `Imágenes del producto: ${productName}`
    } else {
        imageTitle.textContent = 'Imágenes del producto'
    }

    await fetchProductImages()
}

const resetImagesModal = () => {
    showImagesMessage('Selecciona un producto para visualizar sus imágenes.')
    currentImagesProductId = null
    imageTitle.textContent = 'Imágenes del producto'
}

resetImagesModal()

const resetearModal = (event) => {
    const trigger = event.relatedTarget
    formProduct.reset();
    formProduct.querySelectorAll('.is-invalid').forEach(element => element.classList.remove('is-invalid'))
    formProduct.querySelectorAll('[id$="Feedback"]').forEach(feedback => feedback.innerHTML = '')

    if (trigger && trigger.classList.contains('btn-warning')) {
        createProductTitle.textContent = "Editar producto"
        btnGuardar.style.display = 'none'
        btnGuardar.disabled = true
        btnModificar.style.display = ''
        btnModificar.disabled = false
        currentUpdateId = trigger.dataset.id
        formProduct.name.value = trigger.dataset.name ?? ''
        formProduct.price.value = trigger.dataset.price ?? ''
        const description = trigger.dataset.description
        formProduct.description.value = description && description !== 'null' ? description : ''
        const brand = trigger.dataset.brand
        formProduct.brand_id.value = brand && brand !== 'null' ? brand : ''
    } else {
        createProductTitle.textContent = "Crear producto"
        btnModificar.style.display = 'none'
        btnModificar.disabled = true
        btnGuardar.style.display = ''
        btnGuardar.disabled = false
        currentUpdateId = null
    }
}

const deleteImage = (id) => {
    Swal.fire({
        icon: 'warning',
        text: '¿Esta seguro que desea eliminar esta imagen?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor: '#E5533D',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const url = `/products/image/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            })
            const body = new FormData()
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
                if (respuesta.status == 200) {
                    Toast.fire({
                        icon: 'success',
                        title: 'Imagen eliminada'
                    })
                    await fetchProductImages();
                    getProducts();
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

const updateProduct = async e => {
    e.preventDefault();
    const url = `/products/${currentUpdateId}`
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
    })
    const body = new FormData(formProduct)
    const config = {
        method: 'POST',
        headers,
        body,
        credentials: 'include'
    }
    btnModificar.disabled = true
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        console.log(data);
        const elements = formProduct.querySelectorAll('.form-control')
        const feedbacks = formProduct.querySelectorAll('[id$="Feedback"]')
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
            getProducts();
            formProduct.reset();
            modalProduct.hide()
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
    btnModificar.disabled = false
}

const deleteProduct = (e) => {
    let id = e.currentTarget.dataset.id
    Swal.fire({
        icon: 'warning',
        text: '¿Esta seguro que desea eliminar este producto?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor: '#E5533D',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const url = `/products/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
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
                        title: 'Producto eliminado'
                    })
                    modalImages.hide();
                    getProducts();
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

formProduct.addEventListener('submit', guardarProducto);
modalImagesElement.addEventListener('show.bs.modal', handleImagesModalShow)
modalImagesElement.addEventListener('hidden.bs.modal', resetImagesModal)
datatableProduct.on('click', '.btn-danger', deleteProduct)
modalProductElement.addEventListener('show.bs.modal', resetearModal)
btnModificar.addEventListener('click', updateProduct)
