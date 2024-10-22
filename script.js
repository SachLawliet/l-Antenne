document.addEventListener("DOMContentLoaded", function () {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    const menu = document.getElementById('menu');
    const centralAntenne = document.getElementById('centralAntenne');
    const galleryItems = document.querySelectorAll('.gallery-section *'); 
    const gridItems = document.querySelectorAll(".grid-item img");
    const largeImage = document.getElementById("large-image");
    const imageDescription = document.getElementById("image-description");
    const countdownElement = document.getElementById('time');
    
    const links = [
        'description.html',
        'bluehand.html',
        'gallery.html',
        'coffee.html',
        'france.html'
    ];
    
    let smallAntennas = [];

    // Show the hamburger menu softly
    if (hamburgerMenu) {
        setTimeout(() => {
            hamburgerMenu.classList.add('menu-show');
        }, 1000); // Delay to show hamburger menu after 1 second
    }

    // Toggle the menu visibility when the hamburger menu is clicked
    if (hamburgerMenu && menu) {
        hamburgerMenu.addEventListener('click', function() {
            menu.classList.toggle('menu-active');
        });
    }

    // Loop through all the grid items and add click event listeners
    if (gridItems.length > 0 && largeImage && imageDescription) {
        gridItems.forEach(item => {
            item.addEventListener("click", function () {
                largeImage.src = this.src;

                const description = this.getAttribute("data-description");
                imageDescription.textContent = description || "No description available.";
            });
        });
    }

    // Show or update small antennas randomly on the page when the central antenna is clicked
    if (centralAntenne) {
        centralAntenne.addEventListener('click', function() {
            // Remove existing small antennas
            smallAntennas.forEach(antenna => antenna.remove());
            smallAntennas = [];

            // Create new small antennas
            for (let i = 0; i < 10; i++) {
                const smallAntenna = document.createElement('a');
                smallAntenna.href = links[Math.floor(Math.random() * links.length)];
                smallAntenna.target = '_self'; // Open in the same tab
                
                const webp = document.createElement('webp');
                webp.src = 'pics/antenne.webp';
                webp.alt = 'Small Antenne';
                webp.className = 'small-antenna';
                
                // Position the small antennas randomly on the page
                webp.style.top = `${Math.random() * 100}vh`;
                webp.style.left = `${Math.random() * 100}vw`;

                smallAntenna.appendChild(webp);
                document.body.appendChild(smallAntenna);
                smallAntennas.push(smallAntenna);
            }
        });
    }

    // Optional: Close menu when clicking outside of it
    if (menu && hamburgerMenu) {
        document.addEventListener('click', function(event) {
            if (!menu.contains(event.target) && !hamburgerMenu.contains(event.target)) {
                menu.classList.remove('menu-active');
            }
        });
    }
});
