document.addEventListener("DOMContentLoaded", () => {
    setTimeout(() => {
        document.querySelectorAll(".alert.dismiss").forEach(e => e.remove());
    }, 3000);
});