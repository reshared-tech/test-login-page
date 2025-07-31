let chatRelations = [];

for (let a of document.getElementsByClassName('start-chat')) {
    a.addEventListener('click', function (e) {
        if (chatRelations.length === 0) {
            chatRelations.push(e.target.dataset.id);

            e.target.innerText = 'Cancel'
            e.target.previousElementSibling.innerText = 'Please select another user to start, or ';
            e.target.previousElementSibling.style.display = 'inline-block';
            return;
        }
        if (chatRelations.length === 1) {
            if (chatRelations[0] === e.target.dataset.id) {
                chatRelations = [];
                e.target.innerText = 'Chat'
                e.target.previousElementSibling.style.display = 'none';
                return;
            }
            chatRelations.push(e.target.dataset.id);
            const res = fetch('/admin/chat', {
                method: 'POST', body: JSON.stringify(chatRelations)
            }).then(res => res.json()).then(res => {
                alert(res.message);
                window.location.reload()
            });
            return;
        }
        chatRelations = [];
        e.target.innerText = 'Chat'
        e.target.previousElementSibling.style.display = 'none';
    });
}