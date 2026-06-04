<!-- =======================================================
     MOTOR GLOBAL DE PERFILES ESTILO NETFLIX (OSWA INV)
     ======================================================= -->

<!-- Pantalla de Selección de Perfiles -->
<div id="oswa-profile-selector" class="oswa-netflix-overlay oswa-hidden" style="display:none">
    <div class="oswa-netflix-content">
<div style="text-align: left; padding-left: 20px; margin-bottom: -40px; position: relative; z-index: 10;">
            <button onclick="cerrarSelectorPerfiles()" style="background: transparent; border: none; color: #b3b3b3; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: color 0.3s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#b3b3b3'">
                <i class="bi bi-arrow-left"></i> <span style="font-size: 1.2rem;">Volver</span>
            </button>
        </div>
        <h1 class="oswa-netflix-title" id="titulo-perfiles">¿Quién está gestionando ahora?</h1>
        
        <div class="oswa-netflix-profiles">
            @foreach(\App\Models\User::all() as $user)
            <div class="oswa-profile-card">
                <div class="oswa-edit-icon edit-icon-overlay oswa-hidden" onclick="abrirModalEdicion({{ $user->id }}, '{{ $user->display_name }}')">
                    <i class="bi bi-pencil-fill"></i>
                </div>
                <div class="oswa-avatar-container" onclick="seleccionarPerfilConCarga({{ $user->id }})">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->display_name }}" class="oswa-avatar-img">
                    @else
                        <div class="oswa-avatar oswa-avatar-initials" style="background-color: {{ $loop->iteration == 1 ? '#E50914' : ($loop->iteration == 2 ? '#2b90d9' : '#4CAF50') }};">
                            {{ strtoupper(substr($user->display_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <span class="oswa-name">{{ $user->display_name }}</span>
            </div>
            @endforeach
            
            <div class="oswa-profile-card" onclick="abrirModalCreacion()">
                <div class="oswa-avatar oswa-add-profile">
                    <i class="bi bi-plus-circle" style="font-size: 3rem;"></i>
                </div>
                <span class="oswa-name">Agregar perfil</span>
            </div>
        </div>

        <button class="oswa-btn-manage" id="btn-administrar" onclick="activarModoEdicion()">Administrar perfiles</button>
        <button class="oswa-btn-cancel oswa-hidden" id="btn-listo" style="display: none;" onclick="desactivarModoEdicion()">Listo</button>
    </div>
</div>

<!-- Modal: Editar Perfil -->
<div id="oswa-modal-edit" class="oswa-modal oswa-hidden">
    <div class="oswa-modal-content">
        <span class="oswa-close" onclick="cerrarModalEdicion()">&times;</span>
        <h2>Editar perfil</h2>
        <div id="oswa-edit-avatar-preview" class="oswa-avatar oswa-avatar-initials" style="background-color: #E50914; margin: 0 auto 1.5rem;">C</div>
        <form id="oswa-form-edit" method="POST" enctype="multipart/form-data" onsubmit="enviarFormularioEdicion(event)">
            <input type="hidden" id="edit-user-id" name="user_id">
            <div class="oswa-input-group">
                <input type="text" id="edit-user-name" name="name" required placeholder="Nuevo nombre de perfil">
            </div>
            <div class="oswa-input-group">
                <label for="edit-profile-photo" class="oswa-btn-manage" style="cursor: pointer; display: inline-block;">Cambiar foto</label>
                <input type="file" id="edit-profile-photo" name="profile_photo" accept="image/*" class="oswa-hidden" onchange="previewPhoto(event)" style="display: none;">
            </div>
            <button type="submit" class="oswa-btn-action">Guardar</button>
            <button type="button" class="oswa-btn-delete" onclick="eliminarPerfil()">Eliminar perfil</button>
        </form>
    </div>
</div>

<!-- Modal: Crear Perfil -->
<div id="oswa-modal-create" class="oswa-modal oswa-hidden">
    <div class="oswa-modal-content">
        <span class="oswa-close" onclick="cerrarModalCreacion()">&times;</span>
        <h2>Crear perfil</h2>
        <form id="oswa-form-create" onsubmit="enviarFormularioCreacion(event)">
            <div class="oswa-input-group">
                <input type="text" name="name" required placeholder="Nombre del nuevo perfil">
            </div>
            <input type="hidden" name="email" value="user_{{ time() }}@oswa.com">
            <input type="hidden" name="password" value="password123">
            <input type="hidden" name="rol" value="empleado">
            <button type="submit" class="oswa-btn-action">Crear</button>
        </form>
    </div>
</div>

<!-- Estilos Globales de Perfiles -->
<style>
.oswa-netflix-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #141414; z-index: 99999; display: flex; justify-content: center; align-items: center; transition: opacity 0.4s ease, visibility 0.4s; }
.oswa-netflix-overlay.oswa-hidden { opacity: 0; visibility: hidden; pointer-events: none;}
.oswa-netflix-content { text-align: center; animation: zoomIn 0.4s cubic-bezier(0.2, 0.8, 0.2, 1); }
@keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
.oswa-netflix-title { color: #fff; font-size: 2rem; font-weight: 600; margin-bottom: 1.5rem; }
.oswa-netflix-profiles { display: flex; justify-content: center; flex-wrap: wrap; gap: 2vw; margin-bottom: 3rem; }
.oswa-profile-card { position: relative; display: flex; flex-direction: column; align-items: center; cursor: pointer; transition: transform 0.2s; }
.oswa-profile-card:hover .oswa-avatar, .oswa-profile-card:hover .oswa-avatar-initials, .oswa-profile-card:hover .oswa-avatar-img { border: 4px solid white; }
.oswa-profile-card:hover .oswa-name { color: white; }
.oswa-avatar-container { cursor: pointer; }
.oswa-avatar { width: 150px; height: 150px; border-radius: 4px; border: 4px solid transparent; display: flex; justify-content: center; align-items: center; color: white; font-size: 4rem; font-weight: bold; box-sizing: border-box; transition: border 0.2s ease; overflow: hidden; }
.oswa-avatar img, .oswa-avatar-img { width: 150px; height: 150px; border-radius: 4px; border: 4px solid transparent; object-fit: cover; transition: border 0.2s ease; }
#oswa-edit-avatar-preview { background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important; }
.oswa-add-profile { background-color: transparent; border: 2px solid grey !important; color: grey; }
.oswa-profile-card:hover .oswa-add-profile { background-color: white; color: #141414; }
.oswa-name { color: grey; margin-top: 15px; font-size: 1.2rem; transition: color 0.2s; }
.oswa-edit-icon { position: absolute; top: 10px; right: 10px; background: rgba(0, 0, 0, 0.7); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; z-index: 10; font-size: 1.2rem; border: 2px solid white; }
.oswa-edit-icon:hover { background: #E50914; }
.edit-icon-overlay { display: none !important; }
body.manage-mode .edit-icon-overlay { display: flex !important; }
body.manage-mode .oswa-avatar-img, body.manage-mode .oswa-avatar { opacity: 0.5; transition: opacity 0.3s; }
.oswa-btn-manage, .oswa-btn-cancel, .oswa-btn-action, .oswa-btn-delete { background: transparent; border: 1px solid grey; color: grey; padding: 10px 30px; font-size: 1.2rem; cursor: pointer; transition: all 0.2s; margin: 10px; text-transform: uppercase; letter-spacing: 1px;}
.oswa-btn-manage:hover, .oswa-btn-action:hover { color: white; border-color: white; }
.oswa-btn-cancel:hover { background: #333; color: white; border-color: white; }
.oswa-btn-delete:hover { background: #E50914; color: white; border-color: #E50914; }
.oswa-modal { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.85); z-index: 100000; display: flex; justify-content: center; align-items: center; transition: opacity 0.3s ease, visibility 0.3s; }
.oswa-modal.oswa-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.oswa-modal-content { background: #1c1c1c; padding: 2.5rem; border-radius: 8px; width: 90%; max-width: 450px; position: relative; border: 1px solid #333;}

@media (max-width: 576px) {
    .oswa-netflix-overlay { align-items: flex-start; padding-top: 1.5rem; }
    .oswa-netflix-content { width: 100%; padding: 0 1rem; max-height: calc(100vh - 2rem); overflow-y: auto; }
    .oswa-netflix-title { font-size: 1.2rem; margin-top: 0.5rem; margin-bottom: 1rem; }
    .oswa-netflix-profiles { gap: 0.8rem; margin-bottom: 1.5rem; }
    .oswa-avatar, .oswa-avatar-initials, .oswa-avatar-img { width: 80px; height: 80px; font-size: 2rem; }
    .oswa-name { font-size: 0.85rem; margin-top: 0.5rem; }
    .oswa-edit-icon { width: 28px; height: 28px; font-size: 0.8rem; top: 4px; right: 4px; }
    .oswa-btn-manage, .oswa-btn-cancel { font-size: 0.8rem; padding: 8px 16px; margin: 6px; }
    .oswa-btn-cancel.oswa-hidden { display: none; }
    .oswa-netflix-content > div:first-child { margin-bottom: 0 !important; }
    .oswa-netflix-content > div:first-child button { font-size: 0.9rem !important; }
    .oswa-netflix-content > div:first-child button span { font-size: 0.9rem !important; }
}
.oswa-modal-content h2 { color: #fff; font-size: 1.8rem; margin-bottom: 1.5rem; text-align: center;}
.oswa-close { position: absolute; top: 15px; right: 20px; color: grey; font-size: 2.5rem; cursor: pointer; transition: color 0.2s; line-height: 1; z-index: 100001;}
.oswa-close:hover { color: white; }
.oswa-input-group { margin-bottom: 1.5rem; text-align: center;}
.oswa-input-group input[type="text"] { width: 100%; padding: 12px 16px; background: #333; border: 1px solid #444; border-radius: 4px; color: #fff; font-size: 1rem; outline: none; }
.oswa-input-group input[type="text"]:focus { border-color: #E50914; }
.oswa-toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 999999; display: flex; flex-direction: column; gap: 10px; }
.oswa-toast { background: #1c1c1c; color: #fff; padding: 16px 24px; border-radius: 8px; border-left: 4px solid #E50914; box-shadow: 0 8px 30px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 12px; font-size: 1rem; font-weight: 500; animation: toastIn 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); min-width: 300px; max-width: 450px; }
.oswa-toast i { font-size: 1.4rem; color: #E50914; }
.oswa-toast.removing { animation: toastOut 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
@keyframes toastIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
@keyframes toastOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(120%); opacity: 0; } }
</style>
<div class="oswa-toast-container" id="oswa-toast-container"></div>

<!-- Scripts Globales de Perfiles -->
<script>
    const csrfTokenProfile = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    
    // Abrir y cerrar el Overlay gigante
    window.abrirSelectorPerfiles = function(e) {
        if (e) e.preventDefault();

        const menu = document.getElementById('userDropdownMenu');
        if(menu) {
            menu.classList.remove('show');
            menu.style.display = '';
        }
        const sel = document.getElementById('oswa-profile-selector');
        sel.style.display = '';
        sel.classList.remove('oswa-hidden');
    };

    window.cerrarSelectorPerfiles = function() {
        const sel = document.getElementById('oswa-profile-selector');
        sel.classList.add('oswa-hidden');
        sel.style.display = 'none';
    };

    // Cambiar de Cuenta (pide contraseña del perfil al clickear)
    window.seleccionarPerfilConCarga = async function(userId) {
        if(document.body.classList.contains('manage-mode')) return;

        // Cerrar overlay para que SweetAlert sea visible
        cerrarSelectorPerfiles();

        const { value: password } = await Swal.fire({
            title: 'Ingresa la contraseña',
            text: 'Contraseña del perfil para acceder',
            input: 'password',
            inputPlaceholder: 'Contraseña',
            showCancelButton: true,
            confirmButtonText: 'Cambiar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#E50914',
            cancelButtonColor: '#333',
            background: '#121212',
            color: '#fff',
            inputAttributes: { autocapitalize: 'off', autocomplete: 'off' }
        });

        if (!password) return;

        const loader = document.getElementById('oswa-loader');
        if (loader) {
            loader.style.opacity = '1';
            loader.style.visibility = 'visible';
            loader.style.display = 'flex';
        }

        try {
            const response = await fetch('/cambiar-perfil-netflix', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfTokenProfile },
                body: JSON.stringify({ user_id: userId, password })
            });

            if (!response.ok) {
                if (loader) { loader.style.opacity = '0'; loader.style.visibility = 'hidden'; }
                mostrarToast('Error del servidor', 'bi bi-exclamation-triangle-fill');
                return;
            }

            const data = await response.json();

            if (data.success) {
                localStorage.setItem('oswa_switched_to', data.user_name);
                window.location.href = data.redirect || '/dashboard';
            } else {
                if (loader) { loader.style.opacity = '0'; loader.style.visibility = 'hidden'; }
                mostrarToast('Contraseña incorrecta', 'bi bi-shield-exclamation');
            }
        } catch (error) {
            console.error("Error:", error);
            if (loader) { loader.style.opacity = '0'; loader.style.visibility = 'hidden'; }
            mostrarToast('Error de conexión', 'bi bi-exclamation-triangle-fill');
        }
    }

    // Toggle Botón Administrar Perfiles (Versión Global Segura)
    window.activarModoEdicion = function() {
        document.body.classList.add('manage-mode');
        document.getElementById('btn-administrar').style.display = 'none';
        document.getElementById('btn-listo').style.display = 'inline-block';
        const titulo = document.getElementById('titulo-perfiles');
        if (titulo) titulo.innerText = 'Administrar perfiles';
    };

    window.desactivarModoEdicion = function() {
        document.body.classList.remove('manage-mode');
        document.getElementById('btn-listo').style.display = 'none';
        document.getElementById('btn-administrar').style.display = 'inline-block';
        const titulo = document.getElementById('titulo-perfiles');
        if (titulo) titulo.innerText = '¿Quién está gestionando ahora?';
    };

    // Agregar EventListeners si los botones existen
    document.addEventListener("DOMContentLoaded", function() {
        const btnAdministrar = document.getElementById('btn-administrar');
        const btnListo = document.getElementById('btn-listo');
        
        if (btnAdministrar && btnListo) {
            btnAdministrar.addEventListener('click', window.activarModoEdicion);
            btnListo.addEventListener('click', window.desactivarModoEdicion);
        }
    });

    // Modales de Edición y Creación (CORREGIDO EL CIERRE DEL MODAL)
    window.abrirModalCreacion = function() { 
        document.getElementById('oswa-modal-create').classList.remove('oswa-hidden'); 
    }
    
    window.cerrarModalCreacion = function() { 
        document.getElementById('oswa-modal-create').classList.add('oswa-hidden'); 
        document.getElementById('oswa-form-create').reset(); // Limpia el formulario
    }
    
    window.abrirModalEdicion = function(userId, userName) {
        document.getElementById('edit-user-id').value = userId;
        document.getElementById('edit-user-name').value = userName;
        const previewAvatar = document.getElementById('oswa-edit-avatar-preview');
        previewAvatar.innerText = userName.charAt(0).toUpperCase();
        previewAvatar.style.backgroundImage = 'none';
        document.getElementById('oswa-modal-edit').classList.remove('oswa-hidden');
    }
    
    window.cerrarModalEdicion = function() { 
        document.getElementById('oswa-modal-edit').classList.add('oswa-hidden'); 
    }

    window.previewPhoto = function(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('oswa-edit-avatar-preview');
            output.innerText = '';
            output.style.backgroundImage = `url(${reader.result})`;
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    // Peticiones Fetch
    window.enviarFormularioCreacion = async function(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const btnText = btn.innerText;
        btn.innerText = "Creando...";
        btn.disabled = true;
        
        const formData = new FormData(e.target);
        const res = await enviarPeticionPerfil('{{ route("perfil.crear") }}', formData, 'Perfil creado con éxito', 'bi bi-person-plus-fill');
        
        btn.innerText = btnText;
        btn.disabled = false;
    }

    window.enviarFormularioEdicion = async function(e) {
        e.preventDefault();
        const userId = document.getElementById('edit-user-id').value;
        const btn = e.target.querySelector('button[type="submit"]');
        const btnText = btn.innerText;
        btn.innerText = "Guardando...";
        btn.disabled = true;
        
        await enviarPeticionPerfil(`/perfiles/actualizar/${userId}`, new FormData(e.target), 'Perfil actualizado con éxito', 'bi bi-pencil-fill');
        
        btn.innerText = btnText;
        btn.disabled = false;
    }

    window.eliminarPerfil = async function() {
        const userId = document.getElementById('edit-user-id').value;
        const userName = document.getElementById('edit-user-name').value;
        if (confirm(`¿Seguro que deseas eliminar a "${userName}"?`)) {
            await enviarPeticionPerfil(`/perfiles/eliminar/${userId}`, new FormData(), 'Perfil eliminado con éxito', 'bi bi-person-x-fill', 'DELETE');
        }
    }

    async function enviarPeticionPerfil(url, formData, mensajeExito, icono = 'bi bi-check-circle-fill', method = 'POST') {
        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'X-CSRF-TOKEN': csrfTokenProfile, 'Accept': 'application/json' },
                body: method === 'POST' ? formData : null
            });
            const data = await response.json();
            if (data.success) {
                mostrarToast(mensajeExito, icono);
                setTimeout(() => window.location.reload(), 800);
            } else {
                mostrarToast(data.message || 'Error en la operación.', 'bi bi-exclamation-triangle-fill');
            }
            return data;
        } catch (error) {
            mostrarToast('Error de conexión.', 'bi bi-exclamation-triangle-fill');
            return { success: false };
        }
    }
    function mostrarToast(mensaje, icono = 'bi bi-check-circle-fill') {
        const container = document.getElementById('oswa-toast-container');
        if (!container) return;
        const toast = document.createElement('div');
        toast.className = 'oswa-toast';
        toast.innerHTML = `<i class="${icono}"></i><span style="flex:1;">${mensaje}</span><div class="toast-progress"></div>`;
        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }
    window.mostrarToast = mostrarToast;

    // Prevención del error ERR_CACHE_MISS al usar el botón "Atrás" del navegador
    window.addEventListener("pageshow", function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
</script>
