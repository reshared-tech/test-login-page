const createBtn = document.getElementById('create-chat');
const modal = document.getElementById('modal');
const closeBtn = document.getElementById('close-modal');
const optionsBox = document.getElementById('select');
const usersBox = document.getElementById('users-box');
const form = document.getElementById('form');
const name = document.getElementById('name');
let users = {};
let selectedUser = {};
let chatId = 0;

if (createBtn) {
    createBtn.addEventListener('click', function () {
        modal.style.display = 'flex';
        loadUsers();
    });
}

if (closeBtn) {
    closeBtn.addEventListener('click', function () {
        modal.style.display = 'none';
    });
}


if (optionsBox) {
    optionsBox.addEventListener('input', function (e) {
        if (e.target.value === '0') {
            return;
        }
        if (selectedUser[e.target.value]) {
            delete selectedUser[e.target.value];
        } else {
            selectedUser[e.target.value] = users[e.target.value];
        }
        updateOptions();
    });
}

if (name) {
    name.addEventListener('input', function () {
        const tip = document.getElementsByClassName('for-name').item(0);
        tip.innerText = '';
        tip.style.display = 'none';
    });
}

function loadUsers(selected = []) {
    selectedUser = {};
    if (Object.keys(users).length === 0) {
        fetch('admin/api/users').then(res => res.json()).then(res => {
            res.map(item => {
                users[item.id] = item;
                if (selected.indexOf(item.id) > -1) {
                    selectedUser[item.id] = item;
                }
            })
            updateOptions();
        });
    } else {
        Object.keys(users).map(id => {
            if (selected.indexOf(id) > -1) {
                selectedUser[item.id] = item;
            }
        })
        updateOptions();
    }
}

function removeUser(id) {
    delete selectedUser[id];
    updateOptions();
}

function createChat() {
    let error = false;
    if (name.value.trim() === '') {
        const tip = document.getElementsByClassName('for-name').item(0);
        tip.innerText = '会話名を入力願います。';
        tip.style.display = 'block';
        error = true;
    }
    if (Object.keys(selectedUser).length < 2) {
        const tip = document.getElementsByClassName('for-select').item(0);
        tip.innerText = '最低2人のユーザーをお願いします。';
        tip.style.display = 'block';
        error = true;
    }
    if (error) {
        return;
    }

    const body = {
        name: name.value.trim(),
        users: Object.keys(selectedUser),
    };
    if (typeof theChatId !== 'undefined' && theChatId) {
        body.id = theChatId;
    }

    fetch(form.action, {
        method: 'POST',
        body: JSON.stringify(body)
    }).then(res => res.json()).then(res => {
        if (res.code === 10000) {
            alert(res.message);
            window.location.reload();
        } else {
            const tip = document.getElementsByClassName('for-select').item(0);
            tip.innerText = res.message;
            tip.style.display = 'block';
        }
    })
}

function manageMember() {
    if (!theChatId) {
        return;
    }
    if (theChatName) {
        document.getElementById('name').value = theChatName;
    }
    chatId = theChatId;
    modal.style.display = 'flex';
    loadUsers(chatUserIds);
}

function updateOptions() {
    if (!optionsBox) {
        return;
    }
    const options = ['<option value="0">Please select</option>'];
    Object.keys(users).map(id => {
        const item = users[id];
        if (selectedUser[id]) {
            options.push(`<option value="${id}">(SELECTED) ${item.name} [${item.email}]</option>`)
        } else {
            options.push(`<option value="${id}">${item.name} [${item.email}]</option>`)
        }
    });
    optionsBox.innerHTML = options.join('');

    const usersShow = [];
    Object.keys(selectedUser).map(id => {
        const user = selectedUser[id];
        if (user) {
            usersShow.push(`<div class="one-user">
<div class="avatar">${user.name.charAt(0).toUpperCase()}</div>
<div class="username"><span>${user.name}</span><span class="email">${user.email}</span></div>
<span onclick="removeUser(${id})" class="close">除去します</span>
</div>`);
        }
    });
    usersBox.innerHTML = usersShow.join('');

    const tip = document.getElementsByClassName('for-select').item(0);
    tip.innerText = '';
    tip.style.display = 'none';
}