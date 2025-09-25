import pdfmake from 'pdfmake';
import DataTable from 'datatables.net-bs5';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-bs5';
import 'datatables.net-select-bs5';
import { Toast } from '../Toast';
import Swal from 'sweetalert2';
import { Modal } from 'bootstrap';

const formUser = document.querySelector('#formUser');
const formAsign = document.querySelector('#formAsign');
const formPassword = document.querySelector('#formPassword');
const modalElementUser = document.querySelector('#modalCreateUser');
const modalUser = new Modal(modalElementUser, {});
const modalElementAsign = document.querySelector('#modalAsign');
const modalAsign = new Modal(modalElementAsign, {});
const modalElementPassword = document.querySelector('#modalPassword');
const modalPassword = new Modal(modalElementPassword, {});
const spinnerGuardar = document.getElementById('spinnerGuardar')
let counter = 1;
spinnerGuardar.style.display = 'none'
const datatableUser = new DataTable('#usersTable', {
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
            title: 'Email',
            data: 'email'
        },
        {
            title: 'Permiso',
            data: 'roles',
            render: data => {
                if (data.length > 0) {
                    return data[0].name.toUpperCase()

                } else {
                    return "SIN PERMISO ASIGNADO"
                }
            }
        },
        {
            title: 'Opciones',
            data: 'id',
            render: (data, type, row, meta) => {
                return `
                <div class="btn-group-vertical" role="group" aria-label="option group">
                    <button class="btn btn-secondary" data-id="${data}" data-name="${row.name}" data-rol="${row.roles.length > 0 ? row.roles[0].id : null}" data-bs-toggle="modal" data-bs-target="#modalAsign"><i class="bi bi-person-badge-fill me-2"></i>Asignar</button>
                    <button class="btn btn-warning" data-id="${data}" data-name="${row.name}"  data-bs-toggle="modal" data-bs-target="#modalPassword"><i class="bi bi-key me-2"></i>Modificar contraseña</button>
                    <button data-id="${data}" class="btn btn-${row.status == "1" ? 'danger' : 'success'}"><i class="bi bi-${row.status == "1" ? 'x-circle' : 'check-circle'} me-2"></i>${row.status == "1" ? 'Desactivar' : 'Activar'}</button>
                </div>
                `
            }
        },

    ]
})

const getUsers = async () => {
    const url = '/users'
    const headers = new Headers({
        'Content-Type': 'application/json',
        'Accept': 'aplication/json',
    })
    const config = {
        method: 'GET',
        headers,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { status, users } = data;
        console.log(users);
        datatableUser.clear().draw()
        if (status) {
            counter = 1;
            datatableUser.rows.add(users).draw();
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

getUsers();

const guardarUsuario = async (event) => {
    event.preventDefault();
    spinnerGuardar.style.display = ''
    const buttonSubmitter = event.submitter
    buttonSubmitter.disabled = true

    const url = '/users'
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'aplication/json',
    })
    const body = new FormData(formUser)
    const config = {
        method: 'POST',
        headers,
        body,
        credentials: 'include'
    }
    try {
        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const elements = formUser.querySelectorAll('input')
        const feedbacks = formUser.querySelectorAll('[id$="Feedback"]')
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
                title: 'Usuario creado correctamente'
            })
            getUsers();
            formUser.reset();
            modalUser.hide()
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

const asignarValoresPassword = e => {
    const button = e.relatedTarget
    console.log(button.dataset);
    const id = button.dataset.id
    const name = button.dataset.name
    console.log(typeof rol);
    formPassword.user_password_id.value = id;
    formPassword.user_password_name.value = name;
}
const asignarValores = e => {
    const button = e.relatedTarget
    console.log(button.dataset);
    const id = button.dataset.id
    const name = button.dataset.name
    const rol = button.dataset.rol
    console.log(typeof rol);
    formAsign.user_id.value = id;
    formAsign.user_name.value = name;
    formAsign.rol.value = rol != 'null' ? rol : '';
}

const asignRol = async (event) => {
    event.preventDefault();
    const url = `/users/rol/${formAsign.user_id.value}`
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'aplication/json',
    })
    const body = new FormData(formAsign)
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

        const elements = formAsign.querySelectorAll('input')
        const feedbacks = formAsign.querySelectorAll('[id$="Feedback"]')
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
                title: 'Usuario asignado correctamente'
            })
            getUsers();
            formAsign.reset();
            modalAsign.hide()
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
}
const updatePassword = async (event) => {
    event.preventDefault();
    const url = `/users/password/${formPassword.user_password_id.value}`
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const headers = new Headers({
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'aplication/json',
    })
    const body = new FormData(formPassword)
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

        const elements = formPassword.querySelectorAll('input')
        const feedbacks = formPassword.querySelectorAll('[id$="Feedback"]')
        elements.forEach(e => e.classList.remove('is-invalid'))
        feedbacks.forEach(f => f.textContent = '')
        console.log(feedbacks);
        if (respuesta.status == 422) {
            const { errors } = data
            for (const propiedad in errors) {
                document.getElementById("new_" + propiedad).classList.add('is-invalid')
                let contenido = '';
                errors[propiedad].forEach(info => {
                    contenido += info + "<br>"
                });
                document.getElementById("user" + propiedad + "Feedback").innerHTML = contenido
            }
        } else if (respuesta.status == 200) {
            Toast.fire({
                icon: 'success',
                title: 'Contraseña actualizada'
            })
            getUsers();
            formPassword.reset();
            modalPassword.hide()
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
}

const updateStatus = (e) => {

    const button = e.currentTarget
    const id = button.dataset.id
    Swal.fire({
        icon: 'warning',
        text: '¿Esta seguro que desea realizar esta acción?',
        title: 'Confirmación',
        showCancelButton: true,
        confirmButtonColor: '#591C32',
        confirmButtonText: 'Si',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            const url = `/users/status/${id}`
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const headers = new Headers({
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'aplication/json',
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
                        title: 'Estado actualizado'
                    })
                    getUsers();
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


formUser.addEventListener('submit', guardarUsuario);
formAsign.addEventListener('submit', asignRol);
formPassword.addEventListener('submit', updatePassword);
modalElementAsign.addEventListener('show.bs.modal', asignarValores)
modalElementPassword.addEventListener('show.bs.modal', asignarValoresPassword)
datatableUser.on('click', '.btn-danger', updateStatus)
datatableUser.on('click', '.btn-success', updateStatus)
