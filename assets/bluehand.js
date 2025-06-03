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
        const overlay = document.getElementById('fullscreenOverlay');

        // Fullscreen display for individual media (images, video, embed)
        const mediaGrid = document.querySelector('.media-grid');
        if (mediaGrid && overlay) {
          mediaGrid.addEventListener('click', event => {
            const target = event.target;
            if (target.matches('img, video, embed')) {
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
      
      
      
    document.addEventListener('DOMContentLoaded', function () {
        const lazyMedia = Array.from(document.querySelectorAll('img.lazy, embed.lazy-embed'));

        if ('IntersectionObserver' in window) {
            let mediaObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let media = entry.target;
                        const dataSrc = media.dataset.src;
                        if (dataSrc) {
                            if (media.tagName === 'IMG') {
                                media.src = dataSrc;
                            } else if (media.tagName === 'EMBED') {
                                media.setAttribute('src', dataSrc);
                            }
                        }
                        media.classList.remove('lazy', 'lazy-embed');
                        media.classList.add('lazy-loaded');
                        observer.unobserve(media);
                    }
                });
            }, { rootMargin: "0px 0px 100px 0px" }); // Start loading when 100px from viewport
            lazyMedia.forEach(function (media) {
                mediaObserver.observe(media);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            lazyMedia.forEach(function (media) {
                const dataSrc = media.dataset.src;
                if (dataSrc) {
                    if (media.tagName === 'IMG') {
                        media.src = dataSrc;
                    } else if (media.tagName === 'EMBED') {
                        media.setAttribute('src', dataSrc);
                    }
                }
            media.classList.remove('lazy', 'lazy-embed');
            media.classList.add('lazy-loaded');
            });
        }
    });