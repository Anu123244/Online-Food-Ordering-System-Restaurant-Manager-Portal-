function request(url, method, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, url, true);
    if (method.toUpperCase() === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    }
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            try {
                callback(JSON.parse(xhr.responseText));
            } catch (e) {
                alert('Invalid server response');
            }
        }
    };
    xhr.send(data);
}

function statusLabel(status) {
    const labels = {
        pending: 'Pending',
        accepted: 'Accepted',
        preparing: 'Preparing',
        ready: 'Ready for Pickup',
        cancelled: 'Rejected / Cancelled'
    };
    return labels[status] || status;
}

function actionButtons(order) {
    return `
        <div class="actions">
            <button onclick="updateOrderStatus(${order.id}, 'accepted')">Accept</button>
            <button onclick="updateOrderStatus(${order.id}, 'preparing')">Preparing</button>
            <button onclick="updateOrderStatus(${order.id}, 'ready')">Ready for Pickup</button>
            <button class="danger" onclick="updateOrderStatus(${order.id}, 'cancelled')">Reject</button>
        </div>
    `;
}

function loadManagerOrders() {
    request('api/orders.php?action=list', 'GET', null, function (response) {
        if (!response.success) {
            alert(response.message || 'Could not load orders');
            return;
        }

        const box = document.getElementById('ordersList');
        if (!box) return;

        if (response.orders.length === 0) {
            box.innerHTML = '<p>No active orders right now.</p>';
            return;
        }

        const statuses = ['pending', 'accepted', 'preparing', 'ready'];
        const grouped = { pending: [], accepted: [], preparing: [], ready: [] };
        response.orders.forEach(order => {
            if (grouped[order.status]) grouped[order.status].push(order);
        });

        box.innerHTML = statuses.map(status => `
            <div class="status-column">
                <h4>${statusLabel(status)}</h4>
                ${grouped[status].length === 0 ? '<p class="muted">No orders</p>' : grouped[status].map(order => `
                    <article class="order-card">
                        <h4>Order #${order.id}</h4>
                        <p><b>Customer:</b> ${escapeHtml(order.customer_name)}</p>
                        <p><b>Items:</b> ${escapeHtml(order.item_summary || 'No items')}</p>
                        <p><b>Address:</b> ${escapeHtml(order.delivery_address)}</p>
                        <p><b>Total:</b> ৳${Number(order.total_amount).toFixed(2)}</p>
                        ${actionButtons(order)}
                    </article>
                `).join('')}
            </div>
        `).join('');
    });
}

function updateOrderStatus(orderId, status) {
    const data = `action=update_status&order_id=${encodeURIComponent(orderId)}&status=${encodeURIComponent(status)}`;
    request('api/orders.php', 'POST', data, function (response) {
        if (!response.success) {
            alert(response.message || 'Update failed');
        }
        loadManagerOrders();
    });
}

function toggleMenuItem(itemId) {
    const data = `action=toggle_item&item_id=${encodeURIComponent(itemId)}`;
    request('api/orders.php', 'POST', data, function (response) {
        alert(response.message || 'Updated');
        location.reload();
    });
}

function escapeHtml(str) {
    return String(str || '').replace(/[&<>"']/g, function (m) {
        return ({'&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;'})[m];
    });
}

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('ordersList')) {
        loadManagerOrders();
        setInterval(loadManagerOrders, 10000);
    }
});
