document.addEventListener("DOMContentLoaded", function () {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    const menu = document.getElementById('menu');
    const centralAntenne = document.getElementById('centralAntenne');
    const galleryItems = document.querySelectorAll('.gallery-section *'); 
    const gridItems = document.querySelectorAll(".grid-item img");
    const largeImage = document.getElementById("large-image");
    const bikhand = document.getElementById("bikhand");
    const imgDescBiktop = document.getElementById("imgDescBiktop");
    const imageDescription = document.getElementById("image-description");
    const countdownElement = document.getElementById('time');
    
    const links = [
        'description.html',
        'bluehand.html',
        'gallery.html',
        'coffee.html',
        'france.html'
    ];

    const randomTexts = [
        "s/o roi",
        "s/o caca",
        "s/o kawa"
    ];

    let smallAntennas = [];
    let smallTextElements = [];

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

        // Loop through all the grid items and add click event listeners
    if (gridItems.length > 0 && bikhand && imgDescBiktop) {
        gridItems.forEach(item => {
            item.addEventListener("click", function () {
                bikhand.src = this.src;

                const description = this.getAttribute("data-description");
                imgDescBiktop.textContent = description || "No description available.";
            });
        });
    }

    // Show or update small antennas and text elements randomly on the page when the central antenna is clicked
    if (centralAntenne) {
        centralAntenne.addEventListener('click', function() {
            // Remove existing small antennas and text elements
            smallAntennas.forEach(antenna => antenna.remove());
            smallAntennas = [];
            smallTextElements.forEach(text => text.remove());
            smallTextElements = [];

            // Create new small antennas
            for (let i = 0; i < 9; i++) {
                const smallAntenna = document.createElement('a');
                smallAntenna.href = links[Math.floor(Math.random() * links.length)];
                smallAntenna.target = '_self';
                
                const img = document.createElement('img');
                img.src = 'pics/antenne.webp';
                img.alt = 'Small Antenne';
                img.className = 'small-antenna';
                
                // Position the small antennas randomly on the page
                img.style.top = `${Math.random() * 100}vh`;
                img.style.left = `${Math.random() * 100}vw`;

                smallAntenna.appendChild(img);
                document.body.appendChild(smallAntenna);
                smallAntennas.push(smallAntenna);
            }

            // Create new small text elements
            for (let i = 0; i < 3; i++) {
                const smallText = document.createElement('div');
                smallText.textContent = randomTexts[Math.floor(Math.random() * randomTexts.length)];
                smallText.className = 'small-text';
                
                // Position the text elements randomly on the page
                smallText.style.top = `${Math.random() * 100}vh`;
                smallText.style.left = `${Math.random() * 100}vw`;

                document.body.appendChild(smallText);
                smallTextElements.push(smallText);
            }
        });
    }
});
