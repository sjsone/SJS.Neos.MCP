/**
 * Inject a "Connections" link into the Neos backend user dropdown menu.
 * Works for both the Fluid backend and the React-based Neos UI by using
 * a MutationObserver to catch dynamically rendered menus.
 */
(function () {
    var injected = false;
    var basePath = '/neos';

    function injectLink(dropdown) {
        if (injected) return;
        if (!dropdown) return;
        if (dropdown.querySelector('.sjs-connections-link')) {
            injected = true;
            return;
        }

        // Find the base path from the Fluid backend's data attribute
        var userMenuEl = document.querySelector('.neos-user-menu');
        if (userMenuEl && userMenuEl.hasAttribute('data-module-basepath')) {
            basePath = userMenuEl.getAttribute('data-module-basepath');
        }

        var connectionsUrl = basePath + '/user/connections';
        var li = document.createElement('li');

        // Fluid backend: simple <li> with <a>
        li.className = 'sjs-connections-link';
        li.innerHTML = '<a href="' + connectionsUrl + '">'
            + '<i class="fas fa-magic-wand-sparkles"></i> AI Connections'
            + '</a>';

        // Also set className for React UI compatibility
        li.setAttribute('data-sjs-connection-link', 'true');

        // Insert before the last item (typically Logout)
        var items = dropdown.querySelectorAll('li');
        var logoutItem = items[items.length - 1];
        if (logoutItem) {
            dropdown.insertBefore(li, logoutItem);
        } else {
            dropdown.appendChild(li);
        }
        injected = true;
    }

    // Approach 1: Fluid backend — menu is present on DOMContentLoaded
    function tryFluidBackend() {
        var dropdown = document.querySelector('#neos-user-actions .neos-dropdown-menu');
        if (dropdown) {
            injectLink(dropdown);
        }
    }

    // Approach 2: React UI — menu rendered dynamically, watch with MutationObserver
    function watchForMenu() {
        var observer = new MutationObserver(function () {
            // Fluid backend selector
            var fluidDropdown = document.querySelector('#neos-user-actions .neos-dropdown-menu');
            if (fluidDropdown && !fluidDropdown.querySelector('.sjs-connections-link')) {
                injectLink(fluidDropdown);
            }

            // React UI: find any dropdown that contains "User Settings" or "Logout"
            var allLists = document.querySelectorAll('ul');
            for (var i = 0; i < allLists.length; i++) {
                var list = allLists[i];
                var text = list.textContent || '';
                if (text.indexOf('User Settings') !== -1 && text.indexOf('Logout') !== -1) {
                    if (!list.querySelector('.sjs-connections-link')) {
                        injectLink(list);
                    }
                }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    document.addEventListener('DOMContentLoaded', function () {
        tryFluidBackend();
        watchForMenu();
    });
})();
