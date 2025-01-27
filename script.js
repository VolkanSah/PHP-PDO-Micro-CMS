// Für index.html
document.addEventListener('DOMContentLoaded', function() {
    fetch('/micro_cms.php?action=list')
       .then(response => response.json())
       .then(data => {
            const pageList = document.getElementById('pages');
            data.forEach(page => {
                const pageElement = document.createElement('p');
                pageElement.innerHTML = `<b>${page.title}</b> - <a href="view.html?id=${page.id}">Ansehen</a> | <a href="edit.html?id=${page.id}">Bearbeiten</a> | <a href="/micro_cms.php?action=delete&id=${page.id}">Löschen</a>`;
                pageList.appendChild(pageElement);
            });
        })
       .catch(error => console.error('Error:', error));
});

// Für create.html
document.getElementById('create-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    
    fetch('/micro_cms.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ action: 'create', title, content }),
    })
       .then(response => response.json())
       .then(data => console.log('Seite erstellt:', data))
       .catch(error => console.error('Error:', error));
});
