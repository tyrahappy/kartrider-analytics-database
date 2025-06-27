/**
 * 智能标签导航系统 - 通用JavaScript功能
 * 适用于所有模块的标签导航
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

            // 如果标签太多导致需要滚动，添加滚动指示器
            if (hasScrolling) {
                tabsContainer.classList.add('scrollable');

                // 添加滚动到当前活动标签的功能
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

        // 初始化和窗口调整时优化布局
        optimizeTabLayout();

        // 防抖的 resize 事件监听器
        let resizeTimeout;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(optimizeTabLayout, 150);
        });

        // 为标签添加键盘导航支持
        tabs.forEach((tab, index) => {
            // 添加 tabindex 以支持键盘导航
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

            // 鼠标悬停时显示提示信息
            tab.addEventListener('mouseenter', function () {
                if (tab.scrollWidth > tab.offsetWidth) {
                    tab.setAttribute('title', tab.textContent.trim());
                }
            });
        });

        // 添加触摸支持（移动端）
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
            const swipeThreshold = 50; // 最小滑动距离
            const activeTab = tabsContainer.querySelector('.tab.active');

            if (!activeTab) return;

            const activeIndex = Array.from(tabs).indexOf(activeTab);

            if (touchEndX < touchStartX - swipeThreshold) {
                // 向左滑动 - 切换到下一个标签
                if (activeIndex < tabs.length - 1) {
                    tabs[activeIndex + 1].click();
                }
            }

            if (touchEndX > touchStartX + swipeThreshold) {
                // 向右滑动 - 切换到上一个标签
                if (activeIndex > 0) {
                    tabs[activeIndex - 1].click();
                }
            }
        }
    });
}

// 工具函数：平滑滚动到指定标签
function scrollToTab(tabElement) {
    if (tabElement) {
        tabElement.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
}

// 工具函数：设置活动标签
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

// 导出供其他脚本使用
window.TabSystem = {
    initializeTabSystems,
    scrollToTab,
    setActiveTab
};
