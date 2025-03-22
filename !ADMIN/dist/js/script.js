document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.getElementById('menuButton'); 
    const sidebar = document.getElementById('sidenav-main'); 
    const closeIcon = document.getElementById('iconSidenav'); 
    const footerMenuButton = document.getElementById('footerMenuButton'); 

    if (!menuButton || !sidebar || !closeIcon || !footerMenuButton) {
        console.error("One or more sidebar elements are missing from the DOM.");
        return;
    }

    // Function to toggle sidebar visibility based on screen size
    const toggleSidebar = () => {
        if (window.innerWidth < 768) { 
            sidebar.classList.toggle('open'); 
        }
    };

    // Add event listeners for the menu buttons
    menuButton.addEventListener('click', toggleSidebar);
    footerMenuButton.addEventListener('click', toggleSidebar); 

    // Close sidebar when the close icon is clicked (for mobile)
    closeIcon.addEventListener('click', function () {
        if (window.innerWidth < 768) {
            sidebar.classList.remove('open'); 
        }
    });

    // Detect environment (mobile/desktop) and adjust UI
    function detectEnvironment() {
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            document.body.classList.add('app'); 
            const topHeader = document.querySelector('.top-header');
            const footer = document.querySelector('.footer');

            if (topHeader) topHeader.style.display = 'none';
            if (footer) footer.style.display = 'flex';

            sidebar.classList.remove('open');
        } else {
            document.body.classList.remove('app');
            const topHeader = document.querySelector('.top-header');
            const footer = document.querySelector('.footer');

            if (topHeader) topHeader.style.display = 'flex';
            if (footer) footer.style.display = 'none';

            sidebar.classList.add('open');
        }
    }

    // Call the environment detection function on page load
    detectEnvironment();

    // Add event listener for window resize
    window.addEventListener('resize', detectEnvironment);
});
