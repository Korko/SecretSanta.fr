// Mobile menu: the button shows or hides the links, and says which
const burger = document.querySelector('.kt-burger');
const menu = document.getElementById('kt-menu');
if (burger && menu) {
    const toggle = open => {
        menu.hidden = !open;
        burger.setAttribute('aria-expanded', String(open));
        burger.setAttribute('aria-label', open ? burger.dataset.labelClose : burger.dataset.labelOpen);
    };
    burger.addEventListener('click', () => toggle(menu.hidden));
    menu.addEventListener('click', event => event.target.closest('a') && toggle(false));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !menu.hidden) {
            toggle(false);
            burger.focus();
        }
    });
}
