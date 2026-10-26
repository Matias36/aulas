// ===== Utilidades compartidas por las pantallas internas =====
// Se carga antes del JS de cada pantalla (inicio, edificio, aula, calendario, perfil).

// Sesión: la real vive en el servidor (api/sesion.php). En sessionStorage queda solo una copia
// para mostrar el nombre sin esperar. Si no hay copia, o el servidor dice que no hay sesión,
// vuelve al login y recuerda a qué pantalla quería ir.
const sesion = JSON.parse(sessionStorage.getItem("sesion") || "null");
const irAlLogin = () => {
  sessionStorage.removeItem("sesion");
  location.replace("index.html?volver=" + encodeURIComponent(location.pathname.split("/").pop() + location.search));
};
if (!sesion) irAlLogin();
else fetch("api/sesion.php", { cache: "no-store" }).then(r => { if (r.status === 401) irAlLogin(); }).catch(() => {});

// Cerrar sesión: la cierra también en el servidor (usar desde el botón de Perfil)
async function cerrarSesion() {
  try { await fetch("api/logout.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: "{}" }); } catch {}
  sessionStorage.removeItem("sesion");
  location.replace("index.html");
}

// Fecha y hora. Para demostrar: ?dia=1&hora=13:00  (dia: 1 = lunes ... 7 = domingo)
const params = new URLSearchParams(location.search);
const pad = n => String(n).padStart(2, "0");
const ahora = new Date();
const hoy = ahora.getDay() || 7;
let dia = Number(params.get("dia")) || hoy;
const hora = params.get("hora") || `${pad(ahora.getHours())}:${pad(ahora.getMinutes())}`;
const aMin = s => { const [h, m] = s.split(":").map(Number); return h * 60 + m; };
const horaDe = s => Number(s.split(":")[0]);
const horaFin = s => { const [h, m] = s.split(":").map(Number); return m > 0 ? h + 1 : h; };

// Horario de la universidad: lunes a viernes 7-23, sábados 7-17, domingos cerrado
const H_INICIO = 7, H_FIN = 23, H_FIN_SAB = 17;

// Una clase está "en curso" si su día coincide y la hora actual cae entre inicio y fin
const claseActual = a => (a.horarios || []).find(h => h.dia === dia && !h.cancelada && aMin(h.inicio) <= aMin(hora) && aMin(hora) < aMin(h.fin));

// Almacenamiento local (prototipo). Con MySQL estos datos pasan al servidor.
const leer = (k, def) => { try { return JSON.parse(localStorage.getItem(k) || "null") ?? def; } catch { return def; } };
const guardar = (k, v) => { try { localStorage.setItem(k, JSON.stringify(v)); } catch {} };

// Campanita (recordatorio) reutilizable
const campana = '<i class="ico ico-campana-on" aria-hidden="true"></i><i class="ico ico-campana-off" aria-hidden="true"></i>';
// Clases seguidas: ids de horarios que el alumno sigue (y cuáles tienen nota). Los guarda el servidor.
async function cargarSeguidos() {
  const vacio = { ids: new Set(), conNota: new Set() };
  try {
    const r = await fetch("api/seguimientos.php?modo=ids", { cache: "no-store" });
    if (r.status === 401) { irAlLogin(); return vacio; }
    const d = await r.json();
    return { ids: new Set(d.ids), conNota: new Set(d.conNota) };
  } catch { return vacio; }
}

// Activa o desactiva el seguimiento de una clase. El botón lleva data-ids="12,13" y aria-pressed.
// Devuelve true (ahora la sigue), false (dejó de seguirla) o null (se canceló o falló).
async function alternarRecordatorio(boton, tieneNota = false) {
  const activar = boton.getAttribute("aria-pressed") !== "true";
  if (!activar && tieneNota && !confirm("Si dejás de seguir esta clase, también se borra tu nota. ¿Seguir adelante?")) return null;
  boton.setAttribute("aria-pressed", activar);
  const ids = boton.dataset.ids.split(",").map(Number);
  try {
    const r = await fetch("api/seguimientos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ accion: activar ? "seguir" : "dejar", ids })
    });
    if (r.status === 401) { irAlLogin(); return null; }
    if (!r.ok) throw new Error("falló");
    if (!activar) boton.dataset.nota = "0";
    return activar;
  } catch {
    boton.setAttribute("aria-pressed", !activar);
    alert("No se pudo guardar el cambio. Probá de nuevo.");
    return null;
  }
}
