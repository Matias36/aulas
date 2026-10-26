// Barra inferior compartida. Cada página indica su pestaña con <body data-activo="inicio|calendario|perfil">
(function () {
  const items = [
    ["inicio", "Inicio", "inicio.html", '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 2 12h3v8h5v-6h4v6h5v-8h3z"/></svg>'],
    ["calendario", "Calendario", "calendario.html", '<i class="ico ico-calendario" aria-hidden="true"></i>'],
    ["perfil", "Perfil", "perfil.html", '<i class="ico ico-perfil" aria-hidden="true"></i>'],
  ];
  // Los administradores ven una cuarta pestaña para volver al panel
  let quien = null;
  try { quien = JSON.parse(sessionStorage.getItem("sesion") || "null"); } catch {}
  if (quien && quien.rol === "administrador") {
    items.push(["admin", "Panel", "admin.html", '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 6h9v2H3zM16 6h5v2h-5zM12 4h4v6h-4zM3 16h5v2H3zM12 16h9v2h-9zM8 14h4v6H8z"/></svg>']);
  }
  const activo = document.body.dataset.activo;
  const nav = document.createElement("nav");
  nav.className = "nav";
  nav.setAttribute("aria-label", "Principal");
  nav.innerHTML = items.map(([id, txt, href, icono]) =>
    `<a href="${href}" class="${id === activo ? "activo" : ""}"${id === activo ? ' aria-current="page"' : ""}>${icono}${txt}</a>`).join("");
  document.body.appendChild(nav);
})();
