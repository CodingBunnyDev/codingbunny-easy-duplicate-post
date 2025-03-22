document.addEventListener('DOMContentLoaded', function() {
    if (window.location.href.indexOf('page=easy-duplicate-post') !== -1) {
        document.querySelectorAll('.nav-tab').forEach(function(tab) {
            tab.addEventListener('click', function(event) {
                event.preventDefault();
                
                document.querySelectorAll('.nav-tab').forEach(function(t) {
                    t.classList.remove('nav-tab-active');
                });
                
                document.querySelectorAll('.cbedp-tab-content').forEach(function(content) {
                    content.style.display = 'none';
                });
                
                tab.classList.add('nav-tab-active');
                
                document.querySelector(tab.getAttribute('href')).style.display = 'block';
            });
        });
    }
});