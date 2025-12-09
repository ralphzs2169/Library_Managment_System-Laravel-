 document.addEventListener('DOMContentLoaded', function() {
            // --- Carousel Logic ---
            const track = document.getElementById('carouselTrack');
            if (track) {
                const prevBtn = document.getElementById('prevBtn');
                const nextBtn = document.getElementById('nextBtn');

                let currentIndex = 0;
                const cardWidth = 220; // min-width of book-card
                const gap = 24; // 1.5rem gap
                const totalCards = track.children.length;

                function getVisibleCards() {
                    const width = window.innerWidth;
                    if (width >= 1024) return 4;
                    if (width >= 768) return 3;
                    if (width >= 480) return 2;
                    return 1;
                }

                function updateCarousel() {
                    const visibleCards = getVisibleCards();
                    const maxIndex = Math.max(0, totalCards - visibleCards);

                    if (currentIndex > maxIndex) {
                        currentIndex = maxIndex;
                    }

                    const offset = currentIndex * (cardWidth + gap);
                    track.style.transform = `translateX(-${offset}px)`;

                    prevBtn.disabled = currentIndex === 0;
                    nextBtn.disabled = currentIndex >= maxIndex;

                    prevBtn.style.opacity = currentIndex === 0 ? '0.5' : '1';
                    nextBtn.style.opacity = currentIndex >= maxIndex ? '0.5' : '1';
                    prevBtn.style.cursor = currentIndex === 0 ? 'not-allowed' : 'pointer';
                    nextBtn.style.cursor = currentIndex >= maxIndex ? 'not-allowed' : 'pointer';
                }

                prevBtn.addEventListener('click', function() {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateCarousel();
                    }
                });

                nextBtn.addEventListener('click', function() {
                    const visibleCards = getVisibleCards();
                    const maxIndex = Math.max(0, totalCards - visibleCards);
                    if (currentIndex < maxIndex) {
                        currentIndex++;
                        updateCarousel();
                    }
                });

                window.addEventListener('resize', function() {
                    updateCarousel();
                });

                updateCarousel();
            }
        });