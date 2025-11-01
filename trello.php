<?php
// Trello API Helper Functions

function trello_call($endpoint, $method = 'GET', $data = []) {
    $apiKey = $_ENV['TRELLO_API_KEY'] ?? '';
    $token = $_ENV['TRELLO_TOKEN'] ?? '';
    
    if (empty($apiKey) || empty($token)) {
        throw new Exception('Trello API credentials not configured');
    }
    
    $url = 'https://api.trello.com/1' . $endpoint;
    $url .= (strpos($url, '?') !== false ? '&' : '?') . 'key=' . $apiKey . '&token=' . $token;
    
    if ($method === 'GET' && !empty($data)) {
        $url .= '&' . http_build_query($data);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    if ($method !== 'GET' && !empty($data)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception('Trello API error: HTTP ' . $httpCode);
    }
    
    return json_decode($response, true);
}

function trello_get_boards() {
    return trello_call('/members/me/boards');
}

function trello_get_lists($boardId) {
    return trello_call('/boards/' . $boardId . '/lists');
}

function trello_get_cards($listId) {
    return trello_call('/lists/' . $listId . '/cards?fields=name,desc,due,start,labels,idMembers&members=true');
}

function trello_move_card($cardId, $listId) {
    return trello_call('/cards/' . $cardId, 'PUT', ['idList' => $listId]);
}

function trello_get_board_by_name($boardName) {
    $boards = trello_get_boards();
    foreach ($boards as $board) {
        if (strcasecmp($board['name'], $boardName) === 0) {
            return $board;
        }
    }
    return null;
}

function trello_get_list_by_name($boardId, $listName) {
    $lists = trello_get_lists($boardId);
    foreach ($lists as $list) {
        if (strcasecmp($list['name'], $listName) === 0) {
            return $list;
        }
    }
    return null;
}

function trello_get_card($cardId) {
    return trello_call('/cards/' . $cardId . '?fields=name,desc,due,labels,members,attachments&attachments=true&members=true');
}
