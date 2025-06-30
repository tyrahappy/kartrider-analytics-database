/**
 * Smart Tab Navigation System - Universal JavaScript functionality
 * Applicable to tab navigation across all modules
 */

document.addEventListener('DOMContentLoaded', function () {
    initializeTabSystems();
});

function initializeTabSystems() {
    const tabsContainers = document.querySelectorAll('.tabs');

    tabsContainers.forEach(tabsContainer => {
        const tabs = tabsContainer.querySelectorAll('.tab');

        if (tabs.length === 0) return;

        function optimizeTabLayout() {
            const containerWidth = tabsContainer.offsetWidth;
            const totalTabsWidth = Array.from(tabs).reduce((total, tab) => total + tab.offsetWidth, 0);
            const hasScrolling = totalTabsWidth > containerWidth;

            // If too many tabs require scrolling, add scroll indicators
            if (hasScrolling) {
                tabsContainer.classList.add('scrollable');

                // Add functionality to scroll to the currently active tab
                const activeTab = tabsContainer.querySelector('.tab.active');
                if (activeTab) {
                    setTimeout(() => {
                        activeTab.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'center'
                        });
                    }, 100);
                }
            } else {
                tabsContainer.classList.remove('scrollable');
            }
        }

        // Initialize and optimize layout on window resize
        optimizeTabLayout();

        // Debounced resize event listener
        let resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(optimizeTabLayout, 150);
        });

        // Add keyboard navigation support for tabs
        tabs.forEach((tab, index) => {
            // Add tabindex to support keyboard navigation
            if (!tab.hasAttribute('tabindex')) {
                tab.setAttribute('tabindex', '0');
            }

            tab.addEventListener('keydown', function (e) {
                if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    tabs[index - 1].focus();
                    tabs[index - 1].scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                } else if (e.key === 'ArrowRight' && index < tabs.length - 1) {
                    e.preventDefault();
                    tabs[index + 1].focus();
                    tabs[index + 1].scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'center'
                    });
                } else if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    tab.click();
                }
            });

            // Show tooltip on mouse hover
            tab.addEventListener('mouseenter', function () {
                if (tab.scrollWidth > tab.offsetWidth) {
                    tab.setAttribute('title', tab.textContent.trim());
                }
            });
        });

        // Add touch support (mobile devices)
        let touchStartX = 0;
        let touchEndX = 0;

        tabsContainer.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].screenX;
        });

        tabsContainer.addEventListener('touchend', function (e) {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50; // Minimum swipe distance
            const activeTab = tabsContainer.querySelector('.tab.active');

            if (!activeTab) return;

            const activeIndex = Array.from(tabs).indexOf(activeTab);

            if (touchEndX < touchStartX - swipeThreshold) {
                // Swipe left - switch to next tab
                if (activeIndex < tabs.length - 1) {
                    tabs[activeIndex + 1].click();
                }
            }

            if (touchEndX > touchStartX + swipeThreshold) {
                // Swipe right - switch to previous tab
                if (activeIndex > 0) {
                    tabs[activeIndex - 1].click();
                }
            }
        }
    });
}

// Utility function: Smooth scroll to specified tab
function scrollToTab(tabElement) {
    if (tabElement) {
        tabElement.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
}

// Utility function: Set active tab
function setActiveTab(tabsContainer, activeTabIndex) {
    const tabs = tabsContainer.querySelectorAll('.tab');

    tabs.forEach((tab, index) => {
        if (index === activeTabIndex) {
            tab.classList.add('active');
            scrollToTab(tab);
        } else {
            tab.classList.remove('active');
        }
    });
}

// Export for use by other scripts
window.TabSystem = {
    initializeTabSystems,
    scrollToTab,
    setActiveTab
};
