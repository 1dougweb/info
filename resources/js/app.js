import Alpine from 'alpinejs';

// Global Alpine stores
Alpine.store('sidebar', {
    open: window.innerWidth > 768,
    toggle() { this.open = !this.open; },
    close()  { this.open = false; },
});

Alpine.store('toast', {
    items: [],
    add(message, type = 'success') {
        const id = Date.now();
        this.items.push({ id, message, type });
        setTimeout(() => this.remove(id), 4000);
    },
    remove(id) {
        this.items = this.items.filter(i => i.id !== id);
    },
});

// Clipboard copy helper
Alpine.magic('copy', () => (text) => {
    navigator.clipboard.writeText(text).then(() => {
        Alpine.store('toast').add('Copiado!', 'success');
    });
});

window.Alpine = Alpine;
Alpine.start();
