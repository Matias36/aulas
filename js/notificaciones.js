// Pantalla de avisos: cambios en las clases que el alumno sigue y novedades.
const lista = document.getElementById("avisos");
const MESES_CORTO = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
const ENLACE_SEGURO = /^[a-z0-9_-]+\.html(\?[A-Za-z0-9_=&%.:\-]*)?$/i;   // solo páginas de esta misma app

// "Hoy 14:30", "Ayer 09:10", "12 oct 18:05"
function cuando(iso) {
  const f = new Date(iso);
  if (isNaN(f)) return "";
  const hoyF = new Date(), ayer = new Date();
  ayer.setDate(hoyF.getDate() - 1);
  const hm = `${pad(f.getHours())}:${pad(f.getMinutes())}`;
  if (f.toDateString() === hoyF.toDateString()) return `Hoy ${hm}`;
  if (f.toDateString() === ayer.toDateString()) return `Ayer ${hm}`;
  return `${pad(f.getDate())} ${MESES_CORTO[f.getMonth()]}${f.getFullYear() !== hoyF.getFullYear() ? " " + f.getFullYear() : ""} ${hm}`;
}

fetch("api/notificaciones.php", { cache: "no-store" }).then(r => {
  if (r.status === 401) { irAlLogin(); throw new Error("sin sesión"); }
  if (!r.ok) throw new Error("error");
  return r.json();
}).then(({ items }) => {
  if (!items.length) {
    lista.innerHTML = '<li class="vacio">No tenés avisos por ahora. Activá la campanita de una clase y te avisamos acá si cambia el aula o el horario.</li>';
    return;
  }
  lista.innerHTML = items.map(a => {
    const cuerpo = `<small>${esc(cuando(a.creada))}</small><strong>${esc(a.mensaje)}</strong>`;
    const clase = `fecha aviso${a.leida ? "" : " nueva"}`;
    return a.enlace && ENLACE_SEGURO.test(a.enlace)
      ? `<li><a class="${clase}" href="${esc(a.enlace)}">${cuerpo}</a></li>`
      : `<li><div class="${clase}">${cuerpo}</div></li>`;
  }).join("");
  // Al abrir la pantalla, los avisos pasan a leídos (esta vez se ven resaltados los nuevos)
  if (items.some(a => !a.leida)) {
    fetch("api/notificaciones.php", {
      method: "POST", headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ accion: "leer_todas" }), keepalive: true
    }).catch(() => {});
  }
}).catch(e => { if (e.message !== "sin sesión") lista.innerHTML = '<li class="vacio">No se pudieron cargar los avisos. Abrí la app desde localhost.</li>'; });
