let chatRelations = [];

for (let a of document.getElementsByClassName('new-chat')) {
    a.addEventListener('click', function (e) {
        if (chatRelations.length === 0) {
            chatRelations.push(e.target.dataset.id);

            e.target.innerText = 'キャンセル'
            e.target.previousElementSibling.innerText = '起動する他のユーザーを選択してください、または';
            e.target.previousElementSibling.style.display = 'inline-block';
            return;
        }
        if (chatRelations.length === 1) {
            if (chatRelations[0] === e.target.dataset.id) {
                chatRelations = [];
                e.target.innerText = '新しいチャット'
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
        e.target.innerText = 'チャット'
        e.target.previousElementSibling.style.display = 'none';
    });
}