<?php
// DON’T. STEAL. MY. CODE. You think copying someone else's work and slapping your name on it makes you clever? No,  it makes you a colossal Asshole. 
// Respect the free work and the credits of developers or you will pay in future for codes like this! 
// Copyright Volkan Kücükbudak
// Thanks!

// Configuration
$dsn = 'ysql:host=localhost;dbname=micro_cms';
$username = 'your_database_username';
$password = 'your_database_password';

// Connecting to the database
try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: '. $e->getMessage();
    exit();
}

// Function to list all pages
function listPages($pdo) {
    $stmt = $pdo->prepare('SELECT * FROM pages ORDER BY created_at DESC');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to create a new page
function createPage($pdo, $title, $content) {
    $stmt = $pdo->prepare('INSERT INTO pages (title, content) VALUES (:title, :content)');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    return $stmt->execute();
}

// Function to update a page
function updatePage($pdo, $id, $title, $content) {
    $stmt = $pdo->prepare('UPDATE pages SET title = :title, content = :content WHERE id = :id');
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':content', $content);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// Function to delete a page
function deletePage($pdo, $id) {
    $stmt = $pdo->prepare('DELETE FROM pages WHERE id = :id');
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

// Simple Routing for Demonstration
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'list') {
        $pages = listPages($pdo);
        echo '<h1>Pages</h1>';
        foreach ($pages as $page) {
            echo "<p><b>{$page['title']}</b> - <a href='?action=view&id={$page['id']}'>View</a> | <a href='?action=edit&id={$page['id']}'>Edit</a> | <a href='?action=delete&id={$page['id']}'>Delete</a></p>";
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'view' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM pages WHERE id = :id');
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($page) {
            echo "<h1>{$page['title']}</h1>";
            echo "<p>{$page['content']}</p>";
        } else {
            echo 'Page not found.';
        }
    } elseif (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM pages WHERE id = :id');
        $stmt->bindParam(':id', $_GET['id']);
        $stmt->execute();
        $page = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($page) {
           ?>
            <h1>Edit Page</h1>
            <form action="?action=update&id=<?= $_GET['id']?>" method="post">
                <label>Title:</label>
                <input type="text" name="title" value="<?= $page['title']?>"><br><br>
                <label>Content:</label>
                <textarea name="content"><?= $page['content']?></textarea><br><br>
                <input type="submit" value="Update">
            </form>
            <?php
        } else {
            echo 'Page not found.';
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['action']) && $_GET['action'] === 'update' && isset($_GET['id'])) {
        if (updatePage($pdo, $_GET['id'], $_POST['title'], $_POST['content'])) {
            echo 'Page updated successfully.';
        } else {
            echo 'Failed to update page.';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'create') {
        if (createPage($pdo, $_POST['title'], $_POST['content'])) {
            echo 'Page created successfully.';
        } else {
            echo 'Failed to create page.';
        }
    }
}

// Simple Form to Create a New Page
if (!isset($_GET['action']) || $_GET['action']!== 'edit') {
   ?>
    <h1>Create New Page</h1>
    <form action="?action=create" method="post">
        <input type="hidden" name="action" value="create">
        <label>Title:</label>
        <input type="text" name="title"><br><br>
        <label>Content:</label>
        <textarea name="content"></textarea><br><br>
        <input type="submit" value="Create">
    </form>
    <p><a href="?action=list">View All Pages</a></p>
    <?php
}

// Handle Delete (Should ideally be a POST action for consistency and security, but demonstrating simplicity)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (deletePage($pdo, $_GET['id'])) {
        echo 'Page deleted successfully.';
    } else {
        echo 'Failed to delete page.';
    }
    echo '<p><a href="?action=list">Back to List</a></p>';
}
