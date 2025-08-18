const createBtn = document.getElementById('create-chat');
const modal = document.getElementById('modal');
const closeBtn = document.getElementById('close-modal');
const optionsBox = document.getElementById('select');
const usersBox = document.getElementById('users-box');
const form = document.getElementById('form');
const name = document.getElementById('name');
let users = {};
let selectedUser = {};

createBtn.addEventListener('click', function () {
    modal.style.display = 'flex';

    if (Object.keys(users).length === 0) {
        fetch('admin/api/users').then(res => res.json()).then(res => {
            res.map(item => {
                users[item.id] = item;
            })
            selectedUser = {};
            updateOptions();
        });
    } else {
        selectedUser = {};
        updateOptions();
    }
});

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

    fetch(form.action, {
        method: 'POST',
        body: JSON.stringify({
            name: name.value.trim(),
            users: Object.keys(selectedUser),
        })
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

function updateOptions() {
    const options = ['<option value="0">Please select</option>'];
    Object.keys(users).map(id => {
        const item = users[id];
        if (selectedUser[id]) {
            options.push(`<option value="${id}">(SELECTED) ${item.name} [${item.email}]</option>`)
        } else {
            options.push(`<option value="${id}">${item.name} [${item.email}]</option>`)
        }
    })
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

closeBtn.addEventListener('click', function () {
    modal.style.display = 'none';
});

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

name.addEventListener('input', function () {
    const tip = document.getElementsByClassName('for-name').item(0);
    tip.innerText = '';
    tip.style.display = 'none';
});