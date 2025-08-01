// Array to store selected user IDs for chat initiation
let chatRelations = [];

// Get all elements with 'new-chat' class and add click event listeners
for (let a of document.getElementsByClassName('new-chat')) {
    a.addEventListener('click', function (e) {

        // First selection case (no users selected yet)
        if (chatRelations.length === 0) {
            chatRelations.push(e.target.dataset.id);

            // Update UI
            e.target.innerText = 'キャンセル'
            e.target.previousElementSibling.innerText = '起動する他のユーザーを選択してください、または';
            e.target.previousElementSibling.style.display = 'inline-block';
            return;
        }


        // Second selection case (one user already selected)
        if (chatRelations.length === 1) {

            // If clicking the same button again (cancel action)
            if (chatRelations[0] === e.target.dataset.id) {
                chatRelations = [];
                e.target.innerText = '新しいチャット'
                e.target.previousElementSibling.style.display = 'none';
                return;
            }

            // Add second user and initiate chat
            chatRelations.push(e.target.dataset.id);

            // Send request to server to create chat
            fetch('/admin/chat', {
                method: 'POST', body: JSON.stringify(chatRelations)
            }).then(res => res.json()).then(res => {
                alert(res.message);
                window.location.reload()
            });
            return;
        }

        // Reset if somehow more than one user is selected
        chatRelations = [];
        e.target.innerText = 'チャット'
        e.target.previousElementSibling.style.display = 'none';
    });
}