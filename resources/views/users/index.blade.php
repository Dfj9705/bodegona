@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col">
            <h1>Administración de usuarios</h1>
        </div>
        <div class="col-lg-2">
            <button class="btn btn-primary w-100"  data-bs-toggle="modal" data-bs-target="#modalCreateUser"><i class="bi bi-plus-circle me-2"></i> Crear</button>
        </div>
    </div>
    <div class="row">
        <div class="col table-responsive">
            <table class="table table-striped table-bordered text-center" id="usersTable"></table>
        </div>
    </div>

    <div class="modal fade" id="modalCreateUser" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="createUserTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserTitle">Crear usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" id="formUser" enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-2">
                            <div class="col">
                                <label for="name">Nombre de usuario</label>
                                <input type="text" name="name" id="name" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="nameFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="email">Correo del usuario</label>
                                <input type="email" name="email" id="email" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="emailFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="password">Contraseña</label>
                                <input type="password" name="password" id="password" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="passwordFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="password_confirmation">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-sm">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="formUser" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAsign" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="asignTItle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserTitle">Asignar permiso</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" id="formAsign" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="user_name">Nombre de usuario</label>
                                <input disabled type="text" name="user_name" id="user_name" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="userNameFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="rol">Correo del usuario</label>
                                <select name="rol" id="rol" class="form-control form-control-sm">
                                    <option value="">SELECCIONE...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{$role->id}}">{{ strtoupper($role->name) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback" id="rolFeedback"></div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="formAsign" class="btn btn-primary"><span class="spinner-border spinner-border-sm me-2" role="status" id="spinnerGuardar" aria-hidden="true"></span>Guardar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalPassword" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="passwordTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserTitle">Cambiar contraseña</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form autocomplete="off" id="formPassword" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_password_id" id="user_password_id">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="user_password_name">Nombre de usuario</label>
                                <input disabled type="text" name="name" id="user_password_name" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="usernameFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="new_password">Nueva contraseña</label>
                                <input type="password" name="password" id="new_password" class="form-control form-control-sm">
                                <div class="invalid-feedback" id="userpasswordFeedback"></div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="new_password">Confirmar nueva contraseña</label>
                                <input type="password" name="password_confirmation" id="new_password2" class="form-control form-control-sm">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" form="formPassword" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    @vite(['resources/js/users/index.js'])
@endsection