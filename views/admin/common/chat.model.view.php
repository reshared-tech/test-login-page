<div id="modal" class="modal">
    <div class="content">
        <div class="header">
            <h3><?= $modalTitle ?? 'Members' ?></h3>

            <svg id="close-modal" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </div>

        <form class="form" id="form" method="post" action="admin/chat">
            <div class="item">
                <label for="name" class="label"><?= __('Chat Room Name') ?></label>
                <input id="name" type="text" name="name" class="input"
                       placeholder="<?= __('Please input name of the new chat.') ?>" required>
                <p class="tip for-name"></p>
            </div>

            <div class="item">
                <label for="select" class="label"><?= __('Select members') ?></label>
                <select id="select" class="input"></select>
                <p class="tip for-select"></p>
            </div>
        </form>

        <div id="users-box">

        </div>

        <button onclick="createChat()" class="action-btn">確認します</button>
    </div>
</div>