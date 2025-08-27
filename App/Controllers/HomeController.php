<?php

namespace App\Controllers;

use App\Models\ChatModel;
use App\Models\UserModel;

/**
 * Controller for handling the user-side home page
 * Displays paginated list of chats the current user is part of, with unread counts and latest messages
 * Inherits core features (auth, validation, language) from the base Controller class
 */
class HomeController extends Controller
{
    /**
     * Display the home page with the user's chat list
     * Handles pagination, chat data fetching, and enhancement with unread counts/latest messages
     *
     * @return void
     */
    public function index()
    {
        // Define number of chat items to display per page (pagination size)
        $size = 10;

        // Get current page number from URL query parameters:
        // - Use validator to retrieve "page" parameter (defaults to 1 if not provided)
        // - Ensure page number is at least 1 to avoid invalid pagination (e.g., page 0 or negative)
        $page = max($this->validator->number($_GET, 'page', 1), 1);

        // Initialize models for chat and user operations
        $model = new ChatModel();
        $userModel = new UserModel();

        // Get total number of chats the current authorized user is part of (for pagination)
        $total = $model->getChatTotalByUserId(authorizedUser('id'));

        // Fetch paginated chat list if there are existing chats; else set to empty array
        if ($total > 0) {
            $chats = $model->getChatByUserId(authorizedUser('id'), $page, $size);
        } else {
            $chats = [];
        }

        // Enhance chat data with additional metadata (if chats exist)
        if (!empty($chats)) {
            // Extract all chat IDs from the fetched chat list (for batch operations)
            $chatIds = array_column($chats, 'id');

            // Calculate unread message count for each chat (specific to current user)
            $unreadMap = $model->calcUnread(authorizedUser('id'), $chatIds);

            // Fetch the latest message for each chat (to show preview)
            $lastMessages = $model->getLastMessages($chatIds);

            // Add metadata to each chat item (for frontend rendering)
            foreach ($chats as $k => $chat) {
                // Get latest message content (empty string if no message exists)
                $content = $lastMessages[$chat['id']] ?? '';

                // Replace image HTML with "[image]" placeholder (for cleaner preview)
                if (strpos($content, '<img ') === 0) {
                    $content = '[image]';
                }

                // Add chat metadata:
                $chats[$k]['user'] = $chat['name'];                  // Chat name (used as "user" label for frontend consistency)
                $chats[$k]['avatar'] = nameAvatar($chat['name']);   // Generate text avatar from chat name
                $chats[$k]['unread'] = $unreadMap[$chat['id']] ?? 0;// Unread message count (0 if no unread)
                $chats[$k]['content'] = $content;                   // Latest message preview (or "[image]")
                $chats[$k]['updated_at'] = timeHuman($chat['updated_at']); // Human-readable time (e.g., "2 hours ago")
            }
        }

        // Load and render the home page with chat list and pagination data
        view('home', [
            'title' => 'Welcome',  // Page title for the home page
            'chats' => $chats,     // Enhanced chat list with metadata
            'page' => $page,       // Current page number (for pagination navigation)
            'size' => $size,       // Number of chats per page (for pagination logic)
            'total' => $total      // Total number of chats (for pagination UI)
        ]);
    }
}