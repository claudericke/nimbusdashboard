# Trello Integration - Code to Add to index.php

## Step 1: Add require statement at the top (after helpers.php)
Add this line after `require_once 'helpers.php';`:

```php
require_once 'trello.php';
```

## Step 2: Add Tasks menu item to sidebar navigation
Find the Admin menu section in the sidebar (around line with "Manage Users" and "Manage Quotes").
Add this BEFORE the Admin menu section:

```php
<?php if ($isSuperuser): ?>
<li class="nav-item">
    <a class="nav-link <?php echo ($page === 'tasks') ? 'active' : ''; ?>" href="?page=tasks">
        <i class="fas fa-tasks"></i> Tasks
    </a>
</li>
<?php endif; ?>
```

## Step 3: Add Tasks page handler
Find the main page switch statement (where it handles 'dashboard', 'domains', 'emails', etc.).
Add this case BEFORE the 'admin' case:

```php
case 'tasks':
    if (!$isSuperuser) {
        header('Location: ?page=dashboard');
        exit;
    }
    
    try {
        $boards = trello_get_boards();
        $selectedBoard = $_GET['board'] ?? ($boards[0]['id'] ?? null);
        $lists = $selectedBoard ? trello_get_lists($selectedBoard) : [];
    } catch (Exception $e) {
        $boards = [];
        $lists = [];
        $trelloError = $e->getMessage();
    }
    ?>
    <div class="bento-grid">
        <div class="card full-width p-4">
            <div class="card-body">
                <h2 class="card-title text-highlight text-white mb-4">Trello Tasks</h2>
                
                <?php if (isset($trelloError)): ?>
                    <div class="alert alert-danger"><?php echo h($trelloError); ?></div>
                <?php endif; ?>
                
                <?php if (!empty($boards)): ?>
                    <div class="mb-4">
                        <label class="form-label text-white">Select Board:</label>
                        <select class="form-select" onchange="window.location.href='?page=tasks&board=' + this.value">
                            <?php foreach ($boards as $board): ?>
                                <option value="<?php echo h($board['id']); ?>" <?php echo ($selectedBoard === $board['id']) ? 'selected' : ''; ?>>
                                    <?php echo h($board['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if (!empty($lists)): ?>
                        <div class="row">
                            <?php foreach ($lists as $list): ?>
                                <?php
                                try {
                                    $cards = trello_get_cards($list['id']);
                                } catch (Exception $e) {
                                    $cards = [];
                                }
                                ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="text-white mb-3"><?php echo h($list['name']); ?> (<?php echo count($cards); ?>)</h5>
                                            <?php if (!empty($cards)): ?>
                                                <ul class="list-unstyled">
                                                    <?php foreach ($cards as $card): ?>
                                                        <li class="mb-2 p-2" style="background: rgba(255,255,255,0.05); border-radius: 4px;">
                                                            <a href="<?php echo h($card['url']); ?>" target="_blank" class="text-white text-decoration-none">
                                                                <?php echo h($card['name']); ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">No cards</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-white">No Trello boards found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    break;
```

## Summary
1. Add `require_once 'trello.php';` at top of index.php
2. Add Tasks menu item in sidebar (superuser only)
3. Add Tasks page case in main switch statement

The Tasks page will:
- Show dropdown to select Trello board
- Display all lists from selected board in 3-column grid
- Show cards in each list with links to Trello
- Only accessible to superusers
