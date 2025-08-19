const modal = document.getElementById('modal');
let userId = 0;
let lock = 'true';

function lockUser(uid) {
    userId = uid;
    lock = 'true';
    document.getElementById('question').innerText = 'Confirm to lock this user?';
    modal.style.display = 'flex';
}

function unLockUser(uid) {
    userId = uid;
    lock = 'false';
    document.getElementById('question').innerText = 'Confirm to unlock this user?';
    modal.style.display = 'flex';
}

function confirm() {
    if (!userId) {
        return;
    }
    const fd = new FormData();
    fd.append('id', userId);
    fd.append('lock', lock);
    fetch('admin/api/users/lock', {
        method: 'POST', body: fd,
    }).then(res => res.json()).then(res => {
        if (res.code === 10000) {
            window.location.reload();
        } else {
            alert(res.message);
        }
    });
}

function cancel() {
    modal.style.display = 'none';
    userId = 0;
}