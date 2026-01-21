<?php

class TrelloService
{
    private $apiKey;
    private $token;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/trello.php';
        $this->apiKey = $config['api_key'];
        $this->token = $config['token'];
    }

    public function call($endpoint, $method = 'GET', $data = [])
    {
        $url = "https://api.trello.com/1/{$endpoint}";
        $data['key'] = $this->apiKey;
        $data['token'] = $this->token;

        $ch = curl_init();
        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function getBoards()
    {
        return $this->call('members/me/boards');
    }

    public function getBoardByName($name)
    {
        $boards = $this->getBoards();
        foreach ($boards as $board) {
            if ($board['name'] === $name) {
                return $board;
            }
        }
        return null;
    }

    public function getLists($boardId)
    {
        return $this->call("boards/{$boardId}/lists");
    }

    public function getListByName($boardId, $name)
    {
        $lists = $this->getLists($boardId);
        foreach ($lists as $list) {
            if ($list['name'] === $name) {
                return $list;
            }
        }
        return null;
    }

    public function getCards($listId)
    {
        return $this->call("lists/{$listId}/cards", 'GET', [
            'fields' => 'id,name,desc,due,labels,idMembers',
            'members' => 'true',
            'member_fields' => 'fullName'
        ]);
    }

    public function getCard($cardId)
    {
        return $this->call("cards/{$cardId}", 'GET', [
            'fields' => 'id,name,desc,due,labels,idMembers,dateLastActivity',
            'members' => 'true',
            'member_fields' => 'fullName',
            'attachments' => 'true'
        ]);
    }

    public function moveCard($cardId, $listId)
    {
        return $this->call("cards/{$cardId}", 'PUT', ['idList' => $listId]);
    }

    public function markCardComplete($cardId)
    {
        return $this->call("cards/{$cardId}", 'PUT', ['dueComplete' => 'true']);
    }

    public function getOpenTickets()
    {
        $board = $this->getBoardByName('SUPPORT TEAM');
        if (!$board)
            return [];

        $targetLists = ['Tickets Awaiting Response', 'Open Tickets', 'To-Do (TODAY)', 'To-Do (This Week)'];
        $allCards = [];

        $lists = $this->getLists($board['id']);
        foreach ($lists as $list) {
            if (in_array($list['name'], $targetLists)) {
                $cards = $this->getCards($list['id']);
                foreach ($cards as $card) {
                    $card['list_name'] = $list['name'];
                    $allCards[] = $card;
                }
            }
        }
        return $allCards;
    }
}
