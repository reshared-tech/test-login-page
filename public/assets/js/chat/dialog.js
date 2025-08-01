const box = document.getElementById('messages');
const input = document.getElementById('input');
const tip = document.getElementById('tip');
const sendBtn = document.getElementById('send-btn');
let oldestMessageId = 0;
let latestMessageId = 0;
let readMessageIds = [];
let unreadMessageIds = [];

function fetchMessages(toBottom, latest) {
    if (!chatId) {
        console.error('no chat id founded');
        return;
    }

    fetch('/api/fetch', {
        method: 'POST', body: JSON.stringify({
            id: chatId,
            latest,
            oldestMessageId,
            latestMessageId,
            readMessageIds,
            unreadMessageIds,
        })
    })
        .then(res => res.json())
        .then(res => {
            if (res.code === 10000) {
                if (res.unread && Object.keys(res.unread).length > 0) {
                    Object.keys(res.unread).map(id => {
                        if (res.unread[id] === 0) {
                            const index = unreadMessageIds.indexOf(id);
                            if (index > -1) {
                                unreadMessageIds.splice(index, 1);
                            }
                        }

                        const dom = document.getElementById(`read-${id}`);
                        if (dom) {
                            dom.innerText = res.unread[id] === 0 ? '既読です' : (res.unread[id] === 1 ? '未読です' : `${res.unread[id]}人未読です`);
                        }
                    })
                }

                if (res.data && res.data.length > 0) {
                    readMessageIds = [];
                    const tmp = res.data.map(message => {
                        if (oldestMessageId === 0 || oldestMessageId > message.id) {
                            oldestMessageId = message.id;
                        }
                        if (latestMessageId === 0 || latestMessageId < message.id) {
                            latestMessageId = message.id;
                        }

                        if (message.me) {
                            if (message.read > 0) {
                                unreadMessageIds.push(message.id);
                            } else {
                                const index = unreadMessageIds.indexOf(message.id);
                                if (index > -1) {
                                    unreadMessageIds.splice(index, 1);
                                }
                            }
                            const readStatus = message.read === 0 ? '既読です' : (message.read === 1 ? '未読です' : `${message.read}人未読です`);
                            return `<div class="message-right"><span id="read-${message.id}" class="status">${readStatus}</span><div class="message-me"><pre class="content">${message.content}</pre><p class="time">${message.created_at}</p></div></div>`;
                        } else {
                            if (!message.read) {
                                readMessageIds.push(message.id);
                            }
                            return `<div class="message-other"><div class="name">${message.name}:</div><div class="content-box"><pre class="content">${message.content}</pre><p class="time">${message.created_at}</p></div></div>`
                        }
                    }).join('');

                    if (box.innerHTML.trim() === '') {
                        box.innerHTML = '<div class="center-box" onclick="fetchMessages(false, false)"><button class="action-btn">more history</button></div><hr>' + tmp;
                    } else if (latest) {
                        box.innerHTML += tmp;
                    } else {
                        const old = box.innerHTML.split('<hr>');
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

        e.target.innerText = '送信中です...';
        const val = input.value.trim();
        if (val === '') {
            if (tip) {
                tip.innerText = 'メッセージを入力してください';
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
                    e.target.innerText = '送信';

                    if (res.code === 10000) {
                        latestMessageId = res.data.message_id;
                        unreadMessageIds.push(res.data.message_id);
                        box.innerHTML += `<div class="message-right"><span id="read-${res.data.message_id}" class="status">未読です</span><div class="message-me"><pre class="content">${res.data.content}</pre><p class="time">${res.data.created_at}</p></div></div>`;
                        input.value = '';
                        box.scrollTo({
                            top: box.scrollHeight,
                        });
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

fetchMessages(true, false);

setInterval(function () {
    fetchMessages(true, true);
}, 3000);