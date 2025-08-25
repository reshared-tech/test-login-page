const lockModal = document.getElementById('lock-modal');
const question = document.getElementById('question');
let action = 'true';

function lockChat() {
    console.log(theChatId);
    if (!theChatId) {
        return;
    }
    action = 'lock';
    question.innerText = 'Confirm to lock this chat?';
    lockModal.style.display = 'flex';
}

function unLockChat() {
    if (!theChatId) {
        return;
    }
    action = 'unlock';
    question.innerText = 'Confirm to unlock this chat?';
    lockModal.style.display = 'flex';
}

function deleteChat() {
    if (!theChatId) {
        return;
    }
    action = 'delete';
    question.innerText = 'Confirm to delete this chat?';
    lockModal.style.display = 'flex';
}

function confirm() {
    if (!theChatId) {
        return;
    }
    if (action === 'delete') {
        fetch(`admin/api/chats/${theChatId}`, {
            method: 'DELETE'
        }).then(res => res.json()).then(res => {
            if (res.code === 10000) {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
        return;
    }
    if (action === 'lock' || action === 'unlock') {
        const fd = new FormData();
        fd.append('id', `${theChatId}`);
        fd.append('lock', action === 'lock' ? 'true' : 'false');
        fetch('admin/api/chats/lock', {
            method: 'POST', body: fd,
        }).then(res => res.json()).then(res => {
            if (res.code === 10000) {
                window.location.reload();
            } else {
                alert(res.message);
            }
        });
    }
}

function cancel() {
    lockModal.style.display = 'none';
}