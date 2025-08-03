// URL redirect target
const redirectUrl = "#";

// 1. Disable klik kanan dan drag image
document.addEventListener("contextmenu", e => e.preventDefault());
document.addEventListener("dragstart", e => e.preventDefault());
document.addEventListener("mousedown", e => {
  if (e.button === 2 || e.button === 1) e.preventDefault();
});

// 2. Disable shortcut keyboard untuk Inspect & Save Source
document.addEventListener("keydown", function (e) {
  if (
    e.key === "F12" ||
    (e.ctrlKey && e.shiftKey && ["I", "J", "C", "K"].includes(e.key.toUpperCase())) ||
    (e.ctrlKey && ["U", "S"].includes(e.key.toUpperCase()))
  ) {
    e.preventDefault();
    window.location.href = redirectUrl;
  }
});

// 3. Deteksi DevTools via console.log trick
(function detectConsoleOpen() {
  const element = new Image();
  Object.defineProperty(element, "id", {
    get: function () {
      window.location.replace(redirectUrl);
    }
  });
  setInterval(() => {
    console.log(element);
  }, 1000);
})();

// 4. (Opsional) Cegah download gambar via klik kanan Save As
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll("img").forEach(img => {
    img.setAttribute("draggable", "false");
    img.oncontextmenu = e => e.preventDefault();
  });
});
