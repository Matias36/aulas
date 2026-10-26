document.getElementById("saludo").replaceChildren(
  `Hola, ${(sesion?.nombre || "").split(" ")[0].toUpperCase()}`,
  document.createElement("br"),
  "Seleccioná un edificio");

const lista = document.getElementById("edificios");
const vacio = document.getElementById("vacio");
const secMaterias = document.getElementById("materias");
const resultados = document.getElementById("resultados");
const botonCampana = document.getElementById("campana");
const DIAS = ["", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

let edificios = [];
let texto = "";                  // lo que escribió la persona
let materiasHalladas = [];       // resultados del buscador de materias
let textoBuscado = "";           // para qué texto son esos resultados
let SEGUIDOS = { ids: new Set(), conNota: new Set() };   // clases que ya sigue (vienen del servidor)
const seguidosListos = cargarSeguidos().then(s => { SEGUIDOS = s; });

const chevron = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 3l9 9-9 9"/></svg>';
const plano = s => s.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();   // sin tildes ni mayúsculas
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

function tarjetaMateria(m) {
  const activo = m.ids.some(i => SEGUIDOS.ids.has(i));
  const conNota = m.ids.some(i => SEGUIDOS.conNota.has(i));
  const href = `aula.html?id=${encodeURIComponent(m.aulaId)}&e=${encodeURIComponent(m.edificioId)}&d=${m.dia}`;
  return `<li class="curso"><div class="datos">
    <a class="res-link" href="${href}">
      <div class="cuando"><b>${DIAS[m.dia]}</b> ${m.inicio} - ${m.fin} &nbsp;|&nbsp; <b>${esc(m.edificio)}</b> ${esc(m.aula)}</div>
      <h3>${esc(m.materia)}</h3>
      <div class="docente">${esc([m.docente, m.comision].filter(Boolean).join(" | "))}</div>
      <span class="carrera">${esc(m.carrera)}</span>
    </a>
    <button class="campana-curso" type="button" data-ids="${m.ids.join(",")}" data-nota="${conNota ? 1 : 0}" aria-pressed="${activo}" aria-label="Seguir esta clase: ${esc(m.materia)}">${campana}</button>
  </div></li>`;
}

function pintar() {
  const t = plano(texto.trim());
  const visibles = edificios.filter(e => plano(e.nombre).includes(t));
  lista.innerHTML = visibles.map(e => `
    <li><a class="edificio" href="edificio.html?id=${e.id}"
        style="background-image: linear-gradient(to top, rgba(0,0,0,.78), rgba(0,0,0,0) 62%), url('${e.foto}'), linear-gradient(135deg, ${e.color[0]}, ${e.color[1]})">
      <span>${e.nombre}</span>${chevron}</a></li>`).join("");

  const buscaMaterias = t.length >= 2;
  const materias = buscaMaterias ? materiasHalladas : [];
  secMaterias.hidden = !materias.length;
  resultados.innerHTML = materias.map(tarjetaMateria).join("");

  const esperando = buscaMaterias && textoBuscado !== texto.trim();   // todavía no llegó la respuesta
  vacio.textContent = buscaMaterias ? "No encontramos edificios ni materias con ese nombre." : "No hay edificios con ese nombre.";
  vacio.hidden = visibles.length > 0 || materias.length > 0 || esperando;
}

// ---- Buscador de materias: espera un instante después de escribir y descarta respuestas viejas ----
let temporizador = null, controlador = null;
function buscarMaterias(q) {
  if (controlador) controlador.abort();
  controlador = new AbortController();
  Promise.all([
    fetch("api/buscar.php?q=" + encodeURIComponent(q), { signal: controlador.signal }).then(r => r.json()),
    seguidosListos
  ]).then(([hallados]) => { materiasHalladas = Array.isArray(hallados) ? hallados : []; textoBuscado = q; pintar(); })
    .catch(e => { if (e.name !== "AbortError") { materiasHalladas = []; textoBuscado = q; pintar(); } });
}

document.getElementById("buscar").addEventListener("input", e => {
  texto = e.target.value;
  clearTimeout(temporizador);
  const q = texto.trim();
  if (q.length < 2) { if (controlador) controlador.abort(); materiasHalladas = []; textoBuscado = ""; }
  else temporizador = setTimeout(() => buscarMaterias(q), 250);
  pintar();
});

// Campanita de cada resultado: seguir o dejar de seguir la clase
resultados.addEventListener("click", e => {
  const b = e.target.closest(".campana-curso");
  if (!b) return;
  const ids = b.dataset.ids.split(",").map(Number);
  alternarRecordatorio(b, b.dataset.nota === "1").then(estado => {
    if (estado === null) return;   // se canceló o falló
    ids.forEach(i => { if (estado) SEGUIDOS.ids.add(i); else { SEGUIDOS.ids.delete(i); SEGUIDOS.conNota.delete(i); } });
  });
});

// ---- Campana de notificaciones: el punto naranja aparece solo si hay avisos sin leer ----
function actualizarCampana() {
  fetch("api/notificaciones.php?modo=contar", { cache: "no-store" })
    .then(r => r.ok ? r.json() : null)
    .then(d => {
      if (!d) return;
      botonCampana.querySelector(".punto").hidden = d.noLeidas === 0;
      botonCampana.setAttribute("aria-label", d.noLeidas ? `Notificaciones (${d.noLeidas} sin leer)` : "Notificaciones");
    }).catch(() => {});
}
botonCampana.addEventListener("click", () => { location.href = "notificaciones.html"; });
window.addEventListener("pageshow", e => { if (e.persisted) actualizarCampana(); });   // al volver con "atrás"
actualizarCampana();

fetch("api/edificios.php").then(r => r.json()).then(d => { edificios = d; pintar(); })
  .catch(() => { vacio.hidden = false; vacio.textContent = "No se pudieron cargar los edificios. Abrí la app desde localhost."; });
