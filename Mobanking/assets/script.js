document.addEventListener('DOMContentLoaded', () => {
    console.log('Banking system is ready!');
    
    // --- Admin Dashboard Logic ---
    // These functions handle the custom delete confirmation modal
    // They are available globally for the admin_dashboard.php file to call.
    window.showDeleteModal = function(userId) {
        const modal = document.getElementById('delete-modal');
        const confirmBtn = document.getElementById('confirm-delete-btn');
        if (modal && confirmBtn) {
            confirmBtn.href = `index.php?page=admin_dashboard&action=delete&id=${userId}`;
            modal.style.display = 'flex';
        }
    };

    window.hideDeleteModal = function() {
        const modal = document.getElementById('delete-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    };
});

document.addEventListener('DOMContentLoaded', function() {
    // Get the notification button and the message container
    const notificationButton = document.getElementById('notification-button');
    const messageContainer = document.getElementById('message-container');
    const notificationDot = document.querySelector('.notification-dot');

    // Check if the elements exist before adding the event listener
    if (notificationButton && messageContainer) {
        notificationButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior
            
            // Toggle the display of the message container
            if (messageContainer.style.display === 'none' || messageContainer.style.display === '') {
                messageContainer.style.display = 'block';
                // Remove the dot when the user clicks the notification
                if(notificationDot) {
                    notificationDot.style.display = 'none';
                }
            } else {
                messageContainer.style.display = 'none';
            }
        });
    }
});

