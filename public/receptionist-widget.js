(function () {
    'use strict';

    var script = document.currentScript;
    if (!script) return;

    var params = new URL(script.src).searchParams;
    var companyId = params.get('company_id');
    var siteKey = params.get('site_key');
    var apiBase = params.get('api') || new URL(script.src).origin;
    var position = params.get('position') === 'left' ? 'left' : 'right';

    if (!companyId || !siteKey) {
        console.error('AI Receptionist widget: company_id and site_key are required.');
        return;
    }

    var state = {
        config: null,
        open: false,
        loading: false
    };

    var root = document.createElement('div');
    root.id = 'ai-receptionist-widget';
    root.innerHTML = [
        '<style>',
        '#airw-launcher{position:fixed;bottom:24px;' + position + ':24px;z-index:2147483000;border:0;border-radius:999px;padding:13px 18px;background:#0d6efd;color:#fff;font:600 14px system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;box-shadow:0 8px 30px rgba(0,0,0,.18);cursor:pointer}',
        '#airw-panel{display:none;position:fixed;bottom:84px;' + position + ':24px;width:min(380px,calc(100vw - 32px));height:min(620px,calc(100vh - 120px));z-index:2147483000;background:#fff;border:1px solid #dee2e6;border-radius:18px;box-shadow:0 18px 55px rgba(0,0,0,.2);overflow:hidden;font:14px system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;color:#212529}',
        '#airw-header{background:#0d6efd;color:#fff;padding:16px;display:flex;justify-content:space-between;align-items:center}',
        '#airw-title{font-weight:700}#airw-close{border:0;background:transparent;color:#fff;font-size:22px;cursor:pointer}',
        '#airw-messages{height:calc(100% - 166px);overflow-y:auto;padding:14px;background:#f8f9fa}',
        '.airw-msg{max-width:86%;padding:10px 12px;border-radius:14px;margin:0 0 10px;line-height:1.45;white-space:pre-wrap}',
        '.airw-ai{background:#fff;border:1px solid #e9ecef;margin-right:auto}.airw-user{background:#0d6efd;color:#fff;margin-left:auto}',
        '#airw-form{border-top:1px solid #dee2e6;padding:10px;background:#fff}',
        '#airw-fields{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-bottom:7px}',
        '#airw-fields input,#airw-message{box-sizing:border-box;width:100%;border:1px solid #ced4da;border-radius:8px;padding:9px;font:inherit}',
        '#airw-message{resize:none;height:58px;margin-bottom:7px}',
        '#airw-send{width:100%;border:0;border-radius:8px;padding:10px;background:#0d6efd;color:#fff;font-weight:600;cursor:pointer}',
        '#airw-send:disabled{opacity:.6;cursor:not-allowed}',
        '#airw-booking{display:none;margin-bottom:7px;padding:8px;border:1px solid #dee2e6;border-radius:8px;background:#f8f9fa}',
        '#airw-booking label{display:block;font-size:12px;font-weight:600;margin-bottom:4px}',
        '#airw-booking input{width:100%;box-sizing:border-box;border:1px solid #ced4da;border-radius:7px;padding:7px}',
        '@media(max-width:480px){#airw-launcher{bottom:16px;' + position + ':16px}#airw-panel{bottom:74px;' + position + ':8px;width:calc(100vw - 16px);height:calc(100vh - 92px)}}',
        '</style>',
        '<button id="airw-launcher" type="button">Chat with us</button>',
        '<section id="airw-panel" aria-label="AI Receptionist chat">',
        '<header id="airw-header"><span id="airw-title">AI Receptionist</span><button id="airw-close" type="button" aria-label="Close">×</button></header>',
        '<div id="airw-messages"></div>',
        '<form id="airw-form">',
        '<div id="airw-fields"><input id="airw-name" placeholder="Your name" required><input id="airw-email" type="email" placeholder="Email"><input id="airw-phone" type="tel" placeholder="Phone"></div>',
        '<div id="airw-booking"><label for="airw-date">Request an appointment (optional)</label><input id="airw-date" type="datetime-local"></div>',
        '<textarea id="airw-message" placeholder="How can we help?" required></textarea>',
        '<button id="airw-send" type="submit">Send message</button>',
        '</form></section>'
    ].join('');
    document.body.appendChild(root);

    var launcher = document.getElementById('airw-launcher');
    var panel = document.getElementById('airw-panel');
    var close = document.getElementById('airw-close');
    var messages = document.getElementById('airw-messages');
    var form = document.getElementById('airw-form');
    var send = document.getElementById('airw-send');
    var booking = document.getElementById('airw-booking');

    function addMessage(text, type) {
        var el = document.createElement('div');
        el.className = 'airw-msg ' + (type === 'user' ? 'airw-user' : 'airw-ai');
        el.textContent = text;
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
    }

    function setOpen(open) {
        state.open = open;
        panel.style.display = open ? 'block' : 'none';
        launcher.style.display = open ? 'none' : 'block';
        if (open && !state.config) loadConfig();
    }

    async function loadConfig() {
        try {
            var response = await fetch(apiBase + '/api/receptionist/widget?company_id=' + encodeURIComponent(companyId), {
                headers: { 'Accept': 'application/json', 'X-Receptionist-Key': siteKey }
            });
            var data = await response.json();
            if (!response.ok || !data.enabled) throw new Error('Receptionist unavailable');
            state.config = data;
            document.getElementById('airw-title').textContent = data.display_name;
            booking.style.display = data.booking_enabled ? 'block' : 'none';
            if (data.greeting) addMessage(data.greeting, 'ai');
        } catch (error) {
            addMessage('Sorry, the receptionist is currently unavailable. Please contact the business directly.', 'ai');
        }
    }

    launcher.addEventListener('click', function () { setOpen(true); });
    close.addEventListener('click', function () { setOpen(false); });

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (state.loading) return;

        var name = document.getElementById('airw-name').value.trim();
        var email = document.getElementById('airw-email').value.trim();
        var message = document.getElementById('airw-message').value.trim();
        var requestedStart = document.getElementById('airw-date').value;
        var phone = document.getElementById('airw-phone')?.value.trim() || '';

        if (!name || !message) return;

        addMessage(message, 'user');
        document.getElementById('airw-message').value = '';
        state.loading = true;
        send.disabled = true;
        send.textContent = 'Thinking…';

        try {
            var response = await fetch(apiBase + '/api/receptionist/chat?company_id=' + encodeURIComponent(companyId), {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Receptionist-Key': siteKey
                },
                body: JSON.stringify({
                    company_id: Number(companyId),
                    customer_name: name,
                    customer_email: email || null,
                    customer_phone: phone || null,
                    message: message,
                    requested_start: requestedStart || null
                })
            });

            var data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || 'Request failed');
            }

            addMessage(data.reply || 'Thanks. We have received your message.', 'ai');

            if (data.appointment_id && data.booked_at) {
                document.getElementById('airw-date').value = '';
            }
        } catch (error) {
            addMessage('Sorry, I could not process that message right now. Please try again or contact the business directly.', 'ai');
        } finally {
            state.loading = false;
            send.disabled = false;
            send.textContent = 'Send message';
        }
    });
})();
