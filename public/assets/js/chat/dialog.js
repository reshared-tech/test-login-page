const box = document.getElementById('messages');
const input = document.getElementById('input');
const tip = document.getElementById('tip');
const sendBtn = document.getElementById('send-btn');
let lastMessageId = 0;

function fetchMessages(toBottom) {
    if (!chatId) {
        console.error('no chat id founded');
        return;
    }

    const query = new URLSearchParams({
        id: chatId,
        lastMessageId,
    }).toString();
    fetch('/api/messages?' + query)
        .then(res => res.json())
        .then(res => {
            if (res.code === 10000) {
                if (box && res.data && res.data.length > 0) {
                    const tmp = res.data.map(message => {
                        if (lastMessageId === 0 || lastMessageId > message.id) {
                            lastMessageId = message.id;
                        }

                        if (message.me) {
                            return `<div class="message-right"><span class="status">read</span><div class="message-me"><pre class="content">${message.content}</pre><p class="time">${message.created_at}</p></div></div>`;
                        } else {
                            return `<div class="message-other"><div class="name">${message.name}:</div><div class="content-box"><pre class="content">${message.content}</pre><p class="time">${message.created_at}</p></div></div>`
                        }
                    }).join('');

                    if (box.innerHTML.trim() === '') {
                        box.innerHTML = '<div class="center-box" onclick="fetchMessages(false)"><button class="action-btn">more history</button></div><hr>' + tmp;
                    } else {
                        const old = box.innerHTML.split('<hr>');
                        console.log(old);
                        box.innerHTML = old[0] + '<hr>' + tmp + old[1];
                    }

                    if (toBottom) {
                        box.scrollTo({
                            top: box.scrollHeight,
                        });
                    }
                }
            }
        });
}

if (sendBtn) {
    sendBtn.addEventListener('click', function (e) {
        if (!input || e.target.disabled) {
            return;
        }

        e.target.innerText = 'Sending...';
        const val = input.value.trim();
        if (val === '') {
            if (tip) {
                tip.innerText = 'Please input your message';
                tip.style.display = 'block';
            }
        } else {
            e.target.disabled = true;
            const fd = new FormData();
            fd.append('chat_id', chatId);
            fd.append('message', input.value.trim());
            fetch('/api/messages', {
                method: 'POST',
                body: fd,
            }).then(res => res.json())
                .then(res => {
                    e.target.disabled = false;
                    e.target.innerText = 'Send';

                    if (res.code === 10000) {
                        const time = now();
                        box.innerHTML += `<div class="message-right"><div class="message-me"><pre class="content">${input.value.trim()}</pre><p class="time">${time}</p></div></div>`;
                        input.value = '';
                    } else {
                        if (tip) {
                            tip.innerText = res.message;
                            tip.style.display = 'block';
                        }
                    }
                });
        }
    });
}

if (input) {
    input.addEventListener('input', function () {
        if (tip) {
            tip.style.display = 'none';
        }
    });
    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && e.shiftKey) {
            sendBtn.click();
        }
    })
}

function now() {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');

    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

fetchMessages(true);