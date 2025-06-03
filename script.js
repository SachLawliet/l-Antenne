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
        'imag3s.php',
        'images.html',
        'bluehand.php',
        'gallery.php',
        'account.php',
        'Radio.php',
        'morse.php',
        'france.html',
        'coffee.html'
    ];

    const randomTexts = [
        "s/o palin",
        "s/o squeezi",
        "s/o kawa",
        "s/o Xavier Niel",
        "s/o la dep",
        "s/o 4poursang",
        "s/o louann",
        "s/o l'Antoine",
        "s/o coman",
        "s/o urdouÉ"
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
            for (let i = 0; i < 5; i++) {
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


const glitchElements = document.querySelectorAll('.glitch');

setInterval(() => {
    glitchElements.forEach(el => {
        const randomX = Math.random() * 10 - 5; // Random offset between -5 and 5
        const randomY = Math.random() * 10 - 5;
        const randomColor = Math.random() > 0.5 ? '#ff005a' : '#00ffff';
        el.style.transform = `translate(${randomX}px, ${randomY}px)`;
        el.style.textShadow = `
            ${randomX}px ${randomY}px ${randomColor},
            ${-randomX}px ${-randomY}px ${randomColor}`;
    });
}, 200); // Adjust interval for glitch frequency


    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm-password').value;

        if (password !== confirmPassword) {
            e.preventDefault(); // Stop form submission
            alert('Passwords do not match!');
        }
    });
    
    
    

    
    
    
// Helper function to create the correct media element for a given source.
      function createMediaElement(src) {
        let element;
        // Check file extension for video files
        if (src.match(/\.(mp4|webm)$/i)) {
          element = document.createElement('video');
          element.controls = true;
          element.autoplay = true;
          const source = document.createElement('source');
          source.src = src;
          // Modify mime type if needed (e.g., video/mp4)
          source.type = "video/mp4";
          element.appendChild(source);
        } else if (src.match(/\.(pdf)$/i)) {
          element = document.createElement('embed');
          element.src = src;
          element.type = "application/pdf";
        } else {
          // Default to image element if no match
          element = document.createElement('img');
          element.src = src;
        }
        element.style.width = '100%';
        element.style.height = 'auto';
        return element;
      }

      document.addEventListener('DOMContentLoaded', () => {
        const overlay = document.querySelector('.fullscreenOverlay');

        // Fullscreen display for individual media (images, video, embed)
        const mediaGrid = document.querySelector('.media-grid');
        if (mediaGrid && overlay) {
          mediaGrid.addEventListener('click', event => {
            const target = event.target;
            if (target.matches('img, video, embed, audio')) {
              overlay.innerHTML = '';
                // Clone the clicked media element
                const cloned = target.cloneNode(true);
                if (cloned.tagName.toLowerCase() === 'video') {
                  cloned.autoplay = true;
                  cloned.controls = true;
                }
                overlay.appendChild(cloned);
                
                // Create overlay title
                const title = target.getAttribute('data-title') || '';
                if (title) {
                  const overlayTitle = document.createElement('div');
                  overlayTitle.className = 'overlay-title';
                  overlayTitle.textContent = title;
                  overlay.appendChild(overlayTitle);
                }
                
                // Create overlay info
                const createdAt = target.getAttribute('data-created_at') || '';
                const username = target.getAttribute('data-username') || '';
                if (createdAt || username) {
                  const overlayInfo = document.createElement('div');
                  overlayInfo.className = 'overlay-info';
                  overlayInfo.textContent = `${username ? 'By ' + username : ''} ${createdAt ? 'on ' + createdAt : ''}`;
                  overlay.appendChild(overlayInfo);
                }
                
                overlay.classList.add('show');

            }
          });
        }

        // Fullscreen display for folders (reads JSON from data-media) with propagation stopped.
        document.querySelectorAll('.folder-item').forEach(item => {
          item.addEventListener('click', (event) => {
            event.stopPropagation();  // Prevent the event from bubbling up
            const mediaData = item.getAttribute('data-media');
            if (!mediaData) return;
            const mediaArray = JSON.parse(mediaData);
            const folderGrid = document.createElement('div');
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
        
            // Common gap
            folderGrid.style.gap = '10px';
        
            if (isMobile) {
              // === MOBILE: horizontal swipe carousel ===
              folderGrid.style.display               = 'flex';                   // CHANGED: use flex instead of grid
              folderGrid.style.flexWrap              = 'nowrap';                 // CHANGED: prevent wrapping
              folderGrid.style.overflowX             = 'auto';                   // CHANGED: enable horizontal scroll
              folderGrid.style.WebkitOverflowScrolling = 'touch';                // CHANGED: smooth iOS scroll
              folderGrid.style.scrollSnapType        = 'x mandatory';            // CHANGED: snap points along X
              folderGrid.style.scrollBehavior        = 'smooth';                 // CHANGED: smooth snapping
              // (no gridTemplateColumns needed)
            } else {
              // === DESKTOP: classic grid layout ===
              folderGrid.style.display               = 'grid';
              folderGrid.style.gridTemplateColumns   = 'repeat(3, 1fr)';
            }
        
            // Append media items
            mediaArray.forEach(src => {
              const el = createMediaElement(src);
              folderGrid.appendChild(el);
            });
        
            // FOR MOBILE: adjust each child for snapping
            if (isMobile) {
              Array.from(folderGrid.children).forEach(child => {
                child.style.flex            = '0 0 80%';        // CHANGED: each item takes 80% of viewport width
                child.style.scrollSnapAlign = 'center';         // CHANGED: center snap alignment
                child.style.boxSizing       = 'border-box';
              });
            }
        
            overlay.innerHTML = '';
            overlay.appendChild(folderGrid);
            
            // Get folder title and info
            const title = item.getAttribute('data-title');
            const info = item.getAttribute('data-info');
            
            if (title) {
              const overlayTitle = document.createElement('div');
              overlayTitle.className = 'overlay-title';
              overlayTitle.textContent = title;
              overlay.appendChild(overlayTitle);
            }
            if (info) {
              const overlayInfo = document.createElement('div');
              overlayInfo.className = 'overlay-info';
              overlayInfo.textContent = info;
              overlay.appendChild(overlayInfo);
            }
            
            overlay.classList.add('show');
          });
        });

        // Close overlay when clicked
        overlay.addEventListener('click', () => {
          overlay.classList.remove('show');
          overlay.innerHTML = '';
        });
      });