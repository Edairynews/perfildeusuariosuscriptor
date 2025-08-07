document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab-nav li');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            contents.forEach(c => {
                c.classList.remove('active');
                if (c.id === target) c.classList.add('active');
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    let table = document.getElementById('mailchimp-lists');
    let addButton = document.getElementById('add-list');

    addButton.addEventListener('click', function() {
        let index = table.querySelectorAll('tr').length;
        let row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" name="pum_mailchimp_lists[${index}][id]" placeholder="List ID" size="30">
                <input type="text" name="pum_mailchimp_lists[${index}][name]" placeholder="Nombre de la lista" size="30">
                <button type="button" class="remove-list button">Quitar</button>
            </td>
        `;
        table.appendChild(row);
    });

    table.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-list')) {
            e.target.closest('tr').remove();
        }
    });
});


document.addEventListener('DOMContentLoaded', function() {
    var botones = document.querySelectorAll('.btn-suscribirme');
    botones.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var listId = btn.getAttribute('data-list-id');

            fetch('/wp-admin/admin-ajax.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'action=pum_suscribirme_ajax&list_id=' + encodeURIComponent(listId)
            })
            .then(response => response.json())
            .then(data => {
                var msgDiv = document.getElementById('suscripcion-msg-' + listId);
                if (msgDiv) {
                    msgDiv.innerHTML = data.message;
                }
            })
            .catch(err => {
                var msgDiv = document.getElementById('suscripcion-msg-' + listId);
                if (msgDiv) {
                    msgDiv.innerHTML = 'Error al suscribirse';
                }
            });
        });
    });
});
