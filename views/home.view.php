<?php require APP_ROOT . '/views/basic/head.view.php' ?>

    <div class="container">
        <div class="card mini-card">
            <!-- Logged-in state view -->
            <div class="header">
                <h1 class="title"><?= __('Welcome') ?></h1>
                <p class="subtitle"><?= __('Hi') ?>, <?= htmlspecialchars(authorizedUser('name')) ?></p>
                <div style="display:flex; justify-content:space-around;">
                    <a href="profile" class="link"><?= __('Profile') ?></a>
                    <a href="password" class="link"><?= __('Password') ?></a>
                    <a href="logout" class="link"><?= __('Log out') ?></a>
                </div>
            </div>

            <div class="flex flex-col">
                <?php foreach ($chats as $k => $chat): ?>
                    <?php if ($k > 0): ?>
                        <div class="divider"></div>
                    <?php endif ?>
                    <a href="chats?h=<?= $chat['hash'] ?>"
                       class="one-chat flex items-center justify-between px-4 py-3 hover-bg-gray-50 transition cursor-pointer">
                        <div class="flex items-center">
                            <div class="avatar bg-blue-500 text-white rounded-full flex items-center justify-center font-medium mr-3">
                                <?= $chat['avatar'] ?>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-base font-semibold text-gray-700"><?= $chat['user'] ?></span>
                                <span class="text-sm text-gray-500 truncate"
                                      style="max-width: 180px;"><?= $chat['content'] ?></span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="time text-gray-500 mb-2"><?= $chat['updated_at'] ?></span>
                            <?php if ($chat['unread'] > 0): ?>
                                <div class="unread-count bg-blue-500 text-white rounded-full flex items-center justify-center">
                                    <?= $chat['unread'] ?>
                                </div>
                            <?php endif ?>
                        </div>
                    </a>
                <?php endforeach ?>

                <div class="flex justify-around">
                    <?php if ($pre): ?>
                        <a href="<?= pageUrl($pre) ?>" class="text">上一页</a>
                    <?php endif ?>
                    <?php if ($next): ?>
                        <a href="<?= pageUrl($next) ?>" class="text">下一页</a>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>

<?php require APP_ROOT . '/views/basic/foot.view.php' ?>