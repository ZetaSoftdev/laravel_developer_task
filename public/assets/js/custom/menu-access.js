$(document).ready(function() {
    // Hide menu items that don't have access
    $('[data-access="false"]').each(function() {
        // Get the menu item
        const menuItem = $(this);
        
        // If it's a submenu trigger, hide the parent li
        if (menuItem.hasClass('nav-link') && menuItem.attr('data-toggle') === 'collapse') {
            menuItem.closest('li.nav-item').hide();
        } else {
            // Otherwise just hide the menu item itself
            menuItem.hide();
        }
        
        // Also hide any associated submenu
        const submenuId = menuItem.attr('href');
        if (submenuId && submenuId.startsWith('#')) {
            $(submenuId).hide();
        }
    });
}); 