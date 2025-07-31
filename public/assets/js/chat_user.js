function fetchChats() {
    fetch('/api/chats').then(res => res.json()).then(res => {
        if (res.code === 10000) {
            document.getElementById('all-unread').innerText = `未読メッセージが ${res.data.unread} 件あります`;

            document.getElementById('chats').innerHTML = res.data.chats.map(chat => {
                if (chat.unread > 0) {
                    return `<div class="chat"><div class="name">${chat.name} <span class="unread">${chat.unread}</span></div><span>${chat.updated_at}</span></div>`;
                } else {
                    return `<div class="chat"><span class="name">${chat.name}</span> <span>${chat.updated_at}</span></div>`;
                }
            }).join('');
        } else {
            document.getElementById('all-unread').innerText = res.data.message;
            document.getElementById('chats').innerHTML = '';
        }
    })
}

fetchChats();