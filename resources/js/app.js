document.addEventListener('alpine:init', () => {
    Alpine.data('printsyncToast', (message) => ({
        show: true,
        message: message,
        init() {
            setTimeout(() => {
                this.show = false;
            }, 3000);
        }
    }));
});
