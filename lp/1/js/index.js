// Get all list items in the navigation
const navItems = document.getElementById('nav').getElementsByTagName('li');
// Add click event listeners to each navigation item
for (let i = 0; i < navItems.length; i++) {
    navItems[i].addEventListener('click', function (e) {
        // Update window hash to reflect the clicked item
        window.location.hash = `#item-${i + 1}`;
    });
}

// Get the triangle indicator element
const triangleIndicator = document.getElementById('triangle');


/**
 * Position the triangle indicator next to the active navigation item
 * @param {number|string} itemNumber - The 1-based index of the active item
 */
function positionTriangleIndicator(itemNumber) {
    const itemIndex = Number(itemNumber) - 1;
    if (itemIndex >= 0 && itemIndex < navItems.length) {
        const itemTopPosition = navItems[itemIndex].offsetTop;
        triangleIndicator.style.top = `${itemTopPosition + 6}px`;
    }
}

// Create an IntersectionObserver to detect which content item is currently visible
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const id = entry.target.id;
            if (id) {
                // Extract the item number from the ID (format: 'item-1')
                const parts = id.split('-');
                if (parts.length > 1) {
                    positionTriangleIndicator(parts[1]);
                }
            }
        }
    });
}, {threshold: 0.5}); // Trigger when 50% of the item is visible

// Observe all content items for intersection changes
const contentItems = document.getElementsByClassName('content-item');
for (let i = 0; i < contentItems.length; i++) {
    observer.observe(contentItems[i]);
}