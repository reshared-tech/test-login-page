const box = document.getElementById('messages'); // Message container
let oldestMessageId = 0; // ID of the oldest loaded message
let latestMessageId = 0; // ID of the latest loaded message

function fetchMessages(toBottom, latest) {
    if (!chatId) {
        console.error('No chat ID found');
        return;
    }

    // Request payload
    const payload = {
        id: chatId,
        latest,
        oldestMessageId,
        latestMessageId,
    };

    fetch('api/fetch', {
        method: 'POST',
        body: JSON.stringify(payload),
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(res => res.json())
        .then(res => {
            if (res.code !== 10000) return;

            if (res.data && res.data.length > 0) {
                processMessages(res.data, toBottom, latest);
            }
        })
        .catch(err => console.error('Error fetching messages:', err));
}

function processMessages(messages, toBottom, latest) {
    readMessageIds = [];
    const messageHTML = messages.map(message => {
        updateMessageIdTrackers(message.id);

        return processOtherMessage(message);
    }).join('');

    updateMessageContainer(messageHTML, latest);

    if (toBottom) {
        scrollToBottom();
    }
}

/**
 * Updates the oldest and latest message ID trackers
 * @param {number} messageId - The message ID to check
 */
function updateMessageIdTrackers(messageId) {
    if (oldestMessageId === 0 || oldestMessageId > messageId) {
        oldestMessageId = messageId;
    }
    if (latestMessageId === 0 || latestMessageId < messageId) {
        latestMessageId = messageId;
    }
}

/**
 * Processes a message sent by another user
 * @param {Object} message - The message object
 * @returns {string} HTML string for the message
 */
function processOtherMessage(message) {
    if (!message.read) {
        readMessageIds.push(message.id);
    }
    return `
        <div class="message-other">
            <div class="name">${message.name}:</div>
            <div class="content-box">
                <pre class="content">${message.content}</pre>
                <p class="time">${message.created_at}</p>
            </div>
        </div>`;
}

/**
 * Updates the message container with new messages
 * @param {string} html - HTML string of new messages
 * @param {boolean} latest - Whether these are latest messages
 */
function updateMessageContainer(html, latest) {
    if (box.innerHTML.trim() === '') {
        box.innerHTML = `
            <div class="center-box" onclick="fetchMessages(false, false)">
                <button class="action-btn">more history</button>
            </div>
            <hr>${html}`;
    } else if (latest) {
        box.innerHTML += html;
    } else {
        const old = box.innerHTML.split('<hr>');
        box.innerHTML = old[0] + '<hr>' + html + old[1];
    }
    addImagesListener();
}

function addImagesListener() {
    const imgs = document.getElementsByClassName('chat-img');
    if (imgs.length > 0) {
        for (let k in imgs) {
            imgs.item(k).addEventListener('click', function (e) {
                window.open(e.target.src);
                e.target.classList = ['chat-img2'];
            });
        }
    }
}

fetchMessages();