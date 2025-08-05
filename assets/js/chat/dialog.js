// DOM elements
const box = document.getElementById('messages'); // Message container
const input = document.getElementById('input'); // Message input field
const tip = document.getElementById('tip'); // Tip/error message element
const sendBtn = document.getElementById('send-btn'); // Send button
const upload = document.getElementById('upload'); // Upload element
const uploadBtn = document.getElementById('upload-btn'); // Upload button

// Message tracking variables
let oldestMessageId = 0; // ID of the oldest loaded message
let latestMessageId = 0; // ID of the latest loaded message
let readMessageIds = []; // Array of message IDs marked as read
let unreadMessageIds = []; // Array of message IDs marked as unread

/**
 * Fetches messages from the server
 * @param {boolean} toBottom - Whether to scroll to bottom after loading
 * @param {boolean} latest - Whether to fetch only latest messages
 */
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
        readMessageIds,
        unreadMessageIds,
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

            // Update unread message statuses
            if (res.unread && Object.keys(res.unread).length > 0) {
                updateUnreadStatuses(res.unread);
            }

            // Process new messages
            if (res.data && res.data.length > 0) {
                processMessages(res.data, toBottom, latest);
            }
        })
        .catch(err => console.error('Error fetching messages:', err));
}

/**
 * Updates the read/unread status of messages
 * @param {Object} unreadData - Object containing message IDs and their read status
 */
function updateUnreadStatuses(unreadData) {
    Object.keys(unreadData).forEach(id => {
        if (unreadData[id] === 0) {
            const index = unreadMessageIds.indexOf(id);
            if (index > -1) {
                unreadMessageIds.splice(index, 1);
            }
        }

        const dom = document.getElementById(`read-${id}`);
        if (dom) {
            dom.innerText = getReadStatusText(unreadData[id]);
        }
    });
}

/**
 * Returns appropriate text for read status
 * @param {number} readCount - Number of readers
 * @returns {string} Status text
 */
function getReadStatusText(readCount) {
    if (readCount === 0) return '既読です'; // Read
    if (readCount === 1) return '未読です'; // Unread
    return `${readCount}人未読です`; // X people unread
}

/**
 * Processes received messages and updates the UI
 * @param {Array} messages - Array of message objects
 * @param {boolean} toBottom - Whether to scroll to bottom
 * @param {boolean} latest - Whether these are latest messages
 */
function processMessages(messages, toBottom, latest) {
    readMessageIds = [];
    const messageHTML = messages.map(message => {
        updateMessageIdTrackers(message.id);

        if (message.me) {
            return processOwnMessage(message);
        } else {
            return processOtherMessage(message);
        }
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
 * Processes a message sent by the current user
 * @param {Object} message - The message object
 * @returns {string} HTML string for the message
 */
function processOwnMessage(message) {
    if (message.read > 0) {
        unreadMessageIds.push(message.id);
    } else {
        const index = unreadMessageIds.indexOf(message.id);
        if (index > -1) {
            unreadMessageIds.splice(index, 1);
        }
    }

    const readStatus = getReadStatusText(message.read);
    return `
        <div class="message-right">
            <span id="read-${message.id}" class="status">${readStatus}</span>
            <div class="message-me">
                <pre class="content">${message.content}</pre>
                <p class="time">${message.created_at}</p>
            </div>
        </div>`;
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

/**
 * Scrolls the message container to the bottom
 */
function scrollToBottom() {
    box.scrollTo({
        top: box.scrollHeight,
        behavior: 'smooth'
    });
}

function addImagesListener() {
    const imgs = document.getElementsByClassName('chat-img');
    for (let k in imgs) {
        imgs.item(k).addEventListener('click', function (e) {
            window.open(e.target.src);
            e.target.classList = ['chat-img2'];
        });
    }
}

// Send message event handler
if (sendBtn) {
    sendBtn.addEventListener('click', function (e) {
        if (!input || e.target.disabled) return;

        const val = input.value.trim();
        if (val === '') {
            showTip('メッセージを入力してください');
            return;
        }

        sendMessage(e, val);
    });
}

/**
 * Shows a tip/error message
 * @param {string} message - The message to display
 */
function showTip(message) {
    if (tip) {
        tip.innerText = message;
        tip.style.display = 'block';
    }
}

/**
 * Sends a message to the server
 * @param {Event} e - The click event
 * @param {string} message - The message content
 */
function sendMessage(e, message) {
    e.target.innerText = '送信中です...'; // Sending...
    e.target.disabled = true;

    const fd = new FormData();
    fd.append('chat_id', chatId);
    fd.append('message', message);

    fetch('api/messages', {
        method: 'POST',
        body: fd,
    })
        .then(res => res.json())
        .then(res => {
            e.target.disabled = false;
            e.target.innerText = '送信'; // Send

            if (res.code === 10000) {
                handleSuccessfulSend(res.data);
            } else {
                showTip(res.message);
            }
        })
        .catch(err => {
            console.error('Error sending message:', err);
            e.target.disabled = false;
            e.target.innerText = '送信'; // Send
            showTip('送信に失敗しました'); // Failed to send
        });
}

/**
 * Handles successful message sending
 * @param {Object} data - Response data from server
 */
function handleSuccessfulSend(data) {
    latestMessageId = data.message_id;
    unreadMessageIds.push(data.message_id);

    box.innerHTML += `
        <div class="message-right">
            <span id="read-${data.message_id}" class="status">未読です</span>
            <div class="message-me">
                <pre class="content">${data.content}</pre>
                <p class="time">${data.created_at}</p>
            </div>
        </div>`;

    input.value = '';
    scrollToBottom();
    addImagesListener();
}

// Input field event listeners
if (input) {
    input.addEventListener('input', function () {
        if (tip) tip.style.display = 'none';
    });

    input.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && e.shiftKey) {
            sendBtn.click();
        }
    });
}

// Initial fetch and periodic updates
fetchMessages(true, false);
setInterval(() => fetchMessages(true, true), 1000);

if (upload) {
    upload.addEventListener('change', function (e) {
        if (e.target.files.length === 0) {
            return;
        }
        const formData = new FormData();
        formData.append('chat_id', chatId);
        for (let i = 0; i < e.target.files.length; i++) {
            formData.append("files[]", e.target.files[i]);
        }
        fetch('api/upload', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(res => {
                if (res.code === 10000) {
                    handleSuccessfulSend(res.data);
                } else {
                    showTip(res.message);
                }
            })
            .catch(err => {
                console.error('Error uploading message:', err);
                showTip('画像送信に失敗しました'); // Failed to send
            });
    });
}

if (uploadBtn) {
    uploadBtn.addEventListener('click', function () {
        upload.click();
    })
}