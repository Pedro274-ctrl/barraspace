document.addEventListener('DOMContentLoaded', () => {
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches
        || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const dot = document.querySelector('.cursor-dot');
    const outline = document.querySelector('.cursor-outline');
    if (!dot || !outline) {
        return;
    }

    let mouseX = 0;
    let mouseY = 0;
    let outlineX = 0;
    let outlineY = 0;

    window.addEventListener('mousemove', event => {
        mouseX = event.clientX;
        mouseY = event.clientY;
        dot.style.left = `${mouseX}px`;
        dot.style.top = `${mouseY}px`;
    }, { passive: true });

    const animateOutline = () => {
        outlineX += (mouseX - outlineX) * 0.15;
        outlineY += (mouseY - outlineY) * 0.15;
        outline.style.left = `${outlineX}px`;
        outline.style.top = `${outlineY}px`;
        requestAnimationFrame(animateOutline);
    };
    animateOutline();

    document.querySelectorAll('a, button, .bento-card, .condomino-box').forEach(element => {
        element.addEventListener('mouseenter', () => document.body.classList.add('hovering'));
        element.addEventListener('mouseleave', () => document.body.classList.remove('hovering'));
    });
});
