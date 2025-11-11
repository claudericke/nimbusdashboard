<?php

class TicketController extends BaseController {
    private $trelloService;
    private $boardId;
    private $lists = [];

    public function __construct() {
        $this->trelloService = new TrelloService();
        $config = require __DIR__ . '/../../config/trello.php';
        
        $board = $this->trelloService->getBoardByName($config['board_name']);
        $this->boardId = $board['id'] ?? null;

        if ($this->boardId) {
            $allLists = $this->trelloService->getLists($this->boardId);
            foreach ($allLists as $list) {
                $this->lists[$list['name']] = $list['id'];
            }
        }
    }

    public function newTickets() {
        $this->requireSuperuser();

        $tickets = [];
        if (isset($this->lists['New Ticket'])) {
            $tickets = $this->trelloService->getCards($this->lists['New Ticket']);
        }

        $this->view('tickets/new', ['tickets' => $tickets]);
    }

    public function openTickets() {
        $this->requireSuperuser();

        $tickets = [];
        if (isset($this->lists['Open Tickets'])) {
            $tickets = $this->trelloService->getCards($this->lists['Open Tickets']);
        }

        $this->view('tickets/open', ['tickets' => $tickets]);
    }

    public function awaitingTickets() {
        $this->requireSuperuser();

        $tickets = [];
        if (isset($this->lists['Tickets Awaiting Response'])) {
            $tickets = $this->trelloService->getCards($this->lists['Tickets Awaiting Response']);
        }

        $this->view('tickets/awaiting', ['tickets' => $tickets]);
    }

    public function closedTickets() {
        $this->requireSuperuser();

        $tickets = [];
        if (isset($this->lists['Closed Tickets'])) {
            $tickets = $this->trelloService->getCards($this->lists['Closed Tickets']);
        }

        $this->view('tickets/closed', ['tickets' => $tickets]);
    }

    public function closeTicket() {
        $this->requireSuperuser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $cardId = $_POST['card_id'] ?? '';
        
        if (empty($cardId) || !isset($this->lists['Closed Tickets'])) {
            $this->json(['success' => false, 'message' => 'Invalid card ID'], 400);
        }

        $this->trelloService->moveCard($cardId, $this->lists['Closed Tickets']);
        $this->trelloService->markCardComplete($cardId);

        $this->json(['success' => true, 'message' => 'Ticket closed successfully']);
    }

    public function checkNew() {
        $this->requireSuperuser();

        $lastCheck = Session::get('last_ticket_check', []);
        $newTickets = [];

        if (isset($this->lists['Open Tickets'])) {
            $tickets = $this->trelloService->getCards($this->lists['Open Tickets']);
            
            foreach ($tickets as $ticket) {
                if (!in_array($ticket['id'], $lastCheck)) {
                    $newTickets[] = [
                        'id' => $ticket['id'],
                        'name' => $ticket['name']
                    ];
                }
            }

            $currentIds = array_column($tickets, 'id');
            Session::set('last_ticket_check', $currentIds);
        }

        $this->json(['new_tickets' => $newTickets]);
    }

    public function getCard($id) {
        $this->requireSuperuser();

        $card = $this->trelloService->getCard($id);
        
        if (!$card) {
            $this->json(['success' => false, 'message' => 'Card not found'], 404);
        }

        $this->json(['success' => true, 'card' => $card]);
    }
}
