<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - OSWA Inv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #141414; color: #fff; font-family: 'Inter', sans-serif; height: 100vh; overflow: hidden; }
        .chat-container { display: flex; height: 100vh; }
        .sidebar { width: 320px; background: #1a1a1a; border-right: 1px solid rgba(255,255,255,0.06); display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { padding: 1.2rem 1.2rem 0.8rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between; }
        .sidebar-header h5 { font-weight: 700; font-size: 1.1rem; }
        .sidebar-header .back-home { color: #666; text-decoration: none; font-size: 0.85rem; transition: color 0.3s; }
        .sidebar-header .back-home:hover { color: #E50914; }
        .search-box { padding: 0.8rem 1rem; }
        .search-box input { width: 100%; padding: 0.5rem 0.8rem; background: #2a2a2a; border: 1px solid #333; border-radius: 8px; color: #fff; font-size: 0.85rem; }
        .search-box input:focus { outline: none; border-color: #E50914; }
        .conversations { flex: 1; overflow-y: auto; }
        .conv-item { display: flex; align-items: center; gap: 12px; padding: 0.8rem 1rem; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid rgba(255,255,255,0.03); }
        .conv-item:hover { background: rgba(255,255,255,0.05); }
        .conv-item.active { background: rgba(229,9,20,0.12); border-left: 3px solid #E50914; }
        .conv-avatar { width: 42px; height: 42px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; color: #fff; flex-shrink: 0; overflow: hidden; }
        .conv-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .conv-info { flex: 1; min-width: 0; }
        .conv-name { font-size: 0.9rem; font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
        .conv-name small { font-size: 0.7rem; color: #666; font-weight: 400; }
        .conv-preview { font-size: 0.8rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .unread-badge { background: #E50914; color: #fff; font-size: 0.65rem; font-weight: 700; padding: 1px 6px; border-radius: 10px; margin-left: auto; }
        .main-area { flex: 1; display: flex; flex-direction: column; background: #141414; }
        .chat-header { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; gap: 12px; min-height: 68px; }
        .chat-header .h-name { font-weight: 600; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
        .chat-header .h-role { font-size: 0.75rem; color: #666; text-transform: uppercase; letter-spacing: 0.5px; }
        .messages-area { flex: 1; overflow-y: auto; padding: 1rem 1.5rem; display: flex; flex-direction: column; gap: 6px; }
        .msg-row { display: flex; margin-bottom: 4px; }
        .msg-row.sent { justify-content: flex-end; }
        .msg-row.received { justify-content: flex-start; }
        .msg-bubble { max-width: 70%; padding: 0.6rem 1rem; border-radius: 16px; font-size: 0.9rem; line-height: 1.4; word-wrap: break-word; }
        .msg-row.sent .msg-bubble { background: #E50914; color: #fff; border-bottom-right-radius: 4px; }
        .msg-row.received .msg-bubble { background: #2a2a2a; color: #ddd; border-bottom-left-radius: 4px; }
        .msg-bubble img.msg-img { max-width: 100%; border-radius: 8px; margin-top: 4px; cursor: pointer; }
        .msg-bubble audio { width: 100%; margin-top: 4px; }
        .msg-time { font-size: 0.65rem; color: #666; margin-top: 2px; }
        .msg-row.sent .msg-time { text-align: right; }
        .chat-input-area { padding: 0.8rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; gap: 8px; align-items: center; }
        .chat-input-area input[type="text"] { flex: 1; padding: 0.7rem 1rem; background: #2a2a2a; border: 1px solid #333; border-radius: 10px; color: #fff; font-size: 0.9rem; }
        .chat-input-area input[type="text"]:focus { outline: none; border-color: #E50914; }
        .chat-input-area .btn-icon { background: transparent; border: 1px solid #333; border-radius: 10px; color: #aaa; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; cursor: pointer; transition: all 0.3s; flex-shrink: 0; padding: 0; }
        .chat-input-area .btn-icon:hover { border-color: #E50914; color: #E50914; }
        .chat-input-area .btn-icon.recording { background: #E50914; color: #fff; border-color: #E50914; animation: pulse 1s infinite; }
        .chat-input-area .btn-send { background: #E50914; border: none; border-radius: 10px; color: #fff; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; cursor: pointer; transition: background 0.3s; flex-shrink: 0; padding: 0; }
        .chat-input-area .btn-send:hover { background: #b20710; }
        .empty-state { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #444; gap: 12px; }
        .empty-state i { font-size: 4rem; }
        .empty-state p { font-size: 0.95rem; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #333; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
        .presence-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
        .presence-dot.online { background: #00b894; box-shadow: 0 0 6px rgba(0,184,148,0.6); }
        .presence-dot.offline { background: #555; }
        .img-preview { max-height: 120px; max-width: 200px; border-radius: 8px; object-fit: contain; }
        #fileInput { display: none; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
        .rec-time { font-size: 0.8rem; color: #E50914; font-weight: 600; }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h5><i class="bi bi-chat-dots-fill text-danger me-2"></i>Chat</h5>
                <a href="{{ route('inventario') }}" class="back-home"><i class="bi bi-house-fill me-1"></i>Inicio</a>
            </div>
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Buscar...">
            </div>
            <div class="conversations" id="conversationsList"></div>
        </div>

        <div class="main-area">
            <div class="chat-header" id="chatHeader" style="display:none;">
                <div class="conv-avatar" id="headerAvatar" style="width:38px;height:38px;">U</div>
                <div>
                    <div class="h-name" id="headerName">Usuario <span class="presence-dot offline" id="headerPresenceDot"></span></div>
                    <div class="h-role" id="headerRole">empleado</div>
                </div>
            </div>
            <div class="messages-area" id="messagesArea">
                <div class="empty-state" id="emptyState">
                    <i class="bi bi-chat-square-text"></i>
                    <p>Selecciona un usuario para chatear</p>
                </div>
            </div>
            <div class="chat-input-area" id="chatInputArea" style="display:none;">
                <img id="imgPreview" class="img-preview" style="display:none;">
                <button type="button" class="btn-icon" id="btnImage" onclick="document.getElementById('fileInput').click()" title="Enviar imagen"><i class="bi bi-image"></i></button>
                <button type="button" class="btn-icon" id="btnAudio" onclick="toggleGrabar()" title="Grabar audio"><i class="bi bi-mic"></i></button>
                <input type="file" id="fileInput" accept="image/*" onchange="enviarImagen(event)">
                <span class="rec-time" id="recTime" style="display:none;"></span>
                <input type="text" id="messageInput" placeholder="Escribe un mensaje..." maxlength="2000">
                <button type="button" class="btn-send" onclick="enviarMensaje()"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const baseUrl = '{{ url('/') }}';
        let activeUserId = null;
        let pollTimer = null;
        let presenceTimer = null;
        let lastMsgId = 0;
        let onlineIds = [];
        let chatGen = 0;

        // ─── PRESENCIA EN VIVO ───
        async function actualizarPresencia() {
            try {
                const res = await fetch('/api/online-users');
                const data = await res.json();
                onlineIds = data.online_ids || [];
                document.querySelectorAll('.conv-presence-dot').forEach(dot => {
                    const uid = parseInt(dot.dataset.uid);
                    dot.className = 'presence-dot conv-presence-dot ' + (onlineIds.includes(uid) ? 'online' : 'offline');
                });
                if (activeUserId) {
                    const dot = document.getElementById('headerPresenceDot');
                    if (dot) dot.className = 'presence-dot ' + (onlineIds.includes(activeUserId) ? 'online' : 'offline');
                }
            } catch(e) {}
        }

        // ─── CONVERSACIONES ───
        async function cargarConversaciones() {
            try {
                const res = await fetch('/api/chat/conversations');
                const data = await res.json();
                const list = document.getElementById('conversationsList');
                const q = document.getElementById('searchInput').value.toLowerCase();
                list.innerHTML = data.filter(c => c.user.name.toLowerCase().includes(q)).map(c => {
                    const initial = c.user.name.charAt(0).toUpperCase();
                    const img = c.user.profile_photo_path
                        ? `<img src="${baseUrl}/storage/${c.user.profile_photo_path}" alt="" onerror="this.style.display='none';this.parentNode.textContent='${initial}'">`
                        : initial;
                    let preview = c.last_message || 'Sin mensajes';
                    if (c.last_type === 'image') preview = '<i class="bi bi-image"></i> Foto';
                    else if (c.last_type === 'audio') preview = '<i class="bi bi-mic"></i> Audio';
                    else if (preview.length > 30) preview = preview.substring(0,30) + '…';
                    const time = c.last_time ? new Date(c.last_time).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) : '';
                    const isOnline = onlineIds.includes(c.user.id);
                    return `<div class="conv-item ${activeUserId == c.user.id ? 'active' : ''}" data-user-id="${c.user.id}">
                        <div class="conv-avatar">${img}</div>
                        <div class="conv-info">
                            <div class="conv-name"><span class="presence-dot conv-presence-dot ${isOnline ? 'online' : 'offline'}" data-uid="${c.user.id}"></span> ${c.user.name} <small>${time}</small></div>
                            <div class="conv-preview">${preview}</div>
                        </div>
                        ${c.unread > 0 ? `<span class="unread-badge">${c.unread}</span>` : ''}
                    </div>`;
                }).join('');
                if (data.length === 0) list.innerHTML = '<div class="text-center py-4" style="color:#444;font-size:0.9rem;">No hay usuarios disponibles</div>';
            } catch(e) {}
        }

        // ─── SELECCIONAR CHAT ───
        async function seleccionarChat(rawId) {
            const userId = Number(rawId);
            if (userId === activeUserId) return;
            const gen = ++chatGen;
            if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
            if (presenceTimer) { clearInterval(presenceTimer); presenceTimer = null; }
            activeUserId = userId;
            const es = document.getElementById('emptyState');
            if (es) es.style.display = 'none';
            document.getElementById('chatHeader').style.display = 'flex';
            document.getElementById('chatInputArea').style.display = 'flex';
            document.getElementById('messagesArea').innerHTML = '';

            try {
                const res = await fetch('/api/chat/conversations');
                const data = await res.json();
                if (gen !== chatGen) return;
                const conv = data.find(c => Number(c.user.id) === userId);
                if (conv) {
                    document.getElementById('headerName').innerHTML = `${conv.user.name} <span class="presence-dot ${onlineIds.includes(userId) ? 'online' : 'offline'}" id="headerPresenceDot"></span>`;
                    document.getElementById('headerRole').textContent = conv.user.rol || 'empleado';
                    const avatar = document.getElementById('headerAvatar');
                    if (conv.user.profile_photo_path) {
                        avatar.innerHTML = `<img src="${baseUrl}/storage/${conv.user.profile_photo_path}" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none';this.parentNode.textContent='${conv.user.name.charAt(0).toUpperCase()}'">`;
                    } else {
                        avatar.textContent = conv.user.name.charAt(0).toUpperCase();
                    }
                }
            } catch(e) {}

            if (gen !== chatGen) return;
            await cargarMensajes(userId);
            if (gen !== chatGen) return;
            pollTimer = setInterval(() => cargarMensajes(userId, true), 4000);
            presenceTimer = setInterval(actualizarPresencia, 10000);
        }

        // ─── MENSAJES ───
        function renderMessageHTML(m) {
            const sent = m.sender_id == {{ Auth::id() }};
            const time = new Date(m.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
            let content = '';
            if (m.type === 'image' && m.file_path) {
                content = `<img src="${baseUrl}/storage/${m.file_path}" class="msg-img" onclick="window.open(this.src)" alt="Foto"><div class="msg-time">${time}</div>`;
            } else if (m.type === 'audio' && m.file_path) {
                content = `<audio controls src="${baseUrl}/storage/${m.file_path}"></audio><div class="msg-time">${time}</div>`;
            } else {
                content = `${m.message}<div class="msg-time">${time}</div>`;
            }
            return `<div class="msg-bubble">${content}</div>`;
        }

        async function cargarMensajes(userId, isPoll = false) {
            try {
                const res = await fetch('/api/chat/messages/' + userId);
                if (isPoll && Number(userId) !== activeUserId) return;
                const msgs = await res.json();
                if (isPoll && Number(userId) !== activeUserId) return;
                const area = document.getElementById('messagesArea');
                if (msgs.length > 0) lastMsgId = msgs[msgs.length - 1].id;

                if (!isPoll) {
                    area.innerHTML = msgs.map(m => {
                        const sent = m.sender_id == {{ Auth::id() }};
                        return `<div class="msg-row ${sent ? 'sent' : 'received'}">${renderMessageHTML(m)}</div>`;
                    }).join('');
                    area.scrollTop = area.scrollHeight;
                } else {
                    const newMsgs = msgs.filter(m => m.id > lastMsgId);
                    newMsgs.forEach(m => {
                        const sent = m.sender_id == {{ Auth::id() }};
                        const div = document.createElement('div');
                        div.className = 'msg-row ' + (sent ? 'sent' : 'received');
                        div.innerHTML = renderMessageHTML(m);
                        area.appendChild(div);
                    });
                    if (newMsgs.length > 0) {
                        area.scrollTop = area.scrollHeight;
                        lastMsgId = newMsgs[newMsgs.length - 1].id;
                    }
                }
            } catch(e) { console.error('cargarMensajes', e); }
        }

        // ─── ENVIAR TEXTO ───
        async function enviarMensaje() {
            const input = document.getElementById('messageInput');
            const msg = input.value.trim();
            if (!msg || !activeUserId) return;
            input.value = '';
            try {
                const res = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ receiver_id: activeUserId, message: msg })
                });
                const data = await res.json();
                if (data.success) {
                    const area = document.getElementById('messagesArea');
                    const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                    const div = document.createElement('div');
                    div.className = 'msg-row sent';
                    div.innerHTML = `<div class="msg-bubble">${msg}<div class="msg-time">${time}</div></div>`;
                    area.appendChild(div);
                    area.scrollTop = area.scrollHeight;
                    lastMsgId = data.message.id;
                    cargarConversaciones();
                }
            } catch(e) { console.error('enviarMensaje', e); }
        }

        // ─── ENVIAR IMAGEN ───
        async function enviarImagen(event) {
            const file = event.target.files[0];
            if (!file || !activeUserId) return;
            event.target.value = '';
            const formData = new FormData();
            formData.append('file', file);
            formData.append('receiver_id', activeUserId);
            formData.append('type', 'image');
            try {
                const res = await fetch('/api/chat/upload', {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: formData
                });
                const data = await res.json();
                if (data.success) {
                    const area = document.getElementById('messagesArea');
                    const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                    const div = document.createElement('div');
                    div.className = 'msg-row sent';
                    div.innerHTML = `<div class="msg-bubble"><img src="${baseUrl}/storage/${data.message.file_path}" class="msg-img" onclick="window.open(this.src)"><div class="msg-time">${time}</div></div>`;
                    area.appendChild(div);
                    area.scrollTop = area.scrollHeight;
                    lastMsgId = data.message.id;
                    cargarConversaciones();
                }
            } catch(e) { console.error('enviarImagen', e); }
        }

        // ─── GRABAR AUDIO ───
        let mediaRecorder = null;
        let audioChunks = [];
        let recording = false;
        let recTimer = null;
        let recSeconds = 0;

        async function toggleGrabar() {
            const btn = document.getElementById('btnAudio');
            const recTime = document.getElementById('recTime');
            if (recording) {
                if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                    mediaRecorder.stop();
                }
                return;
            }
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Tu navegador no soporta grabación de audio');
                return;
            }
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];
                recording = true;
                btn.classList.add('recording');
                btn.innerHTML = '<i class="bi bi-stop-fill"></i>';
                recSeconds = 0;
                recTime.style.display = 'inline';
                recTime.textContent = '0:00';
                recTimer = setInterval(() => {
                    recSeconds++;
                    const m = Math.floor(recSeconds / 60);
                    const s = recSeconds % 60;
                    recTime.textContent = m + ':' + (s < 10 ? '0' : '') + s;
                }, 1000);

                mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
                mediaRecorder.onstop = async () => {
                    clearInterval(recTimer);
                    recTime.style.display = 'none';
                    btn.classList.remove('recording');
                    btn.innerHTML = '<i class="bi bi-mic"></i>';
                    recording = false;
                    stream.getTracks().forEach(t => t.stop());

                    const blob = new Blob(audioChunks, { type: 'audio/webm' });
                    if (blob.size < 100) return;
                    const formData = new FormData();
                    formData.append('file', blob, 'audio_' + Date.now() + '.webm');
                    formData.append('receiver_id', activeUserId);
                    formData.append('type', 'audio');
                    try {
                        const res = await fetch('/api/chat/upload', {
                            method: 'POST', headers: { 'X-CSRF-TOKEN': csrf }, body: formData
                        });
                        const data = await res.json();
                        if (data.success) {
                            const area = document.getElementById('messagesArea');
                            const time = new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                            const div = document.createElement('div');
                            div.className = 'msg-row sent';
                            div.innerHTML = `<div class="msg-bubble"><audio controls src="${baseUrl}/storage/${data.message.file_path}"></audio><div class="msg-time">${time}</div></div>`;
                            area.appendChild(div);
                            area.scrollTop = area.scrollHeight;
                            lastMsgId = data.message.id;
                            cargarConversaciones();
                        }
                    } catch(e) { console.error('enviarAudio', e); }
                };
                mediaRecorder.start();
            } catch(e) {
                alert('No se pudo acceder al micrófono');
                console.error(e);
            }
        }

        // ─── EVENTOS ───
        document.getElementById('messageInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') enviarMensaje();
        });
        document.getElementById('searchInput').addEventListener('input', cargarConversaciones);
        document.getElementById('conversationsList').addEventListener('click', function(e) {
            const item = e.target.closest('.conv-item');
            if (item) seleccionarChat(item.dataset.userId);
        });

        cargarConversaciones();
        setInterval(cargarConversaciones, 7000);
        actualizarPresencia();
        setInterval(actualizarPresencia, 15000);
    </script>
</body>
</html>
