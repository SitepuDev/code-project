const toggle = document.getElementById("themeToggle");
const html = document.documentElement;

// load theme tersimpan
const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
  html.setAttribute("data-theme", savedTheme);
  toggle.textContent = savedTheme === "dark" ? "🌙" : "☀️";
}

toggle.addEventListener("click", () => {
  const current = html.getAttribute("data-theme");

  if (current === "dark") {
    html.setAttribute("data-theme", "light");
    localStorage.setItem("theme", "light");
    toggle.textContent = "☀️";
  } else {
    html.setAttribute("data-theme", "dark");
    localStorage.setItem("theme", "dark");
    toggle.textContent = "🌙";
  }
});
