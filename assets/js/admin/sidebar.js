function disabled(e) {
    e.preventDefault();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('menu').querySelectorAll('a').forEach(function (a) {
        if (a.href === (window.location.origin + window.location.pathname)) {
            a.parentNode.classList = ['active'];
            a.addEventListener('click', disabled);
        } else {
            a.parentNode.classList = [];
            a.removeEventListener('click', disabled);
        }
    });
});