// Panel de administrador. Lo que se hace acá se vuelve a validar en el servidor (api/admin_*.php).
if (!sesion || sesion.rol !== "administrador") location.replace("inicio.html");

const $ = id => document.getElementById(id);
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));
const DIAS_C = ["", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];
const DIAS_L = ["", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado", "domingo"];
const personas = n => `${n} persona${n === 1 ? "" : "s"}`;
const hoyISO = () => { const d = new Date(); return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`; };

// Pedido al servidor: sin cuerpo = consulta, con cuerpo = cambio
async function api(url, cuerpo) {
  const opciones = cuerpo === undefined
    ? { cache: "no-store" }
    : { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(cuerpo) };
  const r = await fetch(url, opciones);
  const d = await r.json().catch(() => ({}));
  if (r.status === 401) { irAlLogin(); throw new Error("sin sesión"); }
  if (!r.ok) { const e = new Error(d.error || "No se pudo completar la acción."); e.estado = r.status; e.datos = d; throw e; }
  return d;
}

function avisar(texto, esError) {
  const e = $("estado");
  e.textContent = texto;
  e.className = esError ? "error" : "exito";
  e.hidden = false;
  clearTimeout(avisar.t);
  avisar.t = setTimeout(() => { e.hidden = true; }, 8000);
  window.scrollTo({ top: 0, behavior: "smooth" });
}
const fallo = e => { if (e.message !== "sin sesión") avisar(e.message, true); };

function mostrarConflictos(caja, lista) {
  caja.innerHTML = `<b>Hay clases en ese horario:</b><ul>${lista.map(x => `<li>${esc(x)}</li>`).join("")}</ul>Si querés guardarlo igual, tocá "Guardar igual".`;
  caja.hidden = false;
}

// ---- Aulas (para los desplegables) ----
let aulasLista = [];
async function asegurarAulas() { if (!aulasLista.length) aulasLista = await api("api/admin_aulas.php"); }
function opcionesAulas(elegida) {
  const porEdificio = {};
  aulasLista.forEach(a => (porEdificio[a.edificio] = porEdificio[a.edificio] || []).push(a));
  return Object.entries(porEdificio).map(([ed, aulas]) =>
    `<optgroup label="${esc(ed)}">${aulas.map(a => `<option value="${esc(a.id)}"${a.id === elegida ? " selected" : ""}>${esc(a.edificio)} · ${esc(a.etiqueta)}</option>`).join("")}</optgroup>`).join("");
}

// ---- Pestañas ----
const cargadores = { clases: cargarExcepciones, reservas: cargarReservas, fechas: cargarFechas, historial: cargarHistorial };
function mostrar(nombre) {
  document.querySelectorAll("[data-tab]").forEach(b => b.setAttribute("aria-pressed", b.dataset.tab === nombre));
  Object.keys(cargadores).forEach(n => { $("tab-" + n).hidden = n !== nombre; });
  cargadores[nombre]().catch(fallo);
}
document.querySelectorAll("[data-tab]").forEach(b => b.addEventListener("click", () => mostrar(b.dataset.tab)));
$("salir").addEventListener("click", cerrarSesion);

// =====================  CLASES  =====================
let hallados = [], actual = null, forzar = false, consulta = 0;
const modo = { accion: "cambiar", alcance: "todas" };

function tarjeta(m, i) {
  return `<li class="curso"><div class="datos">
    <div class="cuando"><b>${DIAS_C[m.dia]}</b> ${m.inicio} - ${m.fin} &nbsp;|&nbsp; <b>${esc(m.edificio)}</b> ${esc(m.aula)}</div>
    <h3>${esc(m.materia)}</h3>
    <div class="docente">${esc([m.docente, m.comision].filter(Boolean).join(" | "))}</div>
    <span class="carrera">${esc(m.carrera)}</span>
  </div><div class="acciones"><button class="mini" type="button" data-i="${i}">Modificar</button></div></li>`;
}

async function buscar(q) {
  const mia = ++consulta;
  if (q.length < 2) { $("sec-res").hidden = true; $("sin-res").hidden = true; return; }
  const lista = await api("api/admin_clases.php?q=" + encodeURIComponent(q));
  if (mia !== consulta) return;   // llegó tarde: ya escribieron otra cosa
  hallados = lista;
  $("res").innerHTML = lista.map(tarjeta).join("");
  $("sec-res").hidden = !lista.length;
  $("sin-res").hidden = lista.length > 0;
}
let espera = null;
$("q").addEventListener("input", e => { clearTimeout(espera); espera = setTimeout(() => buscar(e.target.value.trim()).catch(fallo), 250); });

async function cargarExcepciones() {
  const lista = await api("api/admin_clases.php?excepciones=1");
  $("exc").innerHTML = lista.length
    ? lista.map(x => `<li class="fecha aviso"><small>${esc(x.cuando)}</small><strong>${esc(x.texto)}</strong><span class="detalle">${esc(x.detalle)}</span>
        <div class="acciones"><button class="mini" type="button" data-quitar="${x.ids.join(",")}">Quitar</button></div></li>`).join("")
    : '<li class="vacio">No hay cambios por fecha programados.</li>';
}
$("exc").addEventListener("click", async e => {
  const b = e.target.closest("[data-quitar]");
  if (!b || !confirm("¿Quitar este cambio? Vuelve el horario de siempre y se avisa a quienes siguen la clase.")) return;
  try {
    const d = await api("api/admin_clases.php", { accion: "quitar_excepcion", ids: b.dataset.quitar.split(",").map(Number), avisar: true });
    avisar(`Listo. Avisamos a ${personas(d.avisados)}.`);
    cargarExcepciones();
  } catch (err) { fallo(err); }
});

$("res").addEventListener("click", async e => {
  const b = e.target.closest("[data-i]");
  if (!b) return;
  try { await asegurarAulas(); abrirEditor(hallados[Number(b.dataset.i)]); } catch (err) { fallo(err); }
});

function limpiarConflictos() { forzar = false; $("ed-conflictos").hidden = true; actualizarEditor(); }

function abrirEditor(m) {
  actual = m;
  modo.accion = "cambiar"; modo.alcance = "todas"; forzar = false;
  $("ed-titulo").textContent = m.materia + (m.comision ? " · " + m.comision : "");
  $("ed-actual").textContent = `Ahora: ${DIAS_C[m.dia]} ${m.inicio} a ${m.fin} · ${m.edificio} ${m.aula}`;
  $("ed-aula").innerHTML = opcionesAulas(m.aulaId);
  $("ed-dia").value = String(m.dia);
  $("ed-inicio").value = m.inicio;
  $("ed-fin").value = m.fin;
  $("ed-fecha").value = "";
  $("ed-fecha").min = hoyISO();
  $("ed-fecha-ayuda").textContent = `Esta clase es los ${DIAS_L[m.dia]}.`;
  $("ed-motivo").value = "";
  $("ed-avisar").checked = true;
  $("ed-error").textContent = "";
  $("ed-conflictos").hidden = true;
  $("vista-lista").hidden = true;
  $("editor").hidden = false;
  actualizarEditor();
  window.scrollTo(0, 0);
}

function actualizarEditor() {
  document.querySelectorAll("[data-accion]").forEach(b => b.setAttribute("aria-pressed", b.dataset.accion === modo.accion));
  document.querySelectorAll("[data-alcance]").forEach(b => b.setAttribute("aria-pressed", b.dataset.alcance === modo.alcance));
  const cambia = modo.accion === "cambiar", unDia = modo.alcance === "fecha";
  $("ed-datos").hidden = !cambia;
  $("ed-dia-g").hidden = !(cambia && !unDia);
  $("ed-fecha-g").hidden = !unDia;
  $("ed-aviso-baja").hidden = !(!cambia && !unDia);
  $("ed-guardar").textContent = forzar ? "Guardar igual" : cambia ? "Guardar cambios" : unDia ? "Cancelar ese día" : "Eliminar la clase";
}
document.querySelectorAll("[data-accion]").forEach(b => b.addEventListener("click", () => { modo.accion = b.dataset.accion; limpiarConflictos(); }));
document.querySelectorAll("[data-alcance]").forEach(b => b.addEventListener("click", () => { modo.alcance = b.dataset.alcance; limpiarConflictos(); }));
$("editor").addEventListener("input", () => { if (forzar) limpiarConflictos(); });

function cerrarEditor() {
  actual = null;
  $("editor").hidden = true;
  $("vista-lista").hidden = false;
}
$("ed-volver").addEventListener("click", cerrarEditor);

$("editor").addEventListener("submit", async e => {
  e.preventDefault();
  if (!actual) return;
  $("ed-error").textContent = "";
  const base = { ids: actual.ids, motivo: $("ed-motivo").value.trim(), avisar: $("ed-avisar").checked, forzar };
  const aula = $("ed-aula").value, inicio = $("ed-inicio").value, fin = $("ed-fin").value, fecha = $("ed-fecha").value;
  let carga;
  if (modo.alcance === "fecha") {
    if (!fecha) { $("ed-error").textContent = "Elegí la fecha."; return; }
    carga = { ...base, accion: "excepcion", tipo: modo.accion === "cancelar" ? "cancelada" : "movida", fecha, aula, inicio, fin };
  } else if (modo.accion === "cambiar") {
    carga = { ...base, accion: "cambiar", aula, dia: Number($("ed-dia").value), inicio, fin };
  } else {
    if (!confirm(`¿Eliminar "${actual.materia}" definitivamente? Esto no se puede deshacer.`)) return;
    carga = { ...base, accion: "eliminar" };
  }
  $("ed-guardar").disabled = true;
  try {
    const d = await api("api/admin_clases.php", carga);
    cerrarEditor();
    avisar(d.avisados ? `Listo. Avisamos a ${personas(d.avisados)}.` : "Listo. No había nadie siguiendo esta clase, así que no se envió ningún aviso.");
    cargarExcepciones().catch(fallo);
    buscar($("q").value.trim()).catch(fallo);
  } catch (err) {
    if (err.estado === 409 && err.datos && err.datos.conflictos) { forzar = true; mostrarConflictos($("ed-conflictos"), err.datos.conflictos); actualizarEditor(); }
    else if (err.message !== "sin sesión") $("ed-error").textContent = err.message;
  } finally { $("ed-guardar").disabled = false; }
});

// =====================  RESERVAS  =====================
let forzarReserva = false;
async function cargarReservas() {
  await asegurarAulas();
  if (!$("rs-aula").options.length) $("rs-aula").innerHTML = opcionesAulas("");
  $("rs-fecha").min = hoyISO();
  const lista = await api("api/admin_reservas.php");
  $("rs-lista").innerHTML = lista.length
    ? lista.map(r => `<li class="fecha aviso"><small>${esc(r.cuando)}</small><strong>${esc(r.titulo)}</strong>
        <span class="detalle">${esc(r.lugar)}${r.organizador ? " · " + esc(r.organizador) : ""}</span>
        <div class="acciones"><button class="mini peligro" type="button" data-borrar="${r.id}">Borrar</button></div></li>`).join("")
    : '<li class="vacio">No hay reservas próximas.</li>';
}
$("form-reserva").addEventListener("input", () => { if (forzarReserva) { forzarReserva = false; $("rs-conflictos").hidden = true; $("rs-guardar").textContent = "Reservar aula"; } });
$("form-reserva").addEventListener("submit", async e => {
  e.preventDefault();
  $("rs-error").textContent = "";
  $("rs-guardar").disabled = true;
  try {
    await api("api/admin_reservas.php", {
      accion: "crear", aula: $("rs-aula").value, fecha: $("rs-fecha").value, inicio: $("rs-inicio").value, fin: $("rs-fin").value,
      titulo: $("rs-titulo").value.trim(), organizador: $("rs-org").value.trim(), forzar: forzarReserva
    });
    forzarReserva = false;
    $("rs-conflictos").hidden = true;
    $("rs-guardar").textContent = "Reservar aula";
    ["rs-fecha", "rs-inicio", "rs-fin", "rs-titulo", "rs-org"].forEach(id => { $(id).value = ""; });
    avisar("Listo. El aula quedó reservada y se ve como evento en la app.");
    cargarReservas().catch(fallo);
  } catch (err) {
    if (err.estado === 409 && err.datos && err.datos.conflictos) { forzarReserva = true; mostrarConflictos($("rs-conflictos"), err.datos.conflictos); $("rs-guardar").textContent = "Guardar igual"; }
    else if (err.message !== "sin sesión") $("rs-error").textContent = err.message;
  } finally { $("rs-guardar").disabled = false; }
});
$("rs-lista").addEventListener("click", async e => {
  const b = e.target.closest("[data-borrar]");
  if (!b || !confirm("¿Borrar esta reserva?")) return;
  try { await api("api/admin_reservas.php", { accion: "borrar", id: Number(b.dataset.borrar) }); avisar("Reserva borrada."); cargarReservas().catch(fallo); }
  catch (err) { fallo(err); }
});

// =====================  FECHAS DEL CALENDARIO  =====================
async function cargarFechas() {
  const lista = await api("api/admin_fechas.php");
  $("fe-lista").innerHTML = lista.length
    ? lista.map(f => `<li class="fecha aviso"><small>${esc(f.cuando)}</small><strong>${esc(f.titulo)}</strong>
        <div class="acciones"><button class="mini peligro" type="button" data-borrar="${f.id}">Borrar</button></div></li>`).join("")
    : '<li class="vacio">No hay fechas próximas.</li>';
}
$("form-fecha").addEventListener("submit", async e => {
  e.preventDefault();
  $("fe-error").textContent = "";
  try {
    await api("api/admin_fechas.php", { accion: "crear", titulo: $("fe-titulo").value.trim(), fecha: $("fe-fecha").value, fin: $("fe-fin").value });
    ["fe-titulo", "fe-fecha", "fe-fin"].forEach(id => { $(id).value = ""; });
    avisar("Listo. La fecha ya figura en el calendario.");
    cargarFechas().catch(fallo);
  } catch (err) { if (err.message !== "sin sesión") $("fe-error").textContent = err.message; }
});
$("fe-lista").addEventListener("click", async e => {
  const b = e.target.closest("[data-borrar]");
  if (!b || !confirm("¿Sacar esta fecha del calendario?")) return;
  try { await api("api/admin_fechas.php", { accion: "borrar", id: Number(b.dataset.borrar) }); avisar("Fecha borrada."); cargarFechas().catch(fallo); }
  catch (err) { fallo(err); }
});

// =====================  HISTORIAL  =====================
async function cargarHistorial() {
  const lista = await api("api/admin_historial.php");
  $("hi-lista").innerHTML = lista.length
    ? lista.map(h => `<li class="fecha aviso"><small>${esc(h.cuando)} · ${esc(h.quien || "—")}</small><strong>${esc(h.detalle)}</strong></li>`).join("")
    : '<li class="vacio">Todavía no se hizo ningún cambio.</li>';
}

mostrar("clases");
